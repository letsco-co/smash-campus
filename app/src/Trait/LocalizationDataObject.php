<?php

namespace LetsCo\Trait;

trait LocalizationDataObject
{
    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels(true);
        foreach ($labels as $index => $label) {
            $labels[$index] = _t(self::class.'.'.$index, $label);
        }

        return $labels;
    }

    public static function getTranslatableEnumValues(array $enumValues)
    {
        $translatableTitle = [];
        foreach ($enumValues as $enumKey => $enumValue) {
            $translatableTitle[$enumKey] = _t(self::class.'.'.$enumValue, $enumValue);
        }
        return $translatableTitle;
    }
    public function summaryFields()
    {
        $defaultSummaryFields = parent::summaryFields();
        $summaryFields = [];
        foreach ($defaultSummaryFields as $summaryFieldKey => $summaryFieldData) {
            if (str_contains($summaryFieldKey, '.')) {
                $summaryFields[$summaryFieldKey] = _t(
                    self::class.'.'. str_replace('.', '_', $summaryFieldKey),
                    str_replace('.', ' ', $summaryFieldKey)
                );
                continue;
            }
            $summaryFields[$summaryFieldKey] = _t(
                self::class.'.'. $summaryFieldKey,
                $summaryFieldKey
            );
        }
        return $summaryFields;
    }
}
