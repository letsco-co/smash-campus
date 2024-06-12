<?php

namespace LetsCo\Form\Steps;

use LetsCo\FormField\FrenchPhoneNumberField;
use LetsCo\Model\Training\TrainingRegistration;
use LetsCo\Trait\TrainingIDFromURL;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationManagerDetailsStep extends MultiFormStep
{
    use TrainingIDFromURL;
    private static $next_steps = TrainingRegistrationTrainingStep::class;

    public function getFields()
    {
        $fields = FieldList::create(
            HeaderField::create('Manager', _t(TrainingRegistration::class.'.Manager', 'Manager'), 3),
            TextField::create('ManagerLastName', _t(TrainingRegistration::class.'.ManagerLastName', 'ManagerLastName'))->addExtraClass("form-control"),
            TextField::create('ManagerFirstName', _t(TrainingRegistration::class.'.ManagerFirstName', 'ManagerFirstName'))->addExtraClass("form-control"),
            EmailField::create('ManagerEmail', _t(TrainingRegistration::class.'.ManagerEmail', 'ManagerEmail'))->addExtraClass("form-control"),
            TextField::create('ManagerFonction', _t(TrainingRegistration::class.'.ManagerFonction', 'ManagerFonction'))->addExtraClass("form-control"),
            FrenchPhoneNumberField::create('ManagerPhoneNumber', _t(TrainingRegistration::class.'.ManagerPhoneNumber', 'ManagerPhoneNumber'))->addExtraClass("form-control"),
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
            'ManagerLastName',
            'ManagerFirstName',
            'ManagerEmail',
            'ManagerPhoneNumber',
        ));
    }
}
