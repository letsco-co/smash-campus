<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\Hierarchy\Hierarchy;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Program extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Program';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_one = [
        'Event' => Event::class,
    ];
    private static $summary_fields = [
        'Title',
        'Parent.Title'
    ];
    private static $extensions = [
        Hierarchy::class,
    ];

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
}
