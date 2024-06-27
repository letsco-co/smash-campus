<?php

namespace LetsCo\Form;

use LetsCo\Form\Steps\MeetingGuestNumberStep;
use LetsCo\Model\Meeting\Meeting;
use SilverStripe\Control\Director;
use SilverStripe\MultiForm\Forms\MultiForm;
use SilverStripe\MultiForm\Models\MultiFormStep;
use SilverStripe\ORM\ArrayList;

class MeetingInvitationForm extends MultiForm
{
    private static $start_step = MeetingGuestNumberStep::class;
    public function __construct($controller, $name)
    {
        $this->extend('notificationConstructor');
        parent::__construct($controller, $name);
    }

    public function actionsFor($step)
    {
        $actions = parent::actionsFor($step);
        foreach ($actions as $action) {
            if ($action->actionName() == "prev") {
                $action->addExtraClass("btn-link btn icon-link icon-link-hover link-offset-2 link-underline link-underline-opacity-0");
                $action->setUseButtonTag(true);
                continue;
            }
            $action->addExtraClass("btn btn-primary bg-secondary-hover border-0 flex-grow-1");
            $action->setUseButtonTag(true);
        }

        return $actions;
    }

    public function finish($data, $form)
    {
        parent::finish($data, $form);
        $steps = MultiFormStep::get()->filter([
            "SessionID" => $this->session->ID
        ]);
        if ($steps) {
            $firstStep = $steps->first();
            $firstStepData = $firstStep->loadData();
            $meetingID = $firstStepData['MeetingID'];
            $meetingLink = Meeting::get()->byID($meetingID)->Link();
            $steps = $steps->exclude('ClassName',MeetingGuestNumberStep::class);
            $dataToLink = [
                'FirstName',
                'LastName',
                'Email',
                'StructureName',
                'Fonction'
            ];
            foreach ($steps as $step) {
                $data = $step->loadData();
                unset($data['MeetingID'], $data['Class'], $data['Guest']);
                $invitationLink = $meetingLink . "?";
                foreach ($data as $key => $datum) {
                    if (!in_array($key, $dataToLink)) continue;
                    $invitationLink .= $key.'='.$datum.'&';
                }
                $meeting = Meeting::get()->byID($meetingID);
                $invitationLink .= 'IsGuest=true';
                $emailParams = [
                    'Conference' => [
                        'Lien' => Director::absoluteURL($invitationLink),
                        'Nom' => $meeting->Title,
                    ]
                ];
                $this->extend('sendValidationEmail', $data, $emailParams);

            }
        }
        $link = $meetingLink.'?CompletionStep=GuestsInvited';
        $this->session->delete();
        $this->controller->redirect($link);
    }
    public function getAllStepsLinear()
    {
        $allSteps = parent::getAllStepsLinear();
        $steps = ArrayList::create();
        foreach ($allSteps as $step) {
            if ($step->ClassName == MeetingGuestNumberStep::class) continue;
            if (str_contains($step->getExtraClasses(), 'completed')) {
                $step->Completed = true;
            }
            $steps->push($step);
        }
        return $steps;
    }
}
