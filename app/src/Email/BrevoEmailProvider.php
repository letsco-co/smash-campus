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
use LetsCo\Model\Event;
use phpDocumentor\Reflection\Types\False_;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\SiteConfig\SiteConfig;

class BrevoEmailProvider implements EmailProvider
{

    public function send($to, $templateID, $params, $attachment= null)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', Environment::getEnv('BREVO_API_KEY'));
        $config = Configuration::getDefaultConfiguration()->setApiKey('partner-key', Environment::getEnv('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
        $siteConfig = SiteConfig::current_site_config();
        $email = $siteConfig->Email ?? 'support@letsco.ovh';
        $sender = ['name' => $email, 'email' => $email];
        if ($siteConfig->Title) {
            $sender['name'] = $siteConfig->Title;
        }
        $sendParams = [
            'sender' => $sender,
            'replyTo' => $sender,
            'to' => $to,
            'params' => $params,
            'templateId' => $templateID,
        ];
        if ($attachment) {
            $sendParams['attachment'] = $attachment;
        }
        $sendSmtpEmail = new SendSmtpEmail($sendParams);
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
        $createList['name'] = strlen($listName) > 50 ? ($listName) : substr($listName, 0, 47) . '...';
        $createList['folderId'] = $folderId;

        try {
            return $apiInstance->createList($createList);
        } catch (Exception $e) {
            Injector::inst()->get(LoggerInterface::class)->error($e);
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
            Injector::inst()->get(LoggerInterface::class)->error($e);
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
