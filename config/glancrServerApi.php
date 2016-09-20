<?php
/**
 * Handles communication with the glancr server API.
 */

namespace glancr;


class glancrServerApi
{
    //API base url
    private $url;

    function __construct($baseurl)
    {
        // @TODO: This is not really OOP, but works for now. Maybe refactor Config as a class and use DI?
        require_once 'glancrConfig.php';
        $this->url = $baseurl;
    }

    /**
     * Retrieves the current local IP address of the host device for the configured connection type.
     * @return string The current IP address.
     */
    private function getCurrentIp()
    {
        $connectionType = getConfigValue('connectionType');
        // DEBUG
        //$ip = '192.168.1.36';
        exec('ip -f inet -o addr show ' . $connectionType . '0|cut -d\  -f 7 | cut -d/ -f 1', $ip);
        return $ip;
    }

    /**
     * Triggers an email of
     * @param $type string The type of email to send. Currently available: setup, change, reset, update
     * @return mixed|string Message whether the request succeeded of failed.
     */
    public function triggerMail($type)
    {
        //Initiate cURL.
        $ch = curl_init($this->url . '/mailer/mail/');

        //The JSON data.
        $jsonData = array(
            'name' => getConfigValue('firstname'),
            'email' => getConfigValue('email'),
            'localip' => $this->getCurrentIp(),
            'type' => $type,
            'language' => getConfigValue('language')
        );

        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);

        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $mailTries = 0;
        $result = '';

        while (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            $result = curl_exec($ch);
            $mailTries++;
            usleep(300000);
            if ($mailTries == 5) {
                error_log("mail could not be sent " . print_r($jsonData));
            }
        }

        return $result;
    }
}