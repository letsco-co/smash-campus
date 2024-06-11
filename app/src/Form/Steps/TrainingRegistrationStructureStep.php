<?php

namespace LetsCo\Form\Steps;

use LetsCo\FormField\FrenchPostCodeField;
use LetsCo\Model\Training\TrainingRegistration;
use LetsCo\Trait\TrainingIDFromURL;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationStructureStep extends MultiFormStep
{
    use TrainingIDFromURL;
    private static $next_steps = TrainingRegistrationManagerDetailsStep::class;
    public function getFields()
    {
        $fields = FieldList::create(
            HeaderField::create('Structure', _t(TrainingRegistration::class.'.Structure', 'Structure'), 3),
            TextField::create('StructureName', _t(TrainingRegistration::class.'.StructureName', 'StructureName'))->addExtraClass("form-control"),
            TextField::create('StructureActivity', _t(TrainingRegistration::class.'.StructureActivity', 'StructureActivity'))->addExtraClass("form-control"),
            TextField::create('StructureAddress', _t(TrainingRegistration::class.'.StructureAddress', 'StructureAddress'))->addExtraClass("form-control"),
            FrenchPostCodeField::create('StructurePostCode', _t(TrainingRegistration::class.'.StructurePostCode', 'StructurePostCode'))->addExtraClass("form-control"),
            TextField::create('StructureCity', _t(TrainingRegistration::class.'.StructureCity', 'StructureCity'))->addExtraClass("form-control"),
        );
        $trainingID = $this->getTrainingID();
        $fields->push(
            HiddenField::create('TrainingID', null, $trainingID),
        );
        return $fields;
    }

    public function getValidator()
    {
        return RequiredFields::create(array(
            'StructureName',
            'StructureActivity',
            'StructureAddress',
            'StructurePostCode',
            'StructureCity',
        ));
    }
}
