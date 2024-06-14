<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\TextField;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\ORM\ArrayList;

class InfosBlock extends BaseElement
{
    private static $table_name = 'InfosBlock';
    private static $db = [
        'Content' => 'HTMLText',
        'OnRight' => 'Boolean',
        'BlockTitle1' => 'Varchar',
        'BlockContent1' => 'Varchar',
        'BlockTitle2' => 'Varchar',
        'BlockContent2' => 'Varchar',
        'BlockTitle3' => 'Varchar',
        'BlockContent3' => 'Varchar',
        'BlockTitle4' => 'Varchar',
        'BlockContent4' => 'Varchar',
    ];
    private static $icon = 'font-icon-block-content';
    private static array $has_one = [
        'BlockLink1' => Link::class,
        'BlockLink2' => Link::class,
        'BlockLink3' => Link::class,
        'BlockLink4' => Link::class,
    ];
    private static array $owns = [
        'BlockLink1',
        'BlockLink2',
        'BlockLink3',
        'BlockLink4',
    ];
    private static $singular_name = 'Contenu';

    private static $description = 'What my custom element does';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['BlockTitle1','BlockTitle2','BlockTitle3','BlockTitle4','BlockContent1','BlockContent2','BlockContent3','BlockContent4', 'BlockLink1ID','BlockLink3ID','BlockLink2ID','BlockLink4ID']);
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create('BlockTitle1', _t(self::class.'.BlockTitle1', 'Title Block 1')),
                TextField::create('BlockContent1', _t(self::class.'.BlockContent1', 'Content Block 1')),
                LinkField::create('BlockLink1'),
                TextField::create('BlockTitle2', _t(self::class.'.BlockTitle2', 'Title Block 2')),
                TextField::create('BlockContent2', _t(self::class.'.BlockContent2', 'Content Block 2')),
                LinkField::create('BlockLink2'),
                TextField::create('BlockTitle3', _t(self::class.'.BlockTitle3', 'Title Block 3')),
                TextField::create('BlockContent3', _t(self::class.'.BlockContent3', 'Content Block 3')),
                LinkField::create('BlockLink3'),
                TextField::create('BlockTitle4', _t(self::class.'.BlockTitle4', 'Title Block 4')),
                TextField::create('BlockContent4', _t(self::class.'.BlockContent4', 'Content Block 4')),
                LinkField::create('BlockLink4'),
            ]
        );
        return $fields;
    }

    public function getSummary(): string
    {
        return 'String that represents element';
    }

    public function getType()
    {
        return 'InfosBlock';
    }

    public function getBlocks() {
        $blocks = new ArrayList();
        $blocks->push([
            'Title' => $this->BlockTitle1,
            'Content' => $this->BlockContent1,
            'Link' => $this->BlockLink1,
        ]);
        $blocks->push([
            'Title' => $this->BlockTitle2,
            'Content' => $this->BlockContent2,
            'Link' => $this->BlockLink2,
        ]);
        $blocks->push([
            'Title' => $this->BlockTitle3,
            'Content' => $this->BlockContent3,
            'Link' => $this->BlockLink3,
        ]);
        $blocks->push([
            'Title' => $this->BlockTitle4,
            'Content' => $this->BlockContent4,
            'Link' => $this->BlockLink4,
        ]);
        return $blocks;
    }
}
