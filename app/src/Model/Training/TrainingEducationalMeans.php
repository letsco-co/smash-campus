<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

class TrainingEducationalMeans extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingEducationalMeans';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $belongs_many_many = [
        'Trainings' => Training::class,
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
