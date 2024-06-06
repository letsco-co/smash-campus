<?php

namespace LetsCo\Model\Training;

use LetsCo\Admin\Training\TrainingAdmin;
use LetsCo\Model\Program;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormScaffolder;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\SearchableDropdownField;
use SilverStripe\Forms\SearchableMultiDropdownField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
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
        'URLSegment' => 'Varchar(255)',
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
        'EvaluationMethods' => TrainingEvaluationMethod::class,
        'ExecutionMonitorings' => TrainingExecutionMonitoring::class,
    ];

    private static $has_many = [
        'Registrations' => TrainingRegistration::class,
        'Programs' => Program::class,
    ];

    public function Link($action = null)
    {
        $relativeLink = $this->RelativeLink($action);
        $link =  Controller::join_links(Director::baseURL(), $relativeLink);
        $this->extend('updateLink', $link, $action, $relativeLink);
        return $link;
    }

    public function RelativeLink($action = null)
    {
        $controller = Controller::curr();
        // Legacy support: If $action === true, retain URLSegment for homepages,
        // but don't append any action
        if ($action === true) {
            $action = null;
        }

        $link = Controller::join_links($controller->Link().'/show/'.$this->URLSegment, $action);

        $this->extend('updateRelativeLink', $link, $base, $action);

        return $link;
    }

    public function getCMSFields()
    {
        $TabSet = TabSet::create('Root');
        $this->setMainTab($TabSet);

        $this->setHasManyRelationsTabs($TabSet);

        return FieldList::create(
            $TabSet
        );
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->isChanged('Title', 2) || !$this->URLSegment) {
            $filter = URLSegmentFilter::create();
            $baseSegment = $filter->filter($this->Title);
            $segment = $baseSegment;
            $count = 1;

            while (self::get()->filter('URLSegment', $segment)->exists()) {
                $segment = $baseSegment . '-' . $count;
                $count++;
            }

            $this->URLSegment = $segment;
        }
    }

    /**
     * @param TabSet $TabSet
     * @return void
     */
    public function setMainTab(TabSet $TabSet): void
    {
        $MainFields = Tab::create('Main',
            TextField::create('Title', _t(self::class . '.Title', 'Title')),
            SearchableDropdownField::create('CategoryID', _t(self::class . '.Category', 'Category'), TrainingCategory::get()),
            SearchableDropdownField::create('DurationID', _t(self::class . '.Duration', 'Duration'), TrainingDuration::get()),
            SearchableDropdownField::create('QualificationID', _t(self::class . '.Qualification', 'Qualification'), TrainingQualification::get()),
            SearchableDropdownField::create('ModeID', _t(self::class . '.Mode', 'Mode'), TrainingMode::get()),
            TextField::create('Address', _t(self::class . '.Address', 'Address')),
            HTMLEditorField::create('Goals', _t(self::class . '.Goals', 'Goals')),
            HTMLEditorField::create('Modalities', _t(self::class . '.Modalities', 'Modalities')),
            HTMLEditorField::create('Accessibility', _t(self::class . '.Accessibility', 'Accessibility')),
            HTMLEditorField::create('Financing', _t(self::class . '.Financing', 'Financing')),
        )->setTitle(_t(FormScaffolder::class.'.TABMAIN', 'Main'));

        $this->setManyManyFields($MainFields);

        $this->setHasOneFields($MainFields);


        $MainFields->push(LiteralField::create('Programs_Title' . 'Title', '<label >' . _t(self::class . '.' . 'Programs', 'Programs') . '</label>'));
        $manyManyConfig = GridFieldConfig_RelationEditor::create();
        $manyManyConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $MainFields->push(CompositeField::create(new FieldList([
            GridField::create('Programs', false, $this->Programs(), $manyManyConfig),
        ])));

        Requirements::customCSS("
            .ss-toggle .ui-accordion-content {
                padding : 2.2em !important;
            }
        ");
        $TabSet->push($MainFields);
    }

    /**
     * @param TabSet $TabSet
     * @return void
     */
    public function setHasManyRelationsTabs(TabSet $TabSet): void
    {
        $hasManyRelations = $this->hasMany();
        unset($hasManyRelations['Programs']);
        foreach ($hasManyRelations as $hasManyRelationName => $hasManyRelationClassName) {
            $relationConfig = GridFieldConfig_RelationEditor::create();
            $relationGridField = GridField::create($hasManyRelationName, false, $this->$hasManyRelationName(), $relationConfig);
            $relationTab = Tab::create($hasManyRelationName, $relationGridField)->setTitle(_t(self::class.'.'.$hasManyRelationName, $hasManyRelationName));
            $TabSet->push($relationTab);
        }
    }

    /**
     * @param Tab $MainFields
     * @return void
     */
    public function setManyManyFields(Tab $MainFields): void
    {
        $manyManyRelations = $this->manyMany();
        unset($manyManyRelations['LinkTracking']);
        unset($manyManyRelations['FileTracking']);
        foreach ($manyManyRelations as $manyManyRelationKey => $manyManyRelationClassName) {
            $MainFields->push(
                SearchableMultiDropdownField::create($manyManyRelationKey,  _t(self::class . '.' . $manyManyRelationKey, $manyManyRelationKey), $manyManyRelationClassName::get())
            );
        }
        $this->setManageRelationDataObject($manyManyRelations, $MainFields, '_Manage', '');
    }

    /**
     * @param Tab $MainFields
     * @return void
     */
    public function setHasOneFields(Tab $MainFields): void
    {
        $hasOne = $this->hasOne();
        unset($hasOne['Category']);
        $this->setManageRelationDataObject($hasOne, $MainFields);
    }

    /**
     * @param array $relations
     * @param Tab $MainFields
     * @param string $GridFieldName
     * @param string $insertAfter
     * @return void
     */
    public function setManageRelationDataObject(array $relations, Tab $MainFields, string $GridFieldName = '', string $insertAfter = 'ID'): void
    {
        foreach ($relations as $relationKey => $relationClassName) {
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType(GridField_ActionMenu::class);
            $paginator = $config->getComponentByType(GridFieldPaginator::class);
            $paginator->setItemsPerPage(5);
            $gridField = GridField::create($relationKey.$GridFieldName, false, $relationClassName::get(), $config);
            $toggleField = ToggleCompositeField::create(
                'Manage' . $relationKey,
                _t(self::class . '.MANAGE_' . strtoupper($relationKey), 'Manage ' . $relationKey),
                new FieldList($gridField));
            $MainFields->insertAfter($relationKey . $insertAfter, $toggleField);
        }
    }
}
