<?php

namespace LetsCo\Extension;

use LetsCo\Email\BrevoEmailProvider;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Extension;

class NewsletterEmailList extends Extension
{
    public function saveToList(&$data)
    {
        $email = new BrevoEmailProvider();
        $contact = $email->getOrCreateContact($data['Email']);
        $email->addContactToList(Environment::getEnv('BREVO_NEWSLETTER_LIST_ID'), $data['Email']);
    }
}
