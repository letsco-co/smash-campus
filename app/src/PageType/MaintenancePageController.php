<?php

namespace LetsCo\PageType;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

class MaintenancePageController extends \PageController
{
    public function index(HTTPRequest $request)
    {
        $config = SiteConfig::current_site_config();

        if (!$config->IsInMaintenanceMode && !Security::getCurrentUser() && !Permission::check('CMS_ACCESS', 'any', Security::getCurrentUser())) {
            return $this->redirect(BASE_URL);
        }

        $this->response->setStatusCode(MaintenancePage::RESPONSE_CODE);
        return $this->renderWith('MaintenanceModePage');
    }
}
