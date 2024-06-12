<?php

namespace LetsCo\Admin\Meeting;

use LetsCo\Model\Meeting\Meeting;
use SilverStripe\Admin\ModelAdmin;

class MeetingAdmin extends ModelAdmin
{
    private static $managed_models = [
        'meeting' => [
            'dataClass' => Meeting::class,
            'title' => Meeting::class,
        ],
    ];
    private static $url_segment = 'meetings';

    private static $menu_title = 'My Meeting Admin';

    private static $menu_icon_class = 'font-icon-monitor';
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
