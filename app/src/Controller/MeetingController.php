<?php

namespace LetsCo\Controller;

use LetsCo\Form\MeetingInvitationForm;
use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Model\Meeting\Meeting;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\View\Requirements;

class MeetingController extends ContentController
{
    private static $allowed_actions = [
        'RegistrationForm',
        'GuestInvitationForm',
    ];

    private static $url_handlers = [
        'guests' => 'index',
    ];
    private $parentUrl;
    public function setUrlSegment(string $urlSegment) {
        $this->parentUrl = $urlSegment;
    }

    public function index(HTTPRequest $request)
    {
        Requirements::javascript('https://unpkg.com/@glidejs/glide@3.6.1/dist/glide.js');
        Requirements::javascript('themes/smash-campus/javascript/carrousel.js', [
            'type' => 'module'
        ]);
        Requirements::css('https://unpkg.com/@glidejs/glide@3.6.1/dist/css/glide.core.min.css');
        Requirements::css('https://unpkg.com/@glidejs/glide@3.6.1/dist/css/glide.theme.css');

        $meeting = Meeting::get()->filter('URLSegment',$request->param('Action'))->first();

        if(!$meeting) {
            return $this->httpError(404,'That meeting could not be found');
        }
        $form = MeetingRegistrationForm::create($this, 'RegistrationForm');
        $form->setFormAction($meeting->Link().'/'.$form->getName());
        $form->loadDataFrom($request->getVars());
        $data = [
            'Meeting' => $meeting,
            'Title' => $meeting->Title,
            'Form' => $form,
        ];
        if ($request->getVar('completed')) {
            $data['Completed'] = true;
        }
        if ($request->getVar('isGuest')) {
            $data['IsGuest'] = true;
        }
        if ($request->getVar('invitationCompleted')) {
            $data['InvitationCompleted'] = true;
        }
        if ($request->getVar('completed') || $request->param('ID')) {
            $guestForm = MeetingInvitationForm::create($this, 'GuestInvitationForm');
            $guestForm->setFormAction($meeting->Link().'/'.$guestForm->getName());
            $data['Form'] = $guestForm;
        }
        if ($request->param('ID')) {

            $data['HideAsideHeader'] = true;
        }

        return $this->customise($data)->renderWith(['MeetingPage', 'Page']);
    }

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
    public function RegistrationForm()
    {
        $form = MeetingRegistrationForm::create($this, 'RegistrationForm');
        $meeting = Meeting::get()->byID($this->getRequest()->postVar("MeetingID"));
        $form->setFormAction($meeting->Link().'/'.$form->getName());
        return $form;
    }

    public function GuestInvitationForm()
    {
        $form = MeetingInvitationForm::create($this, 'GuestInvitationForm');
        $meeting = Meeting::get()->byID($this->getRequest()->postVar("MeetingID"));
        $form->setDisplayLink($meeting->Link().'/guests');
        $form->setFormAction($meeting->Link().'/'.$form->getName());
        return $form;
    }
}
