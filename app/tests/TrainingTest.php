<?php

namespace tests;

use LetsCo\Model\Training\Training;
use SilverStripe\Dev\SapphireTest;

class TrainingTest extends SapphireTest
{
    protected static $fixture_file ="TrainingTest.yml";

    public function testLink()
    {
        $training = $this->objFromFixture(Training::class, 'testTraining');
        $this->assertEquals('/domain/test', $training->Link());
    }
}
