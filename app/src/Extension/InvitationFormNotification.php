<?php

namespace LetsCo\Extension;

use Brevo\Client\ApiException;
use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\Meeting;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;

class InvitationFormNotification extends Extension
{
    private EmailProvider $emailProvider;
    public function notificationConstructor()
    {
        $this->emailProvider =  Injector::inst()->create(DefaultEmailProvider::class);
    }

    public function sendValidationEmail(&$data, array &$emailParams)
    {
        $name = $data['FirstName'] . ' '. $data['LastName'];
        $to = [['name' => $name, 'email' => $data['Email']]];
        $templateId = Environment::getEnv('BREVO_MEETING_INVITATION_TEMPLATE_ID');
        $params = [
            "name" => $name,
        ];
        $params = array_merge($params, $emailParams);
        try {
            $this->emailProvider->send($to, $templateId, $params);
        } catch (APIException $e) {
            user_error(json_encode([$e->getMessage()]),E_USER_ERROR);
        }
    }
}
