<?php

namespace LetsCo\Middleware;

use LetsCo\PageType\MaintenancePage;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;
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
        if ($currentPage instanceof MaintenancePage) {
            return $response;
        }


        $utilityPage = MaintenancePage::get()->first();
        if (!$utilityPage) {
            return $response;
        }

        $response =  HTTPResponse::create();
        return $response->redirect($utilityPage->AbsoluteLink());
    }
}
