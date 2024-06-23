<?php

namespace LetsCo\Admin;

use LetsCo\Model\OfferTrainingIdea;
use SilverStripe\Admin\ModelAdmin;

class OfferTrainingIdeaAdmin extends ModelAdmin
{
    private static $managed_models = [
        'offerTraining' => [
            'dataClass' => OfferTrainingIdea::class,
            'title' => OfferTrainingIdea::class,
        ],
    ];
    private static $url_segment = 'offer-training';

    private static $menu_title = 'My Offer Admin';

    private static $menu_icon_class = 'font-icon-plus';
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
