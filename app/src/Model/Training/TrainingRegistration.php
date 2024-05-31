<?php

namespace LetsCo\Model;

use LetsCo\FormField\FrenchPhoneNumberField;
use LetsCo\FormField\FrenchPostCodeField;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataObject;

class TrainingRegistration extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingRegistration';
    private static $db = [
        'PersonTitle' => 'Enum("Mr,Ms","Mr")',
        'LastName' => 'Varchar(255)',
        'FirstName' => 'Varchar(255)',
        'Fonction' => 'Varchar(255)',
        'ProAddress' => 'Varchar(255)',
        'PhoneNumber' => 'Int',
        'Email' => 'Varchar(255)',
        'StructureName' => 'Varchar(255)',
        'StructureActivity' => 'Varchar(255)',
        'StructureAddress' => 'Varchar(255)',
        'StructurePostCode' => 'Int',
        'StructureCity' => 'Varchar(255)',
        'ManagerLastName' => 'Varchar(255)',
        'ManagerFirstName' => 'Varchar(255)',
        'ManagerEmail' => 'Varchar(255)',
        'ManagerFonction' => 'Varchar(255)',
        'ManagerPhoneNumber' => 'Varchar(255)',
        'DesiredTrainingDate' => 'Varchar(255)',
        'Reasons' => 'Text',
        'Financing' => 'MultiEnum("Structure, OPCO, PublicFinancing, Individual", "Structure")',
        'IsDisabled' => 'Enum("No,Yes","No")',
        'HeardOfTrainingSource' => 'MultiEnum("Web, SocialMedia, WordOfMouth", "Web")',
        'AcceptRGPD' => 'Boolean',
        'AcceptOtherInfos' => 'Boolean',
    ];

    private static $has_one = [
        'Training' => Training::class,
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

    public function getCMSFields()
    {
        $fields = FieldList::create([
            ToggleCompositeField::create('PersonalInfos',_t(self::class.'.PersonalInfos', 'Personal infos'), new FieldList(
                DropdownField::create('PersonTitle', _t(self::class.'.PersonTitle', 'PersonTitle'), $this->getTranslatableEnumValues($this->dbObject('PersonTitle')->enumValues())),
                TextField::create('LastName', _t(self::class.'.LastName', 'LastName')),
                TextField::create('FirstName', _t(self::class.'.FirstName', 'FirstName')),
                TextField::create('Fonction', _t(self::class.'.Fonction', 'Fonction')),
                TextField::create('ProAddress', _t(self::class.'.ProAddress', 'Pro address')),
                FrenchPhoneNumberField::create('PhoneNumber', _t(self::class.'.PhoneNumber', 'Phone number')),
                EmailField::create('Email', _t(self::class.'.Email', 'Email')),)
            )->setStartClosed(false),
            ToggleCompositeField::create('Structure', _t(self::class.'.StructureInfos', 'Structure'), new FieldList(
                TextField::create('StructureName', _t(self::class.'.StructureName', 'StructureName')),
                TextField::create('StructureActivity', _t(self::class.'.StructureActivity', 'StructureActivity')),
                TextField::create('StructureAddress', _t(self::class.'.StructureAddress', 'StructureAddress')),
                FrenchPostCodeField::create('StructurePostCode', _t(self::class.'.StructurePostCode', 'StructurePostCode')),
                TextField::create('StructureCity', _t(self::class.'.StructureCity', 'StructureCity')),
            )),
            ToggleCompositeField::create('Manager', _t(self::class.'.Manager', 'Manager'), new FieldList(
                TextField::create('ManagerLastName', _t(self::class.'.ManagerLastName', 'ManagerLastName')),
                TextField::create('ManagerFirstName', _t(self::class.'.ManagerFirstName', 'ManagerFirstName')),
                EmailField::create('ManagerEmail', _t(self::class.'.ManagerEmail', 'ManagerEmail')),
                TextField::create('ManagerFonction', _t(self::class.'.ManagerFonction', 'ManagerFonction')),
                FrenchPhoneNumberField::create('ManagerPhoneNumber', _t(self::class.'.ManagerPhoneNumber', 'ManagerPhoneNumber')),
            )),
            ToggleCompositeField::create('Training', _t(self::class.'.TrainingInfos', 'Training Infos'), new FieldList(
                TextField::create('DesiredTrainingDate', _t(self::class.'.DesiredTrainingDate', 'DesiredTrainingDate')),
                TextareaField::create('Reasons', _t(self::class.'.Reasons', 'Reasons')),
                CheckboxSetField::create('Financing', _t(self::class.'.Financing', 'Financing'), $this->getTranslatableEnumValues($this->dbObject('Financing')->enumValues())),
                DropdownField::create('IsDisabled', _t(self::class.'.IsDisabled', 'IsDisabled'), $this->getTranslatableEnumValues($this->dbObject('IsDisabled')->enumValues())),
                CheckboxSetField::create('HeardOfTrainingSource', _t(self::class.'.HeardOfTrainingSource', 'HeardOfTrainingSource'), $this->getTranslatableEnumValues($this->dbObject('HeardOfTrainingSource')->enumValues())),
            )),
            ToggleCompositeField::create('RGPD', _t(self::class.'.RGPD', 'RGPD'), new FieldList(
                CheckboxField::create('AcceptRGPD', _t(self::class.'.AcceptRGPD', 'AcceptRGPD')),
                CheckboxField::create('AcceptOtherInfos', _t(self::class.'.AcceptOtherInfos', 'AcceptOtherInfos')),
            )),
        ]);

        $defaultFields = parent::getCMSFields();
        $trainingField = $defaultFields->dataFieldByName('TrainingID');
        $fields->insertBefore('PersonalInfos',$trainingField);
        return $fields;
    }
    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'PersonTitle',
            'LastName',
            'FirstName',
            'Fonction',
            'Email',
            'DesiredTrainingDate',
            'Financing',
            'IsDisabled',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ]));
        return $validator;
    }
}
