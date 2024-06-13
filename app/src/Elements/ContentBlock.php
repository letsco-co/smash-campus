<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;

class ContentBlock extends BaseElement
{
    private static $table_name = 'ContentBlock';
    private static $db = [
        'Content' => 'HTMLText'
    ];
    private static $icon = 'font-icon-block-content';
    private static array $has_one = [
        'MainLink' => Link::class,
        'SecondaryLink' => Link::class,
        'Image' => Image::class,
    ];
    private static array $owns = [
        'MainLink',
        'SecondaryLink',
        'Image',
    ];
    private static $singular_name = 'Contenu';

    private static $description = 'What my custom element does';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['MainLinkID', 'SecondaryLinkID']);
        $fields->addFieldsToTab(
            'Root.Main',
            [
                LinkField::create('MainLink'),
                LinkField::create('SecondaryLink'),
            ]
        );
        $imageField = $fields->dataFieldByName('Image');
        $imageField->setFolderName('pages');
        return $fields;
    }

    public function getSummary(): string
    {
        return 'String that represents element';
    }

    public function getType()
    {
        return 'ContentBlock';
    }
}
