<?php

namespace LetsCo\PageType;

use LetsCo\Model\Meeting\Meeting;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ArrayList;

class MeetingHolder extends \Page
{
    private static $description = "Meetings page";
    private static $table_name = 'Letsco_MeetingHolder';
    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $has_many = [
        'Meetings' => Meeting::class,
    ];
    public function getCMSFields()
    {
        $fields =  parent::getCMSFields();
        $imageField = UploadField::create('Image', _t(self::class.'.Image', 'Image'));
        $imageField->setFolderName('Meeting');
        $fields->insertBefore('Content', $imageField);
        return $fields;
    }

    public function Meetings()
    {
        return Meeting::get()->where('Date >= CURDATE()');
    }

    public function getPastMeetings()
    {
        return Meeting::get()->where('Date < CURDATE()');
    }

    public function getAllMeetings()
    {
        $meetings = new ArrayList();
        $meetingsToCome = $this->Meetings();
        $pastMeetings = $this->getPastMeetings();
        $meetings->merge($meetingsToCome);
        $meetings->merge($pastMeetings);
        return $meetings;
    }
}
