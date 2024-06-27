<?php

namespace LetsCo\Form;

use LetsCo\Form\Steps\TrainingRegistrationPersonalDetailsStep;
use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingRegistration;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\MultiForm\Forms\MultiForm;
use SilverStripe\MultiForm\Models\MultiFormStep;

class TrainingRegistrationIndividualForm extends MultiForm
{
    private static $start_step = TrainingRegistrationPersonalDetailsStep::class;
    private EmailProvider $emailProvider;

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
        $this->sendValidationEmail($emailData, Training::get()->byID($trainingID));
        $link = $trainingID ? Training::get()->byID($trainingID)->Link().'?completed=1' : $this->controller->Link();
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

    public function setEmailProvider(EmailProvider $emailProvider) {
        $this->emailProvider =  $emailProvider;
    }

    private function sendValidationEmail($data, Training $training)
    {
        $contact = $this->emailProvider->getOrCreateContact($data['Email']);
        $this->emailProvider->addContactToList($training->ListId, $contact['email']);
        $this->emailProvider->addContactToList(Environment::getEnv('BREVO_NEWSLETTER_LIST_ID'), $contact['email']);
        $name = $data['FirstName'] . ' '. $data['LastName'];
        $to = [['name' => $name, 'email' => $data['Email']]];
        $templateId = Environment::getEnv('BREVO_TRAINING_TEMPLATE_ID');
        $params = [
            "Name" => $name,
            "Formation" => [
                'Nom' => $training->Title,
                'Lien' => Director::absoluteURL((string) $training->Link()),
            ],
        ];
        try {
            $this->emailProvider->send($to, $templateId, $params);
        } catch (\Exception $e) {
            Injector::inst()->get(LoggerInterface::class)->error($e);
        }
    }
}
