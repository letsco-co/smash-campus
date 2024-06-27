<?php

namespace LetsCo\Extension;

use Brevo\Client\ApiException;
use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\SiteConfig\SiteConfig;

class NotifyAdminExtension extends Extension
{
    private EmailProvider $emailProvider;
    public function notificationConstructor()
    {
        $this->emailProvider =  Injector::inst()->create(DefaultEmailProvider::class);
    }
    public function notifyAdmin(&$data)
    {
        $siteConfig = SiteConfig::current_site_config();
        $email = [['email' => $siteConfig->Email ?? 'support@letsco.ovh']];
        try {
            $this->emailProvider->send($email, Environment::getEnv('BREVO_ADMIN_TEMPLATE_ID'), $data);
        } catch (ApiException $exception) {
            user_error(json_encode([$exception->getMessage(), $email]), E_USER_ERROR);
        }
    }
}
