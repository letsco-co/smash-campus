<?php

namespace LetsCo\Form;

use LetsCo\Form\Steps\TrainingRegistrationPersonalDetailsStep;
use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingRegistration;
use SilverStripe\Control\Director;
use SilverStripe\MultiForm\Forms\MultiForm;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationIndividualForm extends MultiForm
{
    private static $start_step = TrainingRegistrationPersonalDetailsStep::class;

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
        $trainingID = null;
        $emailData = [];
        if ($steps) {
            $registration = TrainingRegistration::create();
            foreach ($steps as $step) {
                $data = $step->loadData();
                if (isset($data['Financing'])) {
                    $data['Financing'] = implode(',', $data['Financing']);
                }
                if (isset($data['HeardOfTrainingSource'])) {
                    $data['HeardOfTrainingSource'] = implode(',', $data['HeardOfTrainingSource']);
                }
                if (isset($data['Email'])) {
                    $emailData['Email'] = $data['Email'];
                }
                if (isset($data['FirstName'])) {
                    $emailData['FirstName'] = $data['FirstName'];
                }
                if (isset($data['LastName'])) {
                    $emailData['LastName'] = $data['LastName'];
                }
                $registration->update($data);
                $trainingID = $data["TrainingID"];
            }
            $registration->write();
        }
        $training = Training::get()->byID($trainingID);
        $link = $trainingID ? $training->Link().'?completed=1' : $this->controller->Link();
        $emailParams = [
            "Formation" => [
                'Nom' => $training->Title,
                'Lieu' => $training->Address,
                'Duree' => $training->Duration()->Title,
                'Lien' => Director::absoluteURL((string) $training->Link()),
            ],
        ];
        $this->extend('sendValidationEmail',  $emailData, $training, $emailParams);
        $this->session->delete();
        $this->controller->redirect($link);
    }

    public function getAllStepsLinear()
    {
        $steps = parent::getAllStepsLinear();
        foreach ($steps as $step) {
            if (str_contains($step->getExtraClasses(), 'completed')) {
                $step->Completed = true;
            }
        }
        return $steps;
    }
}
