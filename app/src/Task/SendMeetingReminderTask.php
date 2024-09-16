<?php

namespace LetsCo\Task;

use Brevo\Client\ApiException;
use DateTime;
use LetsCo\Email\DefaultEmailProvider;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\MeetingRegistration;
use SilverStripe\Control\Director;
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
            $this->sendReminders(7, $email);
            $this->sendReminders(3, $email);
        }
    }

    public function getMeetingRegistrationsForMeetingHappeningThatDay(string $date): DataList
    {
        return MeetingRegistration::get()->filter("Meeting.Date", $date);
    }

    public function getDateInXDays(int $daysAhead, DateTime $dateTime) : string
    {
        $dateTime->modify("+$daysAhead days ");
        return $dateTime->format("Y-m-d");
    }

    /**
     * @param int $daysAhead
     * @param EmailProvider $email
     * @return void
     */
    public function sendReminders(int $daysAhead, EmailProvider $email): void
    {
        $date = $this->getDateInXDays($daysAhead, new DateTime());
        $meetingRegistrations = $this->getMeetingRegistrationsForMeetingHappeningThatDay($date)->filter('Status', MeetingRegistration::STATUS_ACCEPTED);
        foreach ($meetingRegistrations as $meetingRegistration) {
            try {
                $email->send(
                    [["email" => $meetingRegistration->Email]],
                    Environment::getEnv('BREVO_MEETING_REMINDER_TEMPLATE_ID'),
                    $this->getParams($meetingRegistration, $daysAhead)
                );
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

    /**
     * @param $meetingRegistration
     * @param int $daysAhead
     * @return array
     * @throws \Exception
     */
    public function getParams($meetingRegistration, int $daysAhead): array
    {
        $meeting = $meetingRegistration->Meeting();
        $meetingDate = new DateTime($meeting->Date);
        return [
            'Nom' => $meetingRegistration->FirstName . ' ' . $meetingRegistration->LastName,
            'Conference' => [
                "Nom" => $meeting->Title,
                "Date" => $meetingDate->format("d/m/Y"),
                'Heure' => $meeting->Time,
                'Lieu' => $meeting->Address,
                'Lien' => Director::absoluteURL((string)$meeting->Link()),
            ],
            "Jours" => $daysAhead,
        ];
    }
}
