<?php

namespace LetsCo\Controller;

use LetsCo\Model\OfferTrainingIdea;
use LetsCo\Model\Training\Training;
use LetsCo\PageType\DomainPage;
use SilverStripe\CMS\Controllers\ContentController;
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

class SearchController extends ContentController
{
    private static $url_segment = 'search';
    private static $allowed_actions = [
        'TrainingSearch',
        'doSave',
        'TrainingOfferForm',
    ];

    public function TrainingSearch(HTTPRequest $request)
    {
        $trainings = Training::get();
        if ($domainID = $request->getVar('domain')) {
            $trainings = $trainings->filter('CategoryID', $domainID);
        }
        if ($search = $request->getVar('search')) {
            $trainings = $trainings->filterAny([
                'Title:PartialMatch' => $search,
                'Goals:PartialMatch' => $search,
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
            $data['Heading'] = 'Ces formations peuvent peut-être vous intéresser :';
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
            TextField::create('LastName', 'Nom')->addExtraClass("form-control"),
            TextField::create('FirstName', 'Prénom')->addExtraClass("form-control"),
            EmailField::create('Email', 'Email')->addExtraClass("form-control"),
            TextField::create('PhoneNumber', 'Tel')->addExtraClass("form-control"),
            TextareaField::create('Description', 'Description de la formation voulue')->addExtraClass("form-control"),
            CheckboxField::create('RGPD', 'En soumettant ce formulaire vous acceptez qu’on utilise les données recueillis afin d’améliorer notre offre de formation et de vous recontacter sur votre demande.'),
        );
        $actions = FieldList::create(
            FormAction::create('doSave', 'Soumettre votre proposition')->addExtraClass('btn btn-primary bg-secondary-hover border-0 flex-grow-1')
        );
        $validator = RequiredFields::create([
            'LastName',
            'FirstName',
            'Email',
            'Description',
            'RGPD',
        ]);
        $form = new Form($this, __FUNCTION__,$fields,$actions, $validator);
        $form->setTemplate('OfferTrainingForm');
        return $form;
    }

    public function doSave($data) {
        $offer = new OfferTrainingIdea();
        $offer->update($data);
        $offer->write();
        return $this->redirectBack();
    }
}
