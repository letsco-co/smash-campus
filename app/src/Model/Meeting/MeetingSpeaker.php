<?php

namespace LetsCo\Meeting;

use LetsCo\Model\Event;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;

class MeetingSpeaker extends DataObject
{
    private static $table_name = 'Letsco_Speaker';
    use LocalizationDataObject;
    private static $db = [
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Varchar(255)',
        'Function' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Image' => Image::class,
    ];

    private static $many_many = [
        'Event' => Event::class,
    ];
    private static $owns = [
        'Image',
    ];

    private static $summary_fields = [
        'Title'
    ];
    private static $casting = [
        'Title' => 'Varchar',
    ];

    public function getTitle()
    {
        return $this->LastName . " ". $this->FirstName;
    }
}
