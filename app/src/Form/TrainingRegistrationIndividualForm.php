<?php

namespace LetsCo\Form;

use LetsCo\Form\Steps\TrainingRegistrationPersonalDetailsStep;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\MultiForm\Forms\MultiForm;

class TrainingRegistrationIndividualForm extends MultiForm
{
    private static $start_step = TrainingRegistrationPersonalDetailsStep::class;

    public function actionsFor($step)
    {
        $actions = parent::actionsFor($step);
        foreach ($actions as $action) {
            if ($action->actionName() == "prev") {
                $action->addExtraClass("btn-link btn icon-link icon-link-hover link-offset-2 link-underline link-underline-opacity-0");
                $action->setUseButtonTag(true);
                continue;
            }
            $action->addExtraClass("btn btn-primary bg-secondary-hover border-0");
            $action->setUseButtonTag(true);
        }

        return $actions;
    }
}
