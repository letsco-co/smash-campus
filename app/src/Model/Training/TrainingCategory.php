<?php

namespace LetsCo\Model\Training;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

class TrainingCategory extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingCategory';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_one = [
        'Image' => Image::class,
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
