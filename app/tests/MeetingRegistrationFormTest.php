<?php

namespace tests;

use LetsCo\Form\MeetingRegistrationForm;
use LetsCo\Model\Meeting\Meeting;
use LetsCo\Model\Meeting\MeetingRegistration;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use tests\Stubs\FormTestController;

class MeetingRegistrationFormTest extends SapphireTest
{
    protected static $fixture_file = 'MeetingRegistrationFormTest.yml';

    protected $controller;

    protected $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new FormTestController();
        $this->controller->setRequest(new HTTPRequest('GET', '/'));
        $this->controller->getRequest()->setSession(new Session([]));
        $this->controller->pushCurrent();
        $form = $this->form = $this->controller->MeetingRegistrationForm();
        Injector::inst()->registerService($form, MeetingRegistrationForm::class);
        $this->form =  $form;
    }

    public function testFormFields()
    {
        $fields = $this->form->getFields();

        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNotNull($fields->fieldByName('MeetingID'));
        $this->assertInstanceOf(TextField::class, $fields->fieldByName('LastName'));
        $this->assertInstanceOf(TextField::class, $fields->fieldByName('FirstName'));
        $this->assertInstanceOf(EmailField::class, $fields->fieldByName('Email'));
        $this->assertInstanceOf(CheckboxField::class, $fields->fieldByName('AcceptRGPD'));
        $this->assertInstanceOf(LiteralField::class, $fields->fieldByName('RGPDLink'));
    }

    public function testFormActions()
    {
        $actions = $this->form->getActions();

        $this->assertInstanceOf(FieldList::class, $actions);
        $this->assertInstanceOf(FormAction::class, $actions->fieldByName('action_doSaveMeeting'));
    }

    public function testFormValidation()
    {
        $validator = $this->form->defineValidator();

        $this->assertTrue($validator->fieldIsRequired('LastName'));
        $this->assertTrue($validator->fieldIsRequired('FirstName'));
        $this->assertTrue($validator->fieldIsRequired('Email'));
        $this->assertTrue($validator->fieldIsRequired('AcceptRGPD'));
        $this->assertTrue($validator->fieldIsRequired('AcceptOtherInfos'));
    }

    public function testDoSaveMeeting()
    {
        $meeting = $this->objFromFixture(Meeting::class, 'testMeeting');
        $data = [
            'MeetingID' => $meeting->ID,
            'LastName' => 'Doe',
            'FirstName' => 'John',
            'Email' => 'john@example.com',
            'AcceptRGPD' => true,
            'AcceptOtherInfos' => true,
            'IsGuest' => false,
        ];


        $this->assertEmpty(MeetingRegistration::get()); // No registrations yet

        $this->form->doSaveMeeting($data);

        $this->assertEquals(1, MeetingRegistration::get()->count());
        $registration = MeetingRegistration::get()->first();
        $this->assertEquals(MeetingRegistration::STATUS_ACCEPTED, $registration->Status);
        $this->assertEquals($data['Email'], $registration->Email);
    }
}
