<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;

class HeroSection extends BaseElement
{
    private static $table_name = 'HeroSection';
    private static $db = [
        'Content' => 'HTMLText'
    ];
    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $owns = [
        'Image'
    ];
    private static $singular_name = 'Première section';

    private static $description = 'What my custom element does';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $contentField = $fields->dataFieldByName('Content');
        $contentField->setTitle(_t(self::class.'.Content', 'Content'));
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
