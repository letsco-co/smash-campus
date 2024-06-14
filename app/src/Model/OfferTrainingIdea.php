<?php

namespace LetsCo\Model;

use LetsCo\Trait\LocalizationDataObject;
use SilverStripe\ORM\DataObject;

class OfferTrainingIdea extends DataObject
{
    use LocalizationDataObject;
    private static $table_name = 'Letsco_OfferTrainingIdea';
    private static $db = [
        'LastName' => 'Varchar',
        'FirstName' => 'Varchar',
        'Email' => 'Varchar',
        'PhoneNumber' => 'Varchar',
        'Description' => 'Text',
        'RGPD' => 'Boolean',
    ];
}
