<?php

namespace LetsCo\Model\Meeting;

use LeKoala\CmsActions\CustomAction;
use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class MeetingRegistration extends DataObject
{
    const STATUS_ACCEPTED = 'Accepted';
    const STATUS_WAITING = 'Waiting';
    const STATUS_CANCELLED = 'Cancelled';
    use LocalizationDataObject;
    private static $table_name = 'Letsco_MeetingRegistration';
    private static $db = [
        'LastName' => 'Varchar(255)',
        'Status' => 'Enum("Waiting, Accepted, Cancelled","Waiting")',
        'FirstName' => 'Varchar(255)',
        'Fonction' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'StructureName' => 'Varchar(255)',
        'AcceptRGPD' => 'Boolean',
        'AcceptOtherInfos' => 'Boolean',
        'CanSendValidationEmail' => 'Boolean',
    ];
    private static $has_one = [
        'Meeting' => Meeting::class,
    ];

    private static $summary_fields = [
        'LastName',
        'FirstName',
        'Email',
        'Status',
    ];
    public function getTitle()
    {
        return $this->LastName . " ". $this->FirstName;
    }
    public function getCMSFields()
    {
        $fields =  parent::getCMSFields();
        $fields->removeByName('CanSendValidationEmail');
        $statusField = $fields->dataFieldByName('Status');
        $statusField->setSource(self::getTranslatableEnumValues($this->dbObject('Status')->enumValues()));
        return $fields;
    }

    public function getCMSActions()
    {
        $actions = parent::getCMSActions();
        if ($this->CanSendValidationEmail) {
            $sendEmailAction = new CustomAction("doSendRegistrationAcceptedEmail", _t(self::class.".SendRegistrationAcceptedEmail", "Send accepted email"));
            $sendEmailAction->setDescription(_t(self::class.".SendRegistrationAcceptedEmail_Description", "Send a confirmation's email"));
            $actions->push($sendEmailAction);
        }

        return $actions;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $changedFields = $this->getChangedFields(["Status"]);
        if ($changedFields && $changedFields["Status"]["after"] === self::STATUS_ACCEPTED) {
            $this->CanSendValidationEmail = true;
        }
    }

    public function doSendRegistrationAcceptedEmail()
    {
        $registrationForm = new MeetingRegistrationForm();
        $registrationForm->sendEmail($this->Meeting(), "", ["Email" => $this->Email, "FirstName" => $this->FirstName, "LastName" => $this->LastName]);
        $this->CanSendValidationEmail = false;
        $this->write();
        return _t(self::class.".EmailSent", "The validation email has been sent");
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $validator->addValidator(RequiredFields::create([
            'LastName',
            'FirstName',
            'Email',
            'AcceptRGPD',
            'AcceptOtherInfos',
        ]));
        return $validator;
    }
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Meeting\MeetingAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_LetsCo\Admin\Training\MeetingAdmin', 'any', $member);
    }

}
