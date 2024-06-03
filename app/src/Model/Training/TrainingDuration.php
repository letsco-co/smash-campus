<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

class TrainingDuration extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingDuration';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Title',
        ]));
        return $validator;
    }
}
