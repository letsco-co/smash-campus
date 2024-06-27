<?php

namespace LetsCo\Controller;

use LetsCo\Model\OfferTrainingIdea;
use LetsCo\Model\Training\Training;
use LetsCo\PageType\DomainPage;
use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

class SearchController extends PageController
{
    private static $url_segment = 'search';
    private static $allowed_actions = [
        'TrainingSearch',
        'doSaveOffer',
        'TrainingOfferForm',
    ];

    public function __construct($dataRecord = null)
    {
        $this->extend('notificationConstructor');
        parent::__construct($dataRecord);
    }

    public function TrainingSearch(HTTPRequest $request)
    {
        $trainings = Training::get();
        if ($domainID = $request->getVar('domain')) {
            $trainings = $trainings->filter('CategoryID', $domainID);
        }
        if ($search = $request->getVar('search')) {
            $trainings = $trainings->filterAny([
                'Title:PartialMatch' => $search,
                'Description:PartialMatch' => $search,
            ]);
        }
        $data = [
            'Trainings' => $trainings,
            'Title' => 'Résultat pour : '.$search,
            'Categories' => DomainPage::get(),
            'CurrentCategoryID' => $domainID,
        ];
        if (!$trainings->exists()) {
            $data['NoTraining'] = true;
            $data['Heading'] = _t(self::class.'.OtherTrainings', 'This trainings can be of interest');
            $data['Trainings'] = Training::get();
        }
        return $this->customise($data)->renderWith(['SearchPage', 'Page']);
    }

    public function filterLink($domainID)
    {
        $link =  $this->Link('TrainingSearch').'?';
        foreach ($this->getRequest()->getVars() as $key => $var) {
            if ($key == 'domain') continue;
            $link .= "$key=$var&";
        }
        $link .= "domain=$domainID";
        return $link;
    }

    public function Link($action = null)
    {
        return Controller::join_links('search', $action);
    }

    public function TrainingOfferForm() {
        $fields = FieldList::create(
            TextField::create('LastName', _t(OfferTrainingIdea::class.'.LastName', 'LastName'))->addExtraClass("form-control"),
            TextField::create('FirstName', _t(OfferTrainingIdea::class.'.FirstName', 'FirstName'))->addExtraClass("form-control"),
            EmailField::create('Email', _t(OfferTrainingIdea::class.'.Email', 'Email'))->addExtraClass("form-control"),
            TextField::create('PhoneNumber', _t(OfferTrainingIdea::class.'.PhoneNumber', 'PhoneNumber'))->addExtraClass("form-control"),
            TextareaField::create('Description', _t(OfferTrainingIdea::class.'.Description', 'Description'))->addExtraClass("form-control"),
            CheckboxField::create('AcceptRGPD', _t(OfferTrainingIdea::class.'.AcceptRGPD', 'AcceptRGPD')),
        );
        $actions = FieldList::create(
            FormAction::create('doSaveOffer', _t(self::class.'.doSaveOffer', 'doSaveOffer'))->addExtraClass('btn btn-primary bg-secondary-hover border-0 flex-grow-1')
        );
        $validator = RequiredFields::create([
            'LastName',
            'FirstName',
            'Email',
            'Description',
            'AcceptRGPD',
        ]);
        $form = new Form($this, __FUNCTION__,$fields,$actions, $validator);
        $form->setTemplate('OfferTrainingForm');
        $form->enableSpamProtection();
        return $form;
    }

    public function doSaveOffer($data, $form) {
        $offer = new OfferTrainingIdea();
        $offer->update($data);
        $offer->write();
        $emailParams["Lien"] = $offer->CMSEditLink();
        $emailParams["Nom"] = "{$data['FirstName']} {$data['LastName']}";
        $this->extend("notifyAdmin", $emailParams);
        $form->sessionMessage(_t(self::class.'.Form_Validate', 'Your request has been sent to us'), 'good');
        return $this->redirectBack();
    }
}
