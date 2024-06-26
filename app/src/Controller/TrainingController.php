<?php

namespace LetsCo\Controller;

use LetsCo\Form\TrainingRegistrationIndividualForm;
use LetsCo\Model\Training\Training;
use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;

class TrainingController extends PageController
{
    private static $allowed_actions = [
        'getTraining',
        'RegistrationForm',
        'NewsletterForm',
        'doSave',
    ];

    private static $url_handlers = [
        'RegistrationForm' => 'RegistrationForm',
        'individualform' => 'index',
        'structureform' => 'index',
    ];
    private $parentUrl;
    public function setUrlSegment(string $urlSegment) {
        $this->parentUrl = $urlSegment;
    }

    public function index(HTTPRequest $request)
    {
        $training = Training::get()->filter('URLSegment',$request->param('Action'))->first();

        if(!$training) {
            return $this->httpError(404,'That training could not be found');
        }

        $otherDomains = $training->Category()->otherDomains();

        $data = [
            'Training' => $training,
            'Title' => $training->Title,
            'otherDomains' => $otherDomains,
        ];

        if ($request->param('ID') || $request->getVar("MultiFormSessionID")) {
            $form = TrainingRegistrationIndividualForm::create($this, 'RegistrationForm');
            $form->setDisplayLink($training->Link());
            $form->setFormAction($training->Link().'/'.$form->getName());
            $form->enableSpamProtection();
            $data['Form'] = $form;
        }
        if ($request->getVar('completed')) {
            $data['Completed'] = true;
        }
        return $this->customise($data)->renderWith(['TrainingPage', 'Page']);
    }

    public function RegistrationForm()
    {
        $form = TrainingRegistrationIndividualForm::create($this, 'RegistrationForm');
        $training = Training::get()->byID($this->getRequest()->postVar("TrainingID"));
        $form->setDisplayLink($training->Link());
        $form->setFormAction($training->Link().'/'.$form->getName());
        $form->enableSpamProtection();
        return $form;
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
}
