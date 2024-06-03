<?php

namespace LetsCo\Admin\Training;


use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingCategory;
use SilverStripe\Admin\ModelAdmin;

class TrainingAdmin extends ModelAdmin
{
    private static $managed_models = [
        'training' => [
            'dataClass' => Training::class,
            'title' => Training::class,
        ],
        'category' => [
            'dataClass' => TrainingCategory::class,
            'title' => TrainingCategory::class,
        ],
    ];
    private static $url_segment = 'trainings';

    private static $menu_title = 'My Training Admin';

    private static $menu_icon_class = 'font-icon-book-open';
    public function getManagedModels()
    {
        $models = parent::getManagedModels();
        // Ensure tab titles can be localised

        foreach ($models as $key => $spec) {
            $models[$key]['title'] = singleton($spec['dataClass'])->i18n_plural_name();
        }
        return $models;
    }
}
