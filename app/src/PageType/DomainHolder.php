<?php

namespace LetsCo\PageType;

class DomainHolder extends \Page
{
    private static $description = 'Parent page to display domains';
    private static $allowed_children = [
        DomainPage::class
    ];
}
