<?php

include_once 'Logger.php';

/**
 * A thin CSV file reader.
 *
 * User: Rafael da Silva Ferreira
 * Date: 5/13/17
 * Time: 20:17
 */
final class MailChimpSubscriber
{
    /**
     * Curl URL of MailChimp.
     */
    private const MAIL_CHIMP_URL_FORMAT = 'https://%s.api.mailchimp.com/3.0/lists/%s/members/%s';

    /**
     * MailChimp API Key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Email list ID.
     *
     * @var string
     */
    private $listId;

    /**
     * Logger implementation.
     *
     * @var Logger
     */
    private $logger;

    /**
     * MailChimpSender constructor.
     *
     * @param string $apiKey
     * @param string $listId
     * @param Logger|null $logger
     */
    function __construct(string $apiKey, string $listId, Logger $logger = null)
    {
        $this->apiKey = $apiKey;
        $this->listId = $listId;
        $this->logger = $logger;
    }

    /**
     * Send emails of MailChimp from a list of $receivers.
     *
     * @param array $receivers
     */
    function send(array $receivers)
    {
        while ($email = array_pop($receivers)) {
            $statusCode = $this->subscribeEmail($email);

            if ($this->logger != null) {
                $this->logMailResult($email, $statusCode);
            }
        }
    }

    private function subscribeEmail($email)
    {
        $json = $this->getJSON($email);

        $ch = curl_init($this->getURL($email));

        curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthentication());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $statusCode;
    }

    private function getJSON($email)
    {
        return [
            'email_address' => $email,
            'status'        => 'cleaned'
        ];
    }

    private function getURL($email)
    {
        $memberId = $this->getMemberId($email);
        $areaPosition = strpos($this->apiKey, '-') + 1;
        $dataCenter = substr($this->apiKey, $areaPosition);

        return sprintf(self::MAIL_CHIMP_URL_FORMAT, $dataCenter, $this->listId, $memberId);
    }

    private function getMemberId($email)
    {
        return md5(strtolower($email));
    }

    private function getAuthentication()
    {
        return 'user:' . $this->apiKey;
    }

    private function getHeaders()
    {
        return [
            'Content-Type: application/json'
        ];
    }

    private function logMailResult(string $email, int $statusCode)
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            $this->logger->success("Subscribe email: $email.");
        } else if ($statusCode > 300 && $statusCode < 400) {
            $this->logger->warning("Something wrong when we try subscribe: $email. ($statusCode).");
        } else {
            $this->logger->error("Email not subscribe: $email. ($statusCode).");
        }
    }
}