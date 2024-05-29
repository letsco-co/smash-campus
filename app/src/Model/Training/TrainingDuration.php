<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\ORM\DataObject;

class TrainingDuration extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco-TrainingDuration';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
}
