<?php

namespace LetsCo\FormField;

use SilverStripe\Forms\TextField;

class FrenchPostCodeField extends TextField
{
    public function validate($validator)
    {
        $pattern = '/^\d{5}$/';
        if (!preg_match($pattern, $this->value)) {
            $validator->validationError(
                $this->name,
                _t(self::class.'.INVALID','Please enter a valid French post code'),
                'validation'
            );
            return parent::validate($validator);
        }
        return true;
    }

    public function Type()
    {
        return 'text';
    }

    public function getInputType()
    {
        return 'text';
    }
}
