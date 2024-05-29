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
}
