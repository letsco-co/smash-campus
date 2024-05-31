<?php

namespace LetsCo\Trait;

trait LocalizationDataObject
{
    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels(true);
        foreach ($labels as $index => $label) {
            $labels[$index] = _t(self::class.'.'.$label, $label);
        }

        return $labels;
    }

    public function getTranslatableEnumValues(array $enumValues)
    {
        $translatableTitle = [];
        foreach ($enumValues as $enumKey => $enumValue) {
            $translatableTitle[$enumKey] = _t(self::class.'.'.$enumValue, $enumValue);
        }
        return $translatableTitle;
    }
}
