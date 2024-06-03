<?php

namespace LetsCo\Model;

use LetsCo\Model\Training\Training;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Hierarchy\Hierarchy;

class Program extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Program';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_one = [
        'Training' => Training::class,
    ];
    private static $summary_fields = [
        'Title',
        'Parent.Title'
    ];
    private static $extensions = [
        Hierarchy::class,
    ];

    public function summaryFields()
    {
        $defaultSummaryFields = parent::summaryFields();
        $summaryFields = [];
        foreach ($defaultSummaryFields as $summaryFieldKey => $summaryFieldData) {
            if (str_contains($summaryFieldKey, '.')) {
                $summaryFields[$summaryFieldKey] = _t(
                    self::class.'.'. str_replace('.', '_', $summaryFieldKey),
                    str_replace('.', ' ', $summaryFieldKey)
                );
                continue;
            }
            $summaryFields[$summaryFieldKey] = $summaryFieldData;
        }
        return $summaryFields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Title',
        ]));
        return $validator;
    }
}
