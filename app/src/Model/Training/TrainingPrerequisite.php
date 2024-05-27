<?php

namespace LetsCo\Model;

use SilverStripe\ORM\DataObject;

class TrainingPrerequisite extends DataObject
{
    private static $table_name = 'Letsco-TrainingPrerequisite';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $belongs_many_many = [
        'Trainings' => Training::class,
    ];
}
