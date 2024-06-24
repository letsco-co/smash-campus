<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;

class ContentBlock extends BaseElement
{
    use LocalizationDataObject;
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
        $contentField = $fields->dataFieldByName('Content');
        $contentField->setTitle(_t(self::class.'.Content', 'Content'));
        $fields->removeByName(['MainLinkID', 'SecondaryLinkID']);
        $fields->addFieldsToTab(
            'Root.Main',
            [
                LinkField::create('MainLink', _t(self::class.'.MainLink', 'Main Link')),
                LinkField::create('SecondaryLink', _t(self::class.'.SecondaryLink', 'Secondary Link')),
            ]
        );
        $imageField = $fields->dataFieldByName('Image');
        $imageField->setTitle(_t(self::class.'.Image', 'Image'));
        $imageField->setFolderName('pages');
        return $fields;
    }

    public function getSummary(): string
    {
        return _t(self::class.'.Summary', 'Summary');
    }
}
