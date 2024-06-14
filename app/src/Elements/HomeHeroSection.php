<?php

namespace LetsCo\Elements;

use DNADesign\Elemental\Models\BaseElement;
use LetsCo\Controller\SearchController;
use LetsCo\PageType\DomainPage;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\TextField;

class HomeHeroSection extends BaseElement
{
    private static $table_name = 'HomeHeroSection';

    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $owns = [
        'Image'
    ];
    private static $singular_name = 'Première section';

    private static $description = 'What my custom element does';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $imageField = $fields->dataFieldByName('Image');
        $imageField->setFolderName('pages');

        return $fields;
    }

    public function getSummary(): string
    {
        return 'String that represents element';
    }

    public function getType()
    {
        return 'HomeHeroSection';
    }

    public function TrainingSearchForm() {
        $fields = FieldList::create(
            TextField::create('search', '')
                ->addExtraClass("form-control")
        );
        $action = FieldList::create(
            FormAction::create('search')
                ->setUseButtonTag(true)
                ->setButtonContent('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
</svg>')
                ->addExtraClass('btn btn-primary bg-secondary-hover border-0')
        );
        $form = new Form(SearchController::create(), 'TrainingSearch', $fields, $action);
        $form->setFormMethod('GET');
        $form->disableSecurityToken();
        $form->setTemplate('SearchForm');
        return $form;
    }

    public function Categories() {
        return DomainPage::get();
    }
}
