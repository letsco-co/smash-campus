<?php

namespace LetsCo\Form\Steps;

use LetsCo\Trait\MeetingGuestFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class MeetingGuest1PersonalDetailsStep extends MultiFormStep
{
    use MeetingGuestFields;
    private static $next_steps = MeetingGuest2PersonalDetailsStep::class;

    public function getNextStep()
    {
        $number = $this->getValueFromOtherStep(MeetingGuestNumberStep::class, 'NumberGuests');
        if(isset($number) && $number == 2) {
            $this->config()->set('next_steps',MeetingGuest3PersonalDetailsStep::class);
            return MeetingGuest3PersonalDetailsStep::class;
        } else {
            return $this->config()->get('next_steps');
        }
    }
}
