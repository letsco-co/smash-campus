<?php

namespace LetsCo\Extension;

use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Event;
use LetsCo\Model\Training\Training;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;

class EventFormNotification extends Extension
{
    private EmailProvider $emailProvider;
    public function notificationConstructor()
    {
        $this->emailProvider =  Injector::inst()->create(DefaultEmailProvider::class);
    }

    public function sendValidationEmail(&$data, Event &$event, array &$emailParams, $ics)
    {
        $this->emailProvider->getOrCreateContact($data['Email']);
        $this->emailProvider->addContactToList($event->ListId, $data['Email']);
        $this->emailProvider->addContactToList(Environment::getEnv('BREVO_NEWSLETTER_LIST_ID'), $data['Email']);
        $name = $data['FirstName'] . ' '. $data['LastName'];
        $to = [['name' => $name, 'email' => $data['Email']]];
        $templateId = Environment::getEnv('BREVO_MEETING_TEMPLATE_ID');
        if (get_class($event) == Training::class) {
            $templateId = Environment::getEnv('BREVO_TRAINING_TEMPLATE_ID');
        }
        $params = [
            "name" => $name,
        ];
        $params = array_merge($params, $emailParams);
        try {
            $this->emailProvider->send($to, $templateId, $params, $ics);
        } catch (\Exception $e) {
            user_error(json_encode([$e->getMessage()]), E_USER_ERROR);
            Injector::inst()->get(LoggerInterface::class)->error($e);
        }
    }
}
