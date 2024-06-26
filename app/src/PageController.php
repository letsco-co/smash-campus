<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;
    use SilverStripe\Forms\EmailField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\Forms\Form;
    use SilverStripe\Forms\FormAction;
    use SilverStripe\Forms\RequiredFields;

    /**
     * @template T of Page
     * @extends ContentController<T>
     */
    class PageController extends ContentController
    {
        /**
         * An array of actions that can be accessed via a request. Each array element should be an action name, and the
         * permissions or conditions required to allow the user to access it.
         *
         * <code>
         * [
         *     'action', // anyone can access this action
         *     'action' => true, // same as above
         *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
         *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
         * ];
         * </code>
         *
         * @var array
         */
        private static $allowed_actions = [
            'NewsletterForm',
            'doSave',
        ];

        protected function init()
        {
            parent::init();
            // You can include any CSS or JS required by your project here.
            // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/
        }

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
            $form->enableSpamProtection();
            return $form;
        }

        public function FooterMenu()
        {
            return \Page::get()->filter('ShowInFooterMenu', 1);
        }

        public function ContactPage() {
            return \Page::get()->filter('IsContactPage', 1)->first();
        }
    }
}
