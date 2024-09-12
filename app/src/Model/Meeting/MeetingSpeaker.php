<?php

namespace LetsCo\Meeting;

use LetsCo\Model\Event;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class MeetingSpeaker extends DataObject
{
    use LocalizationDataObject;
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

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }
}
