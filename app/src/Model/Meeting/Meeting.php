<?php

namespace LetsCo\Model\Meeting;

use LetsCo\Admin\Meeting\MeetingAdmin;
use LetsCo\Meeting\MeetingSpeaker;
use LetsCo\PageType\MeetingHolder;
use LetsCo\Trait\LocalizationDataObject;
use LetsCo\Model\Event;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\SearchableMultiDropdownField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\View\Requirements;

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
        'Speakers' => MeetingSpeaker::class,
    ];
    private static $has_many = [
        'Registrations' => MeetingRegistration::class,
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
        $fields->removeByName('Speakers');
        $imageField = $fields->dataFieldByName('Image');
        $imageField->setFolderName('Meeting');
        $imageField->setTitle(_t(self::class.'.Image', 'Image'));
        $fields->removeByName('Programs');
        $mainTab = $fields->findTab('Root.Main');


        $speakerField = SearchableMultiDropdownField::create('Speakers',  _t(self::class . '.Speakers', 'Speakers'), MeetingSpeaker::get());
        $mainTab->push($speakerField);

        $config = GridFieldConfig_RecordEditor::create();
        $config->removeComponentsByType(GridField_ActionMenu::class);
        $paginator = $config->getComponentByType(GridFieldPaginator::class);
        $paginator->setItemsPerPage(5);
        $gridField = GridField::create('Manage_Speakers', false, MeetingSpeaker::get(), $config);
        $toggleField = ToggleCompositeField::create(
            'ManageSpeakers',
            _t(self::class . '.MANAGE_SPEAKERS', 'Manage Speakers'),
            new FieldList($gridField));
        $mainTab->push($toggleField);

        Requirements::customCSS("
            .ss-toggle .ui-accordion-content {
                padding : 2.2em !important;
            }
        ");

        $mainTab->push(LiteralField::create('Programs_Title' . 'Title', '<label >' . _t(self::class . '.' . 'Programs', 'Programs') . '</label>'));
        $manyManyConfig = GridFieldConfig_RelationEditor::create();
        $manyManyConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $mainTab->push(CompositeField::create(new FieldList([
            GridField::create('Programs', false, $this->Programs(), $manyManyConfig),
        ])));
        $mainTab->push($imagesField = UploadField::create('Images', _t(self::class.'.Images', 'Images')));
        $imagesField->setAllowedExtensions(['png', 'jpg', 'jpeg', 'webp']);
        $imagesField->setFolderName('Meeting');
        $mainTab->push($fileField = UploadField::create('Documents', _t(self::class.'.Documents', 'Documents')));
        $fileField->setFolderName('Meeting');

        return $fields;
    }

    public function Link($action = null)
    {
        $link = $this->Page()->Link($this->URLSegment.'/'.$action);
        $this->extend('updateLink', $link);
        return $link;
    }

    public function otherMeetings($limit = 2)
    {
        $meetings = Meeting::get()->exclude('ID', $this->ID);
        if ($limit) $meetings = $meetings->limit($limit);
        return $meetings;
    }

    public function remainingSeats()
    {
        return $this->Limit - $this->Registrations()->filter('Status', MeetingRegistration::STATUS_ACCEPTED)->count();
    }
}
