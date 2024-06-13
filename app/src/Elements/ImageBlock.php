<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;

class ImageBlock extends BaseElement
{
    private static $table_name = 'ImageBlock';
    private static $icon = 'font-icon-block-file';
    private static array $has_one = [
        'Image' => Image::class,
    ];
    private static array $owns = [
        'Image',
    ];
    private static $displays_title_in_template = false;
    private static $singular_name = 'Image';

    private static $description = 'What my custom element does';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

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
        return 'ImageBlock';
    }
}
