<?php

namespace LetsCo\Model\Meeting;

use LetsCo\Admin\Meeting\MeetingAdmin;
use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;

class Meeting extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_Meeting';
    private static string $cms_edit_owner = MeetingAdmin::class;
    private static $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'Date' => 'Date',
        'Time' => 'Time',
        'Address' => 'Varchar(255)',
        'Limit' => 'Int',
    ];
}
