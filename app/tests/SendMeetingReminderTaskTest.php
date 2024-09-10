<?php

namespace tests;

use LetsCo\Task\SendMeetingReminderTask;
use SilverStripe\Dev\SapphireTest;

class SendMeetingReminderTaskTest extends SapphireTest
{
    protected static $fixture_file = 'SendMeetingReminderTaskTest.yml';


    public function testGetNoMeetingWhenNoneIn7Days()
    {
        $reminderTask = new SendMeetingReminderTask();
        $meetings = $reminderTask->getMeetingRegistrationsForMeetingHappeningThatDay('2024-06-30');
        $this->assertCount(0,$meetings);
    }
}
