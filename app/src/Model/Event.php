<?php

namespace LetsCo\Model;

use SilverStripe\Admin\CMSEditLinkExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Parsers\URLSegmentFilter;

class Event extends DataObject
{
    private static $table_name = 'Letsco_Event';
    private static $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'HTMLText',
        'Address' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
    ];

    private static $has_many = [
        'Programs' => Program::class,
    ];
    private static $extensions = [
        Versioned::class,
        CMSEditLinkExtension::class,
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

    public function ProgramNice()
    {
        return $this->Programs()->filter('ParentID', 0);
    }

    public function translatedTitle($className, $entity)
    {
        return _t($className.'.'. $entity, $entity);
    }
}
