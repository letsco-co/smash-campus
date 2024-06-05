<?php

namespace LetsCo\Extension;

use LetsCo\FormField\FrenchPhoneNumberField;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\UrlField;

class ThemeSiteConfig extends Extension
{
    private static $db = [
        'Address' => 'Varchar(255)',
        'PhoneNumber' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'LinkedInLink' => 'Varchar(255)',
    ];
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Slogan');
        $fields->addFieldToTab('Root.Main', TextField::create('Address', _t(self::class.'.Address', 'Address')));
        $fields->addFieldToTab('Root.Main', FrenchPhoneNumberField::create('PhoneNumber', _t(self::class.'.PhoneNumber', 'Phone number')));
        $fields->addFieldToTab('Root.Main', EmailField::create('Email', _t(self::class.'.Email', 'Email')));
        $fields->addFieldToTab('Root.Main', UrlField::create('LinkedInLink', _t(self::class.'.LinkedInLink', 'LinkedInLink')));
    }
}
