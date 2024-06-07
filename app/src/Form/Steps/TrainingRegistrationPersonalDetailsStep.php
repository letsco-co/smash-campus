<?php

namespace LetsCo\Form\Steps;

use LetsCo\FormField\FrenchPhoneNumberField;
use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingRegistration;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationPersonalDetailsStep extends MultiFormStep
{
    private static $next_steps = TrainingRegistrationStructureStep::class;
    public function getFields()
    {
        $trainingRegistration = singleton(TrainingRegistration::class);
        $fields = FieldList::create(
            HeaderField::create('PersonalDetails', _t(TrainingRegistration::class.".PersonalInfos", "Personal Details"), 3),
            OptionsetField::create(
                'PersonTitle',
                _t(TrainingRegistration::class.'.PersonTitle', 'PersonTitle'),
                TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject("PersonTitle")->enumValues()),
                "Mr"),
            TextField::create('LastName', _t(TrainingRegistration::class.'.LastName', 'LastName'))->addExtraClass("form-control"),
            TextField::create('FirstName', _t(TrainingRegistration::class.'.FirstName', 'FirstName'))->addExtraClass("form-control"),
            TextField::create('Fonction', _t(TrainingRegistration::class.'.Fonction', 'Fonction'))->addExtraClass("form-control"),
            TextField::create('ProAddress', _t(TrainingRegistration::class.'.ProAddress', 'Pro address'))->addExtraClass("form-control"),
            FrenchPhoneNumberField::create('PhoneNumber', _t(TrainingRegistration::class.'.PhoneNumber', 'Phone number'))->addExtraClass("form-control"),
            EmailField::create('Email', _t(TrainingRegistration::class.'.Email', 'Email'))->addExtraClass("form-control"),
        );
        $trainingURLSegment = $this->getForm()->getRequestHandler()->getRequest()->param("ID");
        $training = Training::get()->filter("URLSegment", $trainingURLSegment)->first();
        $trainingID = $training->ID ?? 0;
        $fields->push(
            HiddenField::create('TrainingID', null, $trainingID),
        );
        $formType = $this->getForm()->getRequestHandler()->getRequest()->param("OtherID");
        $isIndividualFinancing = $formType == 'individualform';
        $fields->push(
            HiddenField::create('isIndividualFinancing', null, $isIndividualFinancing),
        );
        return $fields;
    }

    public function getValidator()
    {
        return RequiredFields::create(array(
            'PersonTitle',
            'LastName',
            'FirstName',
            'Fonction',
            'Email',
        ));
    }

    public function getNextStep()
    {
        $data = $this->loadData();
        if(isset($data['isIndividualFinancing']) && $data['isIndividualFinancing']) {
            return TrainingRegistrationTrainingStep::class;
        } else {
            return $this->config()->get('next_steps');
        }
    }
}
