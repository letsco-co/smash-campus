<?php

namespace LetsCo\Form\Steps;

use LetsCo\Trait\MeetingGuestFields;
use SilverStripe\MultiForm\Models\MultiFormStep;

class MeetingGuest3PersonalDetailsStep extends MultiFormStep
{
    use MeetingGuestFields;
    private static $is_final_step = true;

}
