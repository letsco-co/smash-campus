<?php

namespace LetsCo\Form\Steps;

use LetsCo\Trait\MeetingGuestFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class MeetingGuest2PersonalDetailsStep extends MultiFormStep
{
    use MeetingGuestFields;
    private static $next_steps = MeetingGuest3PersonalDetailsStep::class;

}
