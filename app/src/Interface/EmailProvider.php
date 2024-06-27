<?php

namespace LetsCo\Interface;

interface EmailProvider
{
    public function send($to, $templateID, $params);
    public function createContact($email, $list= null);
    public function createList($listName, $folderId);
    public function addContactToList($listId, $contactEmail);
    public function getContact($email);
    public function getOrCreateContact(string $email, $listId = null);
}
