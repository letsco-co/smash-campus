<?php

namespace LetsCo\Model;

use SilverStripe\ORM\DataObject;

class TrainingEducationalMeans extends DataObject
{
    private static $table_name = 'Letsco-TrainingEducationalMeans';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $belongs_many_many = [
        'Trainings' => Training::class,
    ];
}
