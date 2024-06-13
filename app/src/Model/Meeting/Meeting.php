<?php

namespace LetsCo\Model\Meeting;

use LetsCo\Admin\Meeting\MeetingAdmin;
use LetsCo\PageType\MeetingHolder;
use LetsCo\Trait\LocalizationDataObject;
use LetsCo\Model\Event;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\LiteralField;

class Meeting extends Event
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Meeting';
    private static string $cms_edit_owner = MeetingAdmin::class;
    private static $db = [
        'Date' => 'Date',
        'Time' => 'Time',
        'Limit' => 'Int',
    ];
    private static $has_one = [
        'Image' => Image::class,
        'Page' => MeetingHolder::class,
    ];
    private static $many_many = [
        'Images' => Image::class,
        'Documents' => File::class,
    ];
    private static $summary_fields = [
        'Title'
    ];
    private static $owns = [
        'Image',
        'Images',
        'Documents',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('URLSegment');
        $fields->removeByName('Images');
        $fields->removeByName('Documents');
        $imageField = $fields->dataFieldByName('Image');
        $imageField->setFolderName('Meeting');
        $imageField->setTitle(_t(self::class.'.Image', 'Image'));
        $fields->removeByName('Programs');
        $fields->push(LiteralField::create('Programs_Title' . 'Title', '<label >' . _t(self::class . '.' . 'Programs', 'Programs') . '</label>'));
        $manyManyConfig = GridFieldConfig_RelationEditor::create();
        $manyManyConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $fields->push(CompositeField::create(new FieldList([
            GridField::create('Programs', false, $this->Programs(), $manyManyConfig),
        ])));
        $fields->push($imagesField = UploadField::create('Images', _t(self::class.'.Images', 'Images')));
        $imagesField->setAllowedExtensions(['png', 'jpg', 'jpeg', 'webp']);
        $imagesField->setFolderName('Meeting');
        $fields->push($fileField = UploadField::create('Documents', _t(self::class.'.Documents', 'Documents')));
        $fileField->setFolderName('Meeting');
        return $fields;
    }

    public function Link($action = null)
    {
        $link = $this->Page()->Link($this->URLSegment.'/'.$action);
        $this->extend('updateLink', $link);
        return $link;
    }
}
