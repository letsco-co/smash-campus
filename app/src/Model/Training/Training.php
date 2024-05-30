<?php

namespace LetsCo\Model;

use LetsCo\Admin\TrainingAdmin;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\SearchableDropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;

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
        $fields = FieldList::create(
            TextField::create('Title', _t(self::class.'.Title', 'Title')),
            SearchableDropdownField::create('CategoryID', _t(self::class.'.Category', 'Category'), TrainingCategory::get()),
            SearchableDropdownField::create('DurationID', _t(self::class.'.Duration', 'Duration'), TrainingDuration::get()),
            SearchableDropdownField::create('QualificationID', _t(self::class.'.Qualification', 'Qualification'), TrainingQualification::get()),
            SearchableDropdownField::create('ModeID', _t(self::class.'.Mode', 'Mode'), TrainingMode::get()),
            TextField::create('Address', _t(self::class.'.Address', 'Address')),
            HTMLEditorField::create('Goals', _t(self::class.'.Goals', 'Goals')),
            HTMLEditorField::create('Modalities', _t(self::class.'.Modalities', 'Modalities')),
            HTMLEditorField::create('Accessibility', _t(self::class.'.Accessibility', 'Accessibility')),
            HTMLEditorField::create('Financing', _t(self::class.'.Financing', 'Financing')),
        );

        $manyManyRelations = $this->manyMany();
        unset($manyManyRelations['LinkTracking']);
        unset($manyManyRelations['FileTracking']);
        foreach ($manyManyRelations as $manyManyRelationKey => $manyManyRelationClassName) {
            $fields->push(LiteralField::create($manyManyRelationKey.'Title', '<label >'._t(self::class.'.'.$manyManyRelationKey, $manyManyRelationKey).'</label>'));
            $config = GridFieldConfig_RelationEditor::create();
            $fields->push(CompositeField::create(new FieldList([
                GridField::create($manyManyRelationKey, false, $manyManyRelationClassName::get(), $config),
            ])));
        }

        $hasOne = $this->hasOne();
        unset($hasOne['Category']);
        foreach ($hasOne as $hasOneRelationKey => $hasOneRelationData) {
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType(GridFieldFilterHeader::class);
            $config->removeComponentsByType(GridField_ActionMenu::class);
            $gridField = GridField::create($hasOneRelationKey, false, $hasOneRelationData::get(), $config);
            $toggleField = ToggleCompositeField::create('Manage'.$hasOneRelationKey, _t(self::class . '.MANAGE_'.strtoupper($hasOneRelationKey), 'Manage '.$hasOneRelationKey), new FieldList($gridField));
            $fields->insertAfter($hasOneRelationKey.'ID', $toggleField);
        }

        Requirements::customCSS("
            .ss-toggle .ui-accordion-content {
                padding : 1em 2.2em !important;
            }
        ");
        return $fields;
    }
}
