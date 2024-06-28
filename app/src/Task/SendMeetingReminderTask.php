<?php

namespace LetsCo\Task;

use Brevo\Client\ApiException;
use DateTime;
use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\MeetingRegistration;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\CronTask\Interfaces\CronTask;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataList;

class SendMeetingReminderTask extends BuildTask implements CronTask
{

    /**
     * @inheritDoc
     */
    public function getSchedule()
    {
        return '0 9 * * *';
    }

    /**
     * @inheritDoc
     */
    public function process()
    {
        if (Environment::getEnv('BREVO_API_KEY')) {
            $email = Injector::inst()->create(DefaultEmailProvider::class);
            $this->sendReminder(7, $email);
            $this->sendReminder(3, $email);
        }
    }

    public function getMeetingRegistrationsForMeetingHappeningThatDay(string $date): DataList
    {
        return MeetingRegistration::get()->filter("Meeting.Date", $date);
    }

    public function getDateInXDays(int $daysAhead) : string
    {
        $dateTime = new DateTime();
        $dateTime->modify("+$daysAhead days ");
        return $dateTime->format("Y-m-d");
    }

    /**
     * @param int $daysAhead
     * @param EmailProvider $email
     * @return void
     */
    public function sendReminder(int $daysAhead, EmailProvider $email): void
    {
        $date = $this->getDateInXDays($daysAhead);
        $meetingRegistrations = $this->getMeetingRegistrationsForMeetingHappeningThatDay($date)->filter('Status', MeetingRegistration::STATUS_ACCEPTED);
        foreach ($meetingRegistrations as $meetingRegistration) {
            try {
                $meeting = $meetingRegistration->Meeting();
                $meetingDate = new DateTime($meeting->Date);
                $email->send([["email" => $meetingRegistration->Email]], Environment::getEnv('BREVO_MEETING_REMINDER_TEMPLATE_ID'), [
                    'Nom' => $meetingRegistration->FirstName . ' ' . $meetingRegistration->LastName,
                    'Conference' => [
                        "Nom" => $meetingRegistration->Meeting()->Title,
                        "Date" => $meetingDate->format("d/m/Y"),
                    ],
                    "Jours" => $daysAhead,
                ]);
            } catch (ApiException $exception) {
                user_error(json_encode([$exception->getMessage()]), E_USER_ERROR);
            }
        }
    }

    public function run($request)
    {
        if (Environment::getEnv('BREVO_API_KEY')) {
            $this->process();
        }
    }
}
