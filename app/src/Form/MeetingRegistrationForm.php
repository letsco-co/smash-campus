<?php

namespace LetsCo\Form;

use LetsCo\Interface\EmailProvider;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Meeting\MeetingRegistration;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Director;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;

class MeetingRegistrationForm extends Form
{
    private EmailProvider $emailProvider;
    public function __construct(RequestHandler $controller = null, $name = self::DEFAULT_NAME)
    {
        $fields = $this->getFields();
        $actions = $this->getActions();
        $validator = $this->defineValidator();
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    public function getFields()
    {
        $trainingURLSegment = $this->getRequest()->param("Action");
        $training = Meeting::get()->filter("URLSegment", $trainingURLSegment)->first();
        $trainingID = $training->ID ?? 0;
        return FieldList::create(
            HiddenField::create('MeetingID', null, $trainingID),
            HiddenField::create('IsGuest', null, $this->getRequest()->getVar('IsGuest')),
            TextField::create('LastName', _t(MeetingRegistration::class.'.LastName', 'LastName'))->addExtraClass("form-control"),
            TextField::create('FirstName', _t(MeetingRegistration::class.'.FirstName', 'FirstName'))->addExtraClass("form-control"),
            EmailField::create('Email', _t(MeetingRegistration::class.'.Email', 'Email'))->addExtraClass("form-control"),
            TextField::create('StructureName', _t(MeetingRegistration::class.'.StructureName', 'StructureName'))->addExtraClass("form-control"),
            TextField::create('Fonction', _t(MeetingRegistration::class.'.Fonction', 'Fonction'))->addExtraClass("form-control"),
            CheckboxField::create('AcceptRGPD', _t(MeetingRegistration::class.'.AcceptRGPD', 'AcceptRGPD')),
            LiteralField::create('RGPDLink', '<a class="link-underline link-underline-opacity-0" href="#">
                                                      Politique de confidentialité
                                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
</svg>
                                                    </a>'),
            CheckboxField::create('AcceptOtherInfos', _t(MeetingRegistration::class.'.AcceptOtherInfos', 'AcceptOtherInfos')),
        );
    }

    public function getActions()
    {
        return FieldList::create(
            FormAction::create('doSaveMeeting', _t(self::class.'.doSave', 'save'))
            ->addExtraClass('btn btn-primary bg-secondary-hover border-0 flex-grow-1'),
        );
    }

    public function defineValidator()
    {
        return RequiredFields::create(array(
            'LastName',
            'FirstName',
            'Email',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ));
    }

    public function doSaveMeeting($data) {
        $meetingID = $data['MeetingID'];
        $meeting = Meeting::get()->byID($meetingID);
        $registration = new MeetingRegistration();
        $registration->update($data);
        if ($meeting->remainingSeats()) {
            $registration->Status = MeetingRegistration::STATUS_ACCEPTED;
        } else {
            $registration->Status = MeetingRegistration::STATUS_WAITING;
        }
        $registration->write();
        $completionStep = 'Completed';
        if ($registration->Status == MeetingRegistration::STATUS_WAITING) {
            $completionStep = 'WaitingList';
        }
        if ($data['IsGuest']) {
            $completionStep = "GuestCompleted";
        }
        $this->sendValidationEmail($completionStep, $data, $meeting);
        $URLgetVar = "?CompletionStep=$completionStep";
        $link = $meetingID ? Meeting::get()->byID($meetingID)->Link().$URLgetVar : $this->getController()->Link();
        return $this->getController()->redirect($link);

    }

    public function setEmailProvider(EmailProvider $emailProvider) {
        $this->emailProvider =  $emailProvider;
    }

    private function sendValidationEmail(string $completionStep, $data, Meeting $meeting)
    {
        $contact = $this->emailProvider->getOrCreateContact($data['Email']);
        $this->emailProvider->addContactToList($meeting->ListId, $contact['email']);
        $this->emailProvider->addContactToList(Environment::getEnv('BREVO_NEWSLETTER_LIST_ID'), $contact['email']);
        $name = $data['FirstName'] . ' '. $data['LastName'];
        $to = [['name' => $name, 'email' => $data['Email']]];
        $templateId = Environment::getEnv('BREVO_MEETING_TEMPLATE_ID');
        $params = [
            "Name" => $name,
            "Conference" => [
                'Nom' => $meeting->Title,
                'Date' => $meeting->Date,
                'Heure' => $meeting->Time,
                'Lien' => Director::absoluteURL((string) $meeting->Link()),
            ],
            "IsInWaitingList" => $completionStep == "WaitingList",
        ];
        try {
            $this->emailProvider->send($to, $templateId, $params);
        } catch (\Exception $e) {
            Injector::inst()->get(LoggerInterface::class)->error($e);
        }
    }
}
