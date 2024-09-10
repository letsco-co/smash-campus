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

    public function testGetNoMeetingWhenNoneThatDay()
    {
        $meetings = $this->reminderTask->getMeetingRegistrationsForMeetingHappeningThatDay('2024-06-30');
        $this->assertCount(0,$meetings);
    }
}
