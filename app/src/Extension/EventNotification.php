<?php

namespace LetsCo\Extension;

use LetsCo\Email\BrevoEmailProvider;
use LetsCo\Model\Training\Training;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class EventNotification extends DataExtension
{
    private static $db = [
        'ListId' => "Int"
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('ListId');
    }
}
