<?php

namespace LetsCo\Form\Steps;

use LetsCo\Model\Training\TrainingRegistration;
use LetsCo\Trait\TrainingIDFromURL;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationRGPDStep extends MultiFormStep
{
    use TrainingIDFromURL;
    private static $is_final_step = true;
    public function getFields()
    {
        $trainingRegistration = singleton(TrainingRegistration::class);
        $fields = FieldList::create(
            HeaderField::create('RGPD', _t(TrainingRegistration::class.'.RGPD', 'RGPD'), 3),
            CheckboxField::create('AcceptRGPD', _t(TrainingRegistration::class.'.AcceptRGPD', 'AcceptRGPD')),
            LiteralField::create('RGPDLink', '<a class="link-underline link-underline-opacity-0" href="#">
                                                      Politique de confidentialité
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
</svg>
                                                    </a>'),
            CheckboxField::create('AcceptOtherInfos', _t(TrainingRegistration::class.'.AcceptOtherInfos', 'AcceptOtherInfos')),
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
            'AcceptRGPD',
            'AcceptOtherInfos',
        ));
    }
}
