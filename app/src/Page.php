<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Forms\CheckboxField;

    class Page extends SiteTree
    {
        private static $db = [
            "ShowInFooterMenu" => "Boolean",
        ];

        private static $has_one = [];

        public function getSettingsFields()
        {
            $fields = parent::getSettingsFields();
            $showInFooterMenuField = new CheckboxField("ShowInFooterMenu", _t(self::class.'.ShowInFooterMenu', 'Show In Footer Menu'));
            $fields->insertAfter('ShowInMenus', $showInFooterMenuField);
            return $fields;
        }
    }
}
