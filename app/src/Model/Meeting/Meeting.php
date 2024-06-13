<?php

namespace LetsCo\Model\Meeting;

use LetsCo\Admin\Meeting\MeetingAdmin;
use LetsCo\Model\Event;
use LetsCo\Model\Program;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;

class Meeting extends Event
{
    private static $table_name = 'Letsco_Meeting';
    private static string $cms_edit_owner = MeetingAdmin::class;
    private static $db = [
        'Date' => 'Date',
        'Time' => 'Time',
        'Limit' => 'Int',
    ];

    private static $many_many = [
        'Images' => Image::class,
        'Documents' => File::class,
    ];
    private static $has_one = [
        'Programs' => Program::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

    }
}
