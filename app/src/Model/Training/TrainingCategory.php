<?php

namespace LetsCo\Model\Training;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;

class TrainingCategory extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingCategory';
    private static $db = [
        'Title' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
    ];
    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
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
    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Title',
        ]));
        return $validator;
    }

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

        $link = Controller::join_links($controller->Link().'/domain/'.$this->URLSegment, $action);

        $this->extend('updateRelativeLink', $link, $base, $action);

        return $link;
    }
}
