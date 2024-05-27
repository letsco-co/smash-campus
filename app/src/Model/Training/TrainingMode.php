<?php

namespace LetsCo\Model;

use SilverStripe\ORM\DataObject;

class TrainingMode extends DataObject
{
    private static $table_name = 'Letsco-TrainingMode';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
}
