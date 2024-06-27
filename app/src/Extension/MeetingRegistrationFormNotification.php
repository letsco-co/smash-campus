<?php

namespace LetsCo\Extension;

use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\Meeting;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;

class MeetingRegistrationFormNotification extends Extension
{
    private EmailProvider $emailProvider;
    public function notificationConstructor()
    {
        $this->emailProvider =  Injector::inst()->create(DefaultEmailProvider::class);
    }

    private function sendValidationEmail(string $completionStep, $data, Meeting $meeting)
    {
        $this->emailProvider->getOrCreateContact($data['Email']);
        $this->emailProvider->addContactToList($meeting->ListId, $data['Email']);
        $this->emailProvider->addContactToList(Environment::getEnv('BREVO_NEWSLETTER_LIST_ID'), $data['Email']);
        $name = $data['FirstName'] . ' '. $data['LastName'];
        $to = [['name' => $name, 'email' => $data['Email']]];
        $templateId = Environment::getEnv('BREVO_MEETING_TEMPLATE_ID');
        $params = [
            "Name" => $name,
            "Conference" => [
                'Nom' => $meeting->Title,
                'Date' => $meeting->Date,
                'Heure' => $meeting->Time,
                'Lien' => Director::absoluteURL((string) $meeting->Link()),
            ],
            "IsInWaitingList" => $completionStep == "WaitingList",
        ];
        try {
            $this->emailProvider->send($to, $templateId, $params);
        } catch (\Exception $e) {
            Injector::inst()->get(LoggerInterface::class)->error($e);
        }
    }

    public function updateDoSaveMeetingNotification(&$completionStep, &$data, &$meeting)
    {
        $this->sendValidationEmail($completionStep, $data, $meeting);
    }
}
