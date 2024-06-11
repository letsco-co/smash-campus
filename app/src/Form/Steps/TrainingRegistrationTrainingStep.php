<?php

namespace LetsCo\Form\Steps;

use LetsCo\Model\Training\TrainingRegistration;
use LetsCo\Trait\TrainingIDFromURL;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationTrainingStep extends MultiFormStep
{
    use TrainingIDFromURL;
    private static $next_steps = TrainingRegistrationRGPDStep::class;

    public function getFields()
    {
        $trainingRegistration = singleton(TrainingRegistration::class);
        $fields = FieldList::create(
            HeaderField::create('Training', _t(TrainingRegistration::class.'.TrainingInfos', 'Training Infos'), 3),
            TextField::create('DesiredTrainingDate', _t(TrainingRegistration::class.'.DesiredTrainingDate', 'DesiredTrainingDate'))->addExtraClass("form-control"),
            TextareaField::create('Reasons', _t(TrainingRegistration::class.'.Reasons', 'Reasons'))->addExtraClass("form-control")->setRows(1),
            CheckboxSetField::create('Financing', _t(TrainingRegistration::class.'.Financing', 'Financing'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('Financing')->enumValues())),
            OptionsetField::create('IsDisabled', _t(TrainingRegistration::class.'.IsDisabled', 'IsDisabled'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('IsDisabled')->enumValues())),
            CheckboxSetField::create('HeardOfTrainingSource', _t(TrainingRegistration::class.'.HeardOfTrainingSource', 'HeardOfTrainingSource'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('HeardOfTrainingSource')->enumValues())),
        );
        $trainingID = $this->getTrainingID();
        $fields->push(
            HiddenField::create('TrainingID', null, $trainingID)
        );
        return $fields;
    }

    public function getValidator()
    {
        return RequiredFields::create(array(
            'DesiredTrainingDate',
            'Financing',
            'IsDisabled',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ));
    }
}
