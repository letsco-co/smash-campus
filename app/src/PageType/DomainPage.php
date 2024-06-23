<?php

namespace LetsCo\PageType;

use LetsCo\Model\Training\Training;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;

class DomainPage extends \Page
{
    private static $description = "Domain page displaying trainings";
    private static $can_be_root = false;
    private static $table_name = 'Letsco_DomainPage';
    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $has_many = [
        'Trainings' => Training::class,
    ];
    private static $owns = [
        'Image',
    ];
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->insertBefore('Content', $uploader = UploadField::create('Image', _t(self::class.'.Image', 'Image')));

        $uploader->setFolderName('domaines');
        $uploader->getValidator()->setAllowedExtensions(['png','gif','jpeg','jpg', 'webp']);

        return $fields;
    }
    public function otherDomains()
    {
        return DomainPage::get()->filter('ParentID', $this->ParentID)->exclude('ID', $this->ID);
    }
}
