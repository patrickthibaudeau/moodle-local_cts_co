<?php

namespace local_cts_co;

abstract class webservice
{
    protected function authenticate()
    {
    }

    protected function get_data($method, $params)
    {
    }

    /**
     * @param string $method POST|GET
     * @param array $headers
     * @param string $url
     * @param string $post_fields
     * @return bool|string
     */
    protected function send_curl_request($method, $headers, $url, $post_fields = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Return header array for GET or POST
     * @param $method default 'GET' Available 'POST'
     * @param $token
     * @return string[]
     */
    protected function get_headers($token, $method = 'GET') {
        if ($method == 'GET') {
            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer $token",
            );
        } else {
            $headers = array(
                "Content-type: application/json",
                'Accept: application/json',
                "Authorization: Bearer $token",
            );
        }
        return $headers;
    }
}