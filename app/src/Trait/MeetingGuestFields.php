<?php

namespace LetsCo\Trait;

use LetsCo\Form\Steps\MeetingGuest1PersonalDetailsStep;
use LetsCo\Form\Steps\MeetingGuest2PersonalDetailsStep;
use LetsCo\Form\Steps\MeetingGuest3PersonalDetailsStep;
use LetsCo\Form\Steps\MeetingGuestNumberStep;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Meeting\MeetingRegistration;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;

trait MeetingGuestFields
{
    public function getFields()
    {
        $trainingURLSegment = $this->getForm()->getRequestHandler()->getRequest()->param("Action");
        $training = Meeting::get()->filter("URLSegment", $trainingURLSegment)->first();
        $trainingID = $training->ID ?? 0;
        if (self::class == MeetingGuest1PersonalDetailsStep::class && $this->getValueFromOtherStep(MeetingGuestNumberStep::class, 'NumberGuests') == 2) {
            $this->config()->set('next_steps',MeetingGuest3PersonalDetailsStep::class);
        }

        return FieldList::create(
            HiddenField::create('MeetingID', null, $trainingID),
            HiddenField::create('Class', null, self::class),
            HeaderField::create('Guest', _t(self::class.'.Guest', 'Invitation')),
            TextField::create('LastName', _t(MeetingRegistration::class.'.LastName', 'LastName'))->addExtraClass("form-control"),
            TextField::create('FirstName', _t(MeetingRegistration::class.'.FirstName', 'FirstName'))->addExtraClass("form-control"),
            EmailField::create('Email', _t(MeetingRegistration::class.'.Email', 'Email'))->addExtraClass("form-control"),
            TextField::create('StructureName', _t(MeetingRegistration::class.'.StructureName', 'StructureName'))->addExtraClass("form-control"),
            TextField::create('Fonction', _t(MeetingRegistration::class.'.Fonction', 'Fonction'))->addExtraClass("form-control"),
        );
    }

    public function getValidator()
    {
        return RequiredFields::create(array(
            'LastName',
            'FirstName',
            'Email',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ));
    }
}
