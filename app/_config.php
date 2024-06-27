<?php

use LetsCo\Extension\EventNotification;
use LetsCo\Extension\MeetingRegistrationFormNotification;
use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Model\Meeting\Meeting;
use SilverStripe\Core\Environment;
use SilverStripe\i18n\i18n;

i18n::set_locale('fr_FR');

if (Environment::getEnv('BREVO_API_KEY')) {
    Meeting::add_extension(EventNotification::class);
    MeetingRegistrationForm::add_extension(MeetingRegistrationFormNotification::class);
}
