<?php

namespace LetsCo\Model;

use SilverStripe\ORM\DataObject;

class TrainingPublic extends DataObject
{
    private static $table_name = 'Letsco-TrainingPublic';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $belongs_many_many = [
        'Trainings' => Training::class,
    ];
}
