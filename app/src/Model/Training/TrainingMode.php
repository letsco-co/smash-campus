<?php

namespace LetsCo\Model\Training;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class TrainingMode extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_TrainingMode';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'Title',
        ]));
        return $validator;
    }
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\TrainingAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\TrainingAdmin', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\TrainingAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\TrainingAdmin', 'any', $member);
    }
}
