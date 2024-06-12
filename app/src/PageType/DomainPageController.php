<?php

namespace LetsCo\PageType;

use LetsCo\Controller\TrainingController;
use LetsCo\Form\TrainingRegistrationIndividualForm;
use LetsCo\Model\Training\Training;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class DomainPageController extends \PageController
{
    private static $allowed_actions = [
        'getTraining',
    ];
    private static $url_handlers = [
        '$Action!//$ID/$OtherID' => 'getTraining',
    ];
    public function getTraining(HTTPRequest $request)
    {
        $controller = TrainingController::create();
        $controller->setUrlSegment($this->Link());
        return $controller;
    }

}
