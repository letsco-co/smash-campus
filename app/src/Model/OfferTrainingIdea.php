<?php

namespace LetsCo\Model;

use LetsCo\Admin\OfferTrainingIdeaAdmin;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Admin\CMSEditLinkExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class OfferTrainingIdea extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_OfferTrainingIdea';
    private static string $cms_edit_owner = OfferTrainingIdeaAdmin::class;
    private static $db = [
        'LastName' => 'Varchar',
        'FirstName' => 'Varchar',
        'Email' => 'Varchar',
        'PhoneNumber' => 'Varchar',
        'Description' => 'Text',
        'AcceptRGPD' => 'Boolean',
    ];

    private static $summary_fields = [
        'Title',
        'Description.Summary',
    ];

    private static $extensions = [
        CMSEditLinkExtension::class,
    ];

    public function getTitle()
    {
        return $this->LastName . " ". $this->FirstName;
    }

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\OfferTrainingIdeaAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\OfferTrainingIdeaAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\OfferTrainingIdeaAdmin', 'any', $member);
    }
}
