<?php

namespace LetsCo\Controller;

use LetsCo\Form\TrainingRegistrationIndividualForm;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Training\Training;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\View\Requirements;

class MeetingController extends ContentController
{

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
        ];
        if ($request->getVar('completed')) {
            $data['Completed'] = true;
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
}
