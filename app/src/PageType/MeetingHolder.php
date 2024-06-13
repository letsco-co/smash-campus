<?php

namespace LetsCo\PageType;

use LetsCo\Model\Meeting\Meeting;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;

class MeetingHolder extends \Page
{
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
}
