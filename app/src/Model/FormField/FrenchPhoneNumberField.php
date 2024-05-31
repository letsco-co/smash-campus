<?php

namespace LetsCo\FormField;

use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

class FrenchPhoneNumberField extends TextField
{

    public function validate($validator)
    {
        // Regular expression for French phone numbers
        $pattern = '/^0[1-9](\d{2}){4}$/';

        if (!preg_match($pattern, $this->value)) {
            $validator->validationError(
                $this->name,
                _t(self::class.'.INVALID','Please enter a valid French phone number'),
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
        return 'tel';
    }
}
