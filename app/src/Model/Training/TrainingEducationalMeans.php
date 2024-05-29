<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\ORM\DataObject;

class TrainingEducationalMeans extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco-TrainingEducationalMeans';
    private static $db = [
        'Title' => 'Varchar(255)',
    ];
    private static $belongs_many_many = [
        'Trainings' => Training::class,
    ];
}
