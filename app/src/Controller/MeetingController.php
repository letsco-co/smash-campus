<?php

namespace LetsCo\Controller;

use LetsCo\Form\MeetingInvitationForm;
use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Trait\ControllerMethods;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\View\Requirements;

class MeetingController extends ContentController
{
    use ControllerMethods;
    private static $allowed_actions = [
        'RegistrationForm',
        'GuestInvitationForm',
        'Newsletter',
        'NewsletterForm',
        'DoSave',
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

        $data = [
            'Meeting' => $meeting,
            'Title' => $meeting->Title,
            'CompletionStep' => $request->getVar('CompletionStep'),
        ];
        if (!$data['CompletionStep']) {
            $form = MeetingRegistrationForm::create($this, 'RegistrationForm');
            $form->setFormAction($meeting->Link().'/'.$form->getName());
            $form->loadDataFrom($request->getVars());
            $data['Form'] = $form;
        }
        if ($meeting->remainingSeats() && ($data['CompletionStep'] == 'Completed' || $request->param('ID'))) {
            $guestForm = MeetingInvitationForm::create($this, 'GuestInvitationForm');
            $guestForm->setFormAction($meeting->Link().'/'.$guestForm->getName());
            $data['Form'] = $guestForm;
        }
        if (!$meeting->isTodayOrFuture()) {
            $data['Form'] = $this->Newsletter();
        }
        if ($request->param('ID')) {
            $data['HideAsideHeader'] = true;
        }
        if (!$data['CompletionStep'] &&  get_class($data['Form']) == MeetingInvitationForm::class) {
            $data['ShowFormIndicators'] = true;
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
    public function Newsletter()
    {
        $fields = FieldList::create(
            EmailField::create('Email', 'Email')->addExtraClass("form-control"),
        );
        $actions = FieldList::create(
            FormAction::create('doSave', 'Valider')->addExtraClass('btn btn-primary bg-secondary-hover border-0 flex-grow-1')
        );
        $validator = RequiredFields::create([
            'Email',
        ]);
        $form = new Form($this, __FUNCTION__,$fields,$actions, $validator);
        return $form;
    }
}
