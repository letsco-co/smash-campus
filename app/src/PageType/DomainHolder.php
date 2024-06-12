<?php

namespace LetsCo\PageType;

class DomainHolder extends \Page
{
    private static $allowed_children = [
        DomainPage::class
    ];
}
