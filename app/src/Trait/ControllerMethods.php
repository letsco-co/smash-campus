<?php

namespace LetsCo\Trait;

use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;

trait ControllerMethods
{
    public function getCurrentYear()
    {
        return date('Y');
    }

    public function NewsletterForm()
    {
        $fields = FieldList::create(
            EmailField::create('Email', '')->addExtraClass("form-control"),
        );
        $actions = FieldList::create(
            FormAction::create('doSave', 'Valider')->addExtraClass('btn btn-primary bg-secondary-hover border-0 flex-grow-1')
        );
        $validator = RequiredFields::create([
            'Email',
        ]);
        $form = new Form($this, __FUNCTION__,$fields,$actions, $validator);
        $form->setTemplate('SearchForm');
        return $form;
    }
}