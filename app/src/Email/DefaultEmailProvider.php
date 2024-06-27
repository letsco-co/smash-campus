<?php

namespace LetsCo\Email;

use LetsCo\Interface\EmailProvider;

class DefaultEmailProvider implements EmailProvider
{

    public function send($to, $templateID, $params)
    {
        return true;
    }

    public function createContact($email, $list = null)
    {
        return true;
    }

    public function createList($listName, $folderId)
    {
        return true;
    }

    public function addContactToList($listId, $contactEmail)
    {
        return true;
    }

    public function getContact($email)
    {
        return true;
    }

    public function getOrCreateContact(string $email, $listId = null)
    {
        return true;
    }
}
