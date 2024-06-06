<?php

namespace LetsCo\PageType;

use LetsCo\Model\Training\Training;
use LetsCo\Model\Training\TrainingCategory;
use PageController;
use SilverStripe\Control\HTTPRequest;

class TrainingHolderController extends PageController
{
    private static $allowed_actions = [
        'domain',
        'search',
        'show',
    ];
    public function index(HTTPRequest $request) {
        $categories = TrainingCategory::get();
        if (!$categories->exists()) {
            return $this->httpError(404, 'Domain not found');
        }
        $trainings = Training::get()->sort('Category.Title');
        return $this->customise([
            'Layout' => $this
                ->customise([
                    'Categories' => $categories,
                    'Trainings' => $trainings,
                ])
                ->renderWith(['TrainingHolder']),
        ])->renderWith(['Page']);
    }

    public function domain(HTTPRequest $request) {
        $domainID = $request->param('ID');
        $domain = TrainingCategory::get()->filter('URLSegment', $domainID)->first();

        if (!$domain) {
            return $this->httpError(404, 'Domain not found');
        }
        $trainings = Training::get()->filter('CategoryID', $domain->ID);
        $domains = TrainingCategory::get()->exclude('ID', $domain->ID);
        $this->Page($this->Link())->Title = $domain->Title .' | '.$this->Page($this->Link())->Title . ' - ' . $this->SiteConfig()->Title;

        return $this->customise([
            'Layout' => $this
            ->customise([
                'Title' => $domain->Title,
                'Trainings' => $trainings,
                'Category' => $domain,
                'Categories' => $domains,
            ])
            ->renderWith(['TrainingHolder_domain']),
        ])->renderWith(['Page']);
    }

    public function search(HTTPRequest $request) {
        $keyword = $request->getVar('keyword');
        $trainings = Training::get()->filterAny([
            'Title:PartialMatch' => $keyword,
            'Category.Title:PartialMatch' => $keyword
        ]);

        return $this->customise([
            'Layout' => $this
                ->customise([
                    'Trainings' => $trainings,
                    'Keyword' => $keyword
                ])
                ->renderWith(['TrainingHolder_search']),
        ])->renderWith(['Page']);
    }

    public function show(HTTPRequest $request) {
        $trainingID = $request->param('ID');
        $training = Training::get()->filter('URLSegment', $trainingID)->first();
        if (!$training) {
            return $this->httpError(404, 'Training not found');
        }
        $this->Page($this->Link())->Title = $training->Title .' | '.$this->Page($this->Link())->Title . ' - ' . $this->SiteConfig()->Title;
        return $this->customise([
            'Layout' => $this
                ->customise([
                    'Training' => $training,
                ])
                ->renderWith(['TrainingHolder_show']),
        ])->renderWith(['Page']);
    }
}
