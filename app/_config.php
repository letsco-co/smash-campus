<?php

use LetsCo\Controller\SearchController;
use LetsCo\Extension\EventNotification;
use LetsCo\Extension\EventFormNotification;
use LetsCo\Extension\NewsletterEmailList;
use LetsCo\Extension\NotifyAdminExtension;
use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Form\TrainingRegistrationIndividualForm;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Training\Training;
use SilverStripe\Core\Environment;
use SilverStripe\i18n\i18n;

i18n::set_locale('fr_FR');

if (Environment::getEnv('BREVO_API_KEY')) {
    Meeting::add_extension(EventNotification::class);
    Training::add_extension(EventNotification::class);
    PageController::add_extension(NewsletterEmailList::class);
    MeetingRegistrationForm::add_extension(EventFormNotification::class);
    TrainingRegistrationIndividualForm::add_extension(EventFormNotification::class);
    SearchController::add_extension(NotifyAdminExtension::class);
}
