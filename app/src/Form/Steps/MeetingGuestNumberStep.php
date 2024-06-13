<?php

namespace LetsCo\Form\Steps;

use LetsCo\Model\Meeting\Meeting;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\MultiForm\Models\MultiFormStep;

class MeetingGuestNumberStep extends MultiFormStep
{
    private static $next_steps = MeetingGuest1PersonalDetailsStep::class;
    public function getFields()
    {
        $trainingURLSegment = $this->getForm()->getRequestHandler()->getRequest()->param("Action");
        $training = Meeting::get()->filter("URLSegment", $trainingURLSegment)->first();
        $trainingID = $training->ID ?? 0;
        return FieldList::create(
            HiddenField::create('MeetingID', null, $trainingID),
            HeaderField::create('Guests', _t(self::class.'.Guests', 'Invite guests') ,3),
            DropdownField::create('NumberGuests', _t(self::class.'.NumberGuests', 'Number of guests'), [1=>1,2=>2,3=>3])->addExtraClass('form-select')
        );
    }
    public function getNextStep()
    {
        $data = $this->loadData();
        if(isset($data['NumberGuests']) && $data['NumberGuests'] == 1) {
            $this->config()->set('next_steps',MeetingGuest3PersonalDetailsStep::class);
            return MeetingGuest3PersonalDetailsStep::class;
        } else {
            return $this->config()->get('next_steps');
        }
    }

}
