<?php

namespace LetsCo\Form\Steps;

use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingRegistration;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationPersonalDetailsStep extends MultiFormStep
{
    private static $next_steps = TrainingRegistrationStructureDetailsStep::class;
    public function getFields()
    {
        $trainingRegistration = singleton(TrainingRegistration::class);
        $fields = FieldList::create(
            OptionsetField::create(
                'PersonTitle',
                _t(TrainingRegistration::class.'.PersonTitle', 'PersonTitle'),
                TrainingRegistration::getTranslatableEnumValues($trainingRegistration->dbObject("PersonTitle")->enumValues()),
                "Mr"),
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
            'PersonTitle',
        ));
    }
}
