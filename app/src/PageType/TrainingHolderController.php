<?php

namespace LetsCo\PageType;

use LetsCo\Model\Training\TrainingCategory;
use PageController;
use SilverStripe\Control\HTTPRequest;

class TrainingHolderController extends PageController
{
    public function index(HTTPRequest $request) {
        $categories = TrainingCategory::get();
        if (!$categories->exists()) {
            return $this->httpError(404, 'Domain not found');
        }
        return $this->customise([
            'Layout' => $this
                ->customise(['categories' => $categories])
                ->renderWith(['TrainingHolder']),
        ])->renderWith(['Page']);
    }
}
