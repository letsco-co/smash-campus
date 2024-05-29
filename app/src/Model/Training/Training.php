<?php

namespace LetsCo\Model;

use LetsCo\Admin\TrainingAdmin;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\ORM\DataObject;
use SilverStripe\TagField\TagField;

class Training extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Training';
    private static string $cms_edit_owner = TrainingAdmin::class;
    private static $db = [
        'Title' => 'Varchar(255)',
        'Goals' => 'HTMLText',
        'Modalities' => 'HTMLText',
        'Accessibility' => 'HTMLText',
        'Financing' => 'HTMLText',
        'Address' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Duration' => TrainingDuration::class,
        'Category' => TrainingCategory::class,
        'Qualification' => TrainingQualification::class,
        'Mode' => TrainingMode::class,
    ];

    private static $many_many = [
        'Prerequisites' => TrainingPrerequisite::class,
        'Publics' => TrainingPublic::class,
        'EducationalMeans' => TrainingEducationalMeans::class,
        'EvaluationMethod' => TrainingEvaluationMethod::class,
        'ExecutionMonitoring' => TrainingExecutionMonitoring::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $hasOneRelations = $this->hasOne();
        foreach ($hasOneRelations as $hasOneRelationKey => $hasOneRelationClassName) {
            $newRelationField = TagField::create(
                $hasOneRelationKey.'ID',
                $this->fieldLabel($hasOneRelationKey),
                $hasOneRelationClassName::get())
            ->setIsMultiple(false)
            ->setCanCreate(true);
            $fields->replaceField($hasOneRelationKey.'ID', $newRelationField);
        }
        return $fields;
    }
}
