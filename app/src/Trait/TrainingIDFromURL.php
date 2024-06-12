<?php

namespace LetsCo\Trait;

use LetsCo\Model\Training\Training;

trait TrainingIDFromURL
{
    /**
     * @return int
     */
    public function getTrainingID(): int
    {
        $trainingURLSegment = $this->getForm()->getRequestHandler()->getRequest()->param("Action");
        $training = Training::get()->filter("URLSegment", $trainingURLSegment)->first();
        return $training->ID ?? 0;
    }
}
