<?php

namespace LetsCo\Extension;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class MaintenanceModeSiteConfig extends DataExtension
{
    private static $db = [
        "IsInMaintenanceMode" => "Boolean",
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root.Access',
            FieldGroup::create(
                new CheckboxField(
                    'IsInMaintenanceMode',
                    _t(self::class.'.SETTINGSACTIVATE', 'Activate Offline/Maintenance Mode')
                )
            )->setTitle(
                _t(self::class.'.SETTINGSHEADING', 'Offline/Maintenance Mode')
            )
        );
    }
}
