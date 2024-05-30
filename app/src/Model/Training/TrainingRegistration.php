<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\ORM\DataObject;

class TrainingRegistration extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingRegistration';
    private static $db = [
        'Title' => 'Enum("Mr,Ms","Mr")',
        'LastName' => 'Varchar(255)',
        'FirstName' => 'Varchar(255)',
        'Fonction' => 'Varchar(255)',
        'ProAddress' => 'Varchar(255)',
        'PhoneNumber' => 'Int',
        'Email' => 'Varchar(255)',
        'StructureName' => 'Varchar(255)',
        'StructureActivity' => 'Varchar(255)',
        'StructureAddress' => 'Varchar(255)',
        'StructurePostalCode' => 'Int',
        'StructureCity' => 'Varchar(255)',
        'ManagerLastName' => 'Varchar(255)',
        'ManagerFirstName' => 'Varchar(255)',
        'ManagerEmail' => 'Varchar(255)',
        'ManagerFonction' => 'Varchar(255)',
        'ManagerPhoneNumber' => 'Varchar(255)',
        'DesiredTrainingDates' => 'Varchar(255)',
        'Reasons' => 'Varchar(255)',
        'Financing' => 'MultiEnum("Structure, OPCO, PublicFinancing, Individual", "Structure")',
        'IsDisabled' => 'Boolean',
        'HeardOfTrainingSource' => 'MultiEnum("Web, SocialMedia, WordOfMouth", "Web")',
        'AcceptRGPD' => 'Boolean',
        'AcceptOtherInfos' => 'Boolean',
    ];

    private static $has_one = [
        'Training' => Training::class,
    ];
}
