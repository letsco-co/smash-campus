<?php

namespace LetsCo\Model;

use SilverStripe\ORM\DataObject;

class TrainingCategory extends DataObject
{
    private static $table_name = 'Letsco-TrainingCategory';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $has_many = [
        'Training' => Training::class,
    ];
}
