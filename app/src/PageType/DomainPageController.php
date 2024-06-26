<?php

namespace LetsCo\PageType;

use LetsCo\Controller\TrainingController;
use SilverStripe\Control\HTTPRequest;

class DomainPageController extends \PageController
{
    private static $allowed_actions = [
        'getTraining',
    ];
    private static $url_handlers = [
        'NewsletterForm' => 'NewsletterForm',
        '$Action!//$ID/$OtherID' => 'getTraining',
    ];
    public function getTraining(HTTPRequest $request)
    {
        $controller = TrainingController::create();
        $controller->setUrlSegment($this->Link());
        return $controller;
    }

    public function getTrainingsTitle()
    {
        return _t(DomainPage::class . '.TrainingsTitle', 'Trainings');
    }
}
