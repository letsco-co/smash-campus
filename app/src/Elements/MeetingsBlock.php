<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\PageType\MeetingHolder;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;

class MeetingsBlock extends BaseElement
{
    private static string $icon = 'font-icon-menu-campaigns';

    private static string $table_name = 'MeetingsBlock';

    private static array $db = [
        'Limit' => 'Int',
        'Content' => 'HTMLText',
    ];

    /**
     * @var array
     */
    private static array $has_one = [
        'MeetingHolder' => MeetingHolder::class,
    ];

    /**
     * @var array
     */
    private static array $defaults = [
        'Limit' => 2,
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->dataFieldByName('Content')
                ->setRows(8);

            $fields->dataFieldByName('Limit')
                ->setTitle(_t(__CLASS__ . 'LimitLabel', 'Posts to show'));

            if (class_exists(MeetingHolder::class)) {
                $fields->insertBefore(
                    'Limit',
                    $fields->dataFieldByName('MeetingHolderID')
                        ->setTitle(_t(__CLASS__ . 'MeetingHolder', 'Featured Blog'))
                        ->setEmptyString('')
                );

            }
        });

        return parent::getCMSFields();
    }
    public function getMeetingsList()
    {
        /** @var ArrayList $posts */
        $posts = ArrayList::create();

        if ($this->MeetingHolderID && $blog = MeetingHolder::get()->byID($this->MeetingHolderID)) {
            $posts = $blog->Meetings()->where('Date >= CURDATE()')->sort('Date ASC');
        } else {
            $posts = Meeting::get()->sort('Date ASC');
        }

        $this->extend('updateGetPostsList', $posts);

        return $posts->limit($this->Limit);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Meetings');
    }
}
