<?php

namespace tests;

use LetsCo\Task\SendMeetingReminderTask;
use SilverStripe\Dev\SapphireTest;

class SendMeetingReminderTaskTest extends SapphireTest
{
    protected static $fixture_file = 'SendMeetingReminderTaskTest.yml';

    protected SendMeetingReminderTask $reminderTask;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reminderTask = new SendMeetingReminderTask();
    }

    public function testGetNoMeetingRegistrationWhenNoneThatDay()
    {
        $meetings = $this->reminderTask->getMeetingRegistrationsForMeetingHappeningThatDay('2024-06-30');
        $this->assertCount(0,$meetings);
    }

    public function testGet1MeetingRegistrationWhen1ThatDay()
    {
        $meetings = $this->reminderTask->getMeetingRegistrationsForMeetingHappeningThatDay('2024-06-28');
        $this->assertCount(1,$meetings);
    }

    public function testGet2MeetingRegistrationsWhen2ThatDay()
    {
        $meetings = $this->reminderTask->getMeetingRegistrationsForMeetingHappeningThatDay('2024-06-05');
        $this->assertCount(2,$meetings);
    }

    public function testGet20240624WhenIn2DaysAndBaseDateIs20240622()
    {
        $date = $this->reminderTask->getDateInXDays(2, new \DateTime("2024-06-22"));
        $this->assertEquals("2024-06-24", $date);
    }

    public function testGet20240628WhenIn7DaysAndBaseDateIs20240621()
    {
        $date = $this->reminderTask->getDateInXDays(7, new \DateTime("2024-06-21"));
        $this->assertEquals("2024-06-28", $date);
    }
}
