<?php

namespace LetsCo\Model\Meeting;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class MeetingRegistration extends DataObject
{
    const STATUS_ACCEPTED = 'Accepted';
    const STATUS_WAITING = 'Waiting';
    const STATUS_CANCELLED = 'Cancelled';
    use LocalizationDataObject;
    private static $table_name = 'Letsco_MeetingRegistration';
    private static $db = [
        'LastName' => 'Varchar(255)',
        'Status' => 'Enum("Waiting, Accepted, Cancelled","Waiting")',
        'FirstName' => 'Varchar(255)',
        'Fonction' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'StructureName' => 'Varchar(255)',
        'AcceptRGPD' => 'Boolean',
        'AcceptOtherInfos' => 'Boolean',
    ];
    private static $has_one = [
        'Meeting' => Meeting::class,
    ];

    private static $summary_fields = [
        'LastName',
        'FirstName',
        'Email',
        'Status',
    ];
    public function getTitle()
    {
        return $this->LastName . " ". $this->FirstName;
    }
    public function getCMSFields()
    {
        $fields =  parent::getCMSFields();
        $statusField = $fields->dataFieldByName('Status');
        $statusField->setSource(self::getTranslatableEnumValues($this->dbObject('Status')->enumValues()));
        return $fields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'LastName',
            'FirstName',
            'Email',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ]));
        return $validator;
    }
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\MeetingAdmin', 'any', $member);
    }

}
