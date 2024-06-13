<?php

namespace LetsCo\Controller;

use LetsCo\Form\TrainingRegistrationIndividualForm;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Training\Training;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class MeetingController extends ContentController
{
//    private static $allowed_actions = [
//        'getMeeting',
////        'RegistrationForm',
//    ];

//    private static $url_handlers = [
//        'RegistrationForm' => 'RegistrationForm',
//    ];
//    private $parentUrl;
//    public function setUrlSegment(string $urlSegment) {
//        $this->parentUrl = $urlSegment;
//    }

    public function index(HTTPRequest $request)
    {
        $meeting = Meeting::get()->filter('URLSegment',$request->param('Action'))->first();

        if(!$meeting) {
            return $this->httpError(404,'That meeting could not be found');
        }

//        $otherDomains = $meeting->Page()->otherDomains();

        $data = [
            'Meeting' => $meeting,
            'Title' => $meeting->Title,
//            'otherDomains' => $otherDomains,
        ];

//        if ($request->param('ID') || $request->getVar("MultiFormSessionID")) {
//            $form = MeetingRegistrationForm::create($this, 'RegistrationForm');
//            $form->setDisplayLink($meeting->Link());
//            $form->setFormAction($meeting->Link().'/'.$form->getName());
//            $data['Form'] = $form;
//        }
        if ($request->getVar('completed')) {
            $data['Completed'] = true;
        }
        return $this->customise($data)->renderWith(['MeetingPage', 'Page']);
    }

//    public function RegistrationForm()
//    {
//        $form = MeetingRegistrationForm::create($this, 'RegistrationForm');
//        $meeting = Meeting::get()->byID($this->getRequest()->postVar("MeetingID"));
//        $form->setDisplayLink($meeting->Link());
//        $form->setFormAction($meeting->Link().'/'.$form->getName());
//        return $form;
//    }

    public function Link($action = null)
    {
        // Check configured url_segment
        $url = $this->parentUrl;
        if ($url) {
            $link = Controller::join_links($url, $action);
            return $link;
        } else {
            return parent::Link($action);
        }
    }
}
