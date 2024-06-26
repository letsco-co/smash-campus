<?php

namespace LetsCo\Extension;

use LetsCo\Email\BrevoEmailProvider;
use LetsCo\Model\Training\Training;
use SilverStripe\Core\Environment;
use SilverStripe\ORM\DataExtension;

class EventNotification extends DataExtension
{
    private static $db = [
        'ListId' => "Int"
    ];

    public function onAfterPublish(&$original)
    {
        if (!$this->owner->ListId){
            $email = new BrevoEmailProvider();
            $folderId = get_class($this->owner) == Training::class ? Environment::getEnv('BREVO_TRAINING_FOLDER_ID') : Environment::getEnv('BREVO_MEETING_FOLDER_ID');
            $listId = $email->createList($this->owner->Title, $folderId);
            $this->owner->ListId = $listId['id'] ?? 0;
            $this->owner->write();
            $this->owner->publishSingle();
        }
    }
}
