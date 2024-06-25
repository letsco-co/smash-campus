<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\CheckboxField;

    class Page extends SiteTree
    {
        private static $db = [
            "ShowInFooterMenu" => "Boolean",
            "IsContactPage" => "Boolean",
        ];

        private static $has_one = [];

        public function getSettingsFields()
        {
            $fields = parent::getSettingsFields();
            $showInFooterMenuField = new CheckboxField("ShowInFooterMenu", _t(self::class.'.ShowInFooterMenu', 'Show In Footer Menu'));
            $fields->insertAfter('ShowInMenus', $showInFooterMenuField);
            $IsContactPage = new CheckboxField("IsContactPage", _t(self::class.'.IsContactPage', 'Is Contact Page'));
            $fields->insertAfter('ShowInFooterMenu', $IsContactPage);
            return $fields;
        }

        protected function onBeforeWrite()
        {
            parent::onBeforeWrite();

            if ($this->IsContactPage) {
                $contactPages = Page::get()->filter("IsContactPage", 1);
                foreach ($contactPages as $contactPage) {
                    $contactPage->IsContactPage = false;
                    $contactPage->write();
                }
            }
        }
    }
}
