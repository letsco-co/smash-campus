<?php

namespace LetsCo\Form\Steps;

use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingRegistration;
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
    private static $next_steps = TrainingRegistrationRGPDStep::class;

    public function getFields()
    {
        $trainingRegistration = singleton(TrainingRegistration::class);
        $fields = FieldList::create(
            HeaderField::create('Training', _t(TrainingRegistration::class.'.TrainingInfos', 'Training Infos'), 3),
            TextField::create('DesiredTrainingDate', _t(TrainingRegistration::class.'.DesiredTrainingDate', 'DesiredTrainingDate'))->addExtraClass("form-control"),
            TextareaField::create('Reasons', _t(TrainingRegistration::class.'.Reasons', 'Reasons'))->addExtraClass("form-control"),
            CheckboxSetField::create('Financing', _t(TrainingRegistration::class.'.Financing', 'Financing'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('Financing')->enumValues())),
            OptionsetField::create('IsDisabled', _t(TrainingRegistration::class.'.IsDisabled', 'IsDisabled'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('IsDisabled')->enumValues())),
            CheckboxSetField::create('HeardOfTrainingSource', _t(TrainingRegistration::class.'.HeardOfTrainingSource', 'HeardOfTrainingSource'), TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject('HeardOfTrainingSource')->enumValues())),
        );
        $trainingURLSegment = $this->getForm()->getRequestHandler()->getRequest()->param("ID");
        $training = Training::get()->filter("URLSegment", $trainingURLSegment)->first();
        $trainingID = $training->ID ?? 0;
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
