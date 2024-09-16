<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\View\Parsers\URLSegmentFilter;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

class Program extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Program';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_one = [
        'Event' => Event::class,
        'Parent' => Program::class,
    ];

    private static $has_many = [
        'Children' => Program::class,
    ];
    private static $summary_fields = [
        'Title',
        'ChildrenList'
    ];

    private static $casting = [
        'ChildrenList' => 'Varchar',
    ];

    public function getChildrenList()
    {
        $list = array_values($this->Children()->map()->toArray());
        return implode(', ', $list);
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Title',
        ]));
        return $validator;
    }
    public function generateURLSegment()
    {
        $title = $this->Title;
        $filter = URLSegmentFilter::create();
        $filteredTitle = $filter->filter($title);

        // Fallback to generic page name if path is empty (= no valid, convertable characters)
        if (!$filteredTitle || $filteredTitle == '-' || $filteredTitle == '-1') {
            $filteredTitle = "$this->ID";
        }

        // Hook for extensions
        $this->extend('updateURLSegment', $filteredTitle, $title);

        return $filteredTitle;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ParentID');
        $fields->removeByName('Children');
        $fields->dataFieldByName('EventID')->setDisabled(true);
        $fields->insertBefore('EventID',LiteralField::create('Parts_Title' . 'Title', '<label >' . _t(self::class . '.' . 'Parts', 'Parts') . '</label>'));
        $config = 	GridFieldConfig::create()
            ->addComponent(GridFieldButtonRow::create('before'))
            ->addComponent(GridFieldToolbarHeader::create())
            ->addComponent(GridFieldTitleHeader::create())
            ->addComponent(GridFieldEditableColumns::create())
            ->addComponent(GridFieldDeleteAction::create())
            ->addComponent(GridFieldAddNewInlineButton::create());
        $config->getComponentByType(GridFieldEditableColumns::class)->setDisplayFields([
            'Title' => _t(self::class.'.SubTitle', 'Sub Title')
        ]);
        $fields->insertBefore('EventID',CompositeField::create(new FieldList([
            GridField::create('Programs', false, $this->Children(), $config),
        ])));
        return $fields;
    }

    /**
     * @param mixed $member
     * @return bool
     */
    public function checkPermission(mixed $member): bool
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\TrainingAdmin', 'any', $member) || Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->ParentID)
        {
            $this->EventID = $this->Parent()->EventID;
        }
    }

    public function canView($member = null)
    {
        return $this->checkPermission($member);
    }

    public function canEdit($member = null)
    {
        return $this->checkPermission($member);
    }

    public function canDelete($member = null)
    {
        return $this->checkPermission($member);
    }

    public function canCreate($member = null, $context = [])
    {
        return $this->checkPermission($member);
    }
}
