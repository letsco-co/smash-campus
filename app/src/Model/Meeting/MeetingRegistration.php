<?php

namespace LetsCo\Meeting;

use LetsCo\Model\Meeting\Meeting;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

class MeetingRegistration extends DataObject
{
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
        'Email'
    ];
    public function getTitle()
    {
        return $this->LastName . " ". $this->FirstName;
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
}
