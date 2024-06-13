<?php

namespace LetsCo\PageType;

use LetsCo\Controller\MeetingController;
use LetsCo\Model\Meeting\Meeting;
use SilverStripe\Control\HTTPRequest;

class MeetingHolderController extends \PageController
{
    private static $allowed_actions = [
        'getMeeting',
    ];
    private static $url_handlers = [
        '$Action!//$ID/$OtherID' => 'getMeeting',
    ];
    public function getMeeting(HTTPRequest $request)
    {
        $controller = MeetingController::create();
//        $controller->setUrlSegment($this->Link());
        return $controller;
    }
}
