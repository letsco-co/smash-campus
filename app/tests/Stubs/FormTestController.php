<?php

namespace tests\Stubs;

use LetsCo\Form\MeetingRegistrationForm;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\TestOnly;

class FormTestController extends Controller implements TestOnly
{
    private static $url_segment = 'FormTestController';

    public function MeetingRegistrationForm()
    {
        return Injector::inst()->get(MeetingRegistrationForm::class, false, [$this, 'Form'])
            ->setHTMLID(MeetingRegistrationForm::class);
    }
}
