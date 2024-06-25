<?php

namespace LetsCo\Middleware;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\ErrorPage\ErrorPage;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

class MaintenanceModeMiddleware implements HTTPMiddleware
{

    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);
        $config = SiteConfig::current_site_config();
        $url = $request->getURL() ?$request->getURL(): 'home';

        // If Maintenance Mode is Off, skip processing
        if (!$config->IsInMaintenanceMode) {
            return $response;
        }
        if (Security::getCurrentUser() && Permission::check('CMS_ACCESS', 'any', Security::getCurrentUser())) {
            return $response;
        }
        //Is visitor trying to hit the admin URL?  Give them a chance to log in.
        if("Security/login" == $url) {
            return $response;
        }

        $currentPage = \Page::get()->filter('URLSegment', $url)->first();
        if ($currentPage instanceof ErrorPage && $currentPage->ErrorCode == 503) {
            return $response;
        }


        $utilityPage = ErrorPage::get()->filter("ErrorCode", 503)->first();
        if (!$utilityPage) {
            return $response;
        }

        $response =  HTTPResponse::create();
        return $response->redirect($utilityPage->AbsoluteLink());
    }
}
