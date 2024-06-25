<?php

namespace LetsCo\PageType;

class MaintenancePage extends \Page
{
    const RESPONSE_CODE = 503;
    public function canCreate($member = null, $context = [])
    {
        return !MaintenancePage::get()->exists();
    }
}
