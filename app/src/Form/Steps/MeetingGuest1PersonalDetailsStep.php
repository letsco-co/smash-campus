<?php

namespace LetsCo\Form\Steps;

use LetsCo\Trait\MeetingGuestFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class MeetingGuest1PersonalDetailsStep extends MultiFormStep
{
    use MeetingGuestFields;
    private static $next_steps = MeetingGuest2PersonalDetailsStep::class;

}
