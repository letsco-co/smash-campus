<?php

namespace LetsCo\Email;

use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\AddContactToList;
use Brevo\Client\Model\CreateContact;
use Brevo\Client\Model\CreateList;
use Brevo\Client\Model\SendSmtpEmail;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use LetsCo\Interface\EmailProvider;
use phpDocumentor\Reflection\Types\False_;
use SilverStripe\Core\Environment;
use SilverStripe\SiteConfig\SiteConfig;

class BrevoEmailProvider implements EmailProvider
{

    public function send($to, $templateID, $params)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));
        $config = Configuration::getDefaultConfiguration()->setApiKey('partner-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
        $siteConfig = SiteConfig::current_site_config();
        $email = $siteConfig->Email ?? 'support@letsco.co';
        $sender = ['name' => $email, 'email' => $email];
        if ($siteConfig->Title) {
            $sender['name'] = $siteConfig->Title;
        }
        $sendSmtpEmail = new SendSmtpEmail([
            'sender' => $sender,
            'replyTo' => $sender,
            'to' => $to,
            'params' => $params,
            'templateId' => $templateID,
        ]);
        return $apiInstance->sendTransacEmail($sendSmtpEmail);
    }



    public function createContact($email, $list= null)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(
            new Client(),
            $config
        );
        $createContact = new CreateContact(); // Values to create a contact
        $createContact['email'] = $email;
        if (is_int($list)) {
            $list = [$list];
        }
        if ($list) {
            $createContact['listIds'] = $list;
        }

        return $apiInstance->createContact($createContact);
    }

    public function createList($listName, $folderId)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(
            new Client(),
            $config
        );
        $createList = new CreateList();
        $createList['name'] = $listName;
        $createList['folderId'] = $folderId;

        try {
            return $apiInstance->createList($createList);
        } catch (Exception $e) {
            echo 'Exception when calling ContactsApi->createFolder: ', $e->getMessage(), PHP_EOL;
            return false;
        }
    }

    public function addContactToList($listId, $contactEmail)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(
            new Client(),
            $config
        );
        $contactIdentifiers = new AddContactToList();
        $contactIdentifiers['emails'] = [$contactEmail];

        try {
            return $apiInstance->addContactToList($listId, $contactIdentifiers);
        } catch (Exception $e) {
            echo 'Exception when calling ContactsApi->addContactToList: ', $e->getMessage(), PHP_EOL;
            return false;
        }
    }

    public function getContact($email)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new ContactsApi(
            new Client(),
            $config
        );

        return $apiInstance->getContactInfo($email);
    }

    public function getOrCreateContact(string $email, $listId = null)
    {
        try {
            return $this->getContact($email);
        } catch (ApiException $e) {
            if ($e->getCode() == 404) {
                return $this->createContact($email, $listId);
            }
            return false;
        }
    }
}
