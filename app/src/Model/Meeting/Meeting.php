<?php

namespace LetsCo\Model\Meeting;

use DateTime;
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
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Permission;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;

class Meeting extends Event
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Meeting';
    private static string $cms_edit_owner = MeetingAdmin::class;
    private static $db = [
        'Date' => 'Date',
        'Time' => 'Time',
        'Duration' => 'Int',
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
        if (!$this->ID)
        {
            return FieldList::create(
                TextField::create('Title', _t(self::class . '.Title', 'Title')),
            );
        }
        $fields = parent::getCMSFields();
        $fields->removeByName('URLSegment');
        $fields->removeByName('Images');
        $fields->removeByName('Documents');
        $fields->removeByName('Speakers');
        $regiqtrationTab = $fields->findTab('Registrations');
        if ($regiqtrationTab) {
            $regiqtrationTab->setTitle(_t(self::class.'.Registrations', 'Registrations'));
        }
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

    public function isTodayOrFuture()
    {
        $date = $this->Date . ' ' . $this->Time;
        $givenDate = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set time to start of today
        return $givenDate >= $today;
    }

    public function getLinkTitle()
    {
        if ($this->isTodayOrFuture()) {
            return _t(self::class.'.LinkFutureMeeting', 'See the meeting');
        }
        return _t(self::class.'.LinkPastMeeting', '(Re)experience the meeting');
    }

    public function getAvailableSeats()
    {
        $remainingSeats = $this->remainingSeats();
        if (!$remainingSeats) {
            return _t(self::class . '.Full', 'Full');
        }
        if ($this->isTodayOrFuture()) {
            return  $remainingSeats . ' ' . _t(self::class.'.Remaining_Seats', 'remaining seat(s)');
        }
        return $this->Limit . ' ' . _t(self::class.'.Seats', 'seat(s)');
    }

    public function getAsideTitle($step)
    {
        if ($step) {
            $namespace = $step."_Title";
            return _t(self::class .".$namespace", $step);
        }
        $remainingSeats = $this->remainingSeats();
        if (!$remainingSeats) {
            return _t(self::class . '.RegisterOnWaitingList', 'Register on the waiting list');
        }
        if ($this->isTodayOrFuture()) {
            return _t(self::class . '.Register', 'Register to the meeting');
        }
        return _t(self::class.'.LinkPastMeeting', '(Re)experience the meeting');
    }

    public function getAsideText($step)
    {
        if (!$this->isTodayOrFuture()) {
            return new ArrayList([
                ArrayData::create([
                    'Title' => _t(self::class.'.PastMeetingAsideText', 'The conference was successfully held. Thank you to all the participants for being present and for participating enthusiastically. You can now find the documents and photos from the conference.'),
                ]),
                ArrayData::create([
                    'Title' => _t(self::class.'.PastMeetingAsideNewsletter', 'Sign up for the Newsletter to be notified of upcoming conferences'),
                ]),
            ]);
        }
        if ($step) {
            $namespace = $step."_Text";
            return new ArrayList([
                ArrayData::create([
                    'Title' => _t(self::class.".$namespace", $step),
                ]),
            ]);
        }
        return '';
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
