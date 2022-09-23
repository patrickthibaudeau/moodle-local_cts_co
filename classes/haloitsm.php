<?php

namespace local_cts_co;

use core\notification;

class haloitsm extends webservice
{

    /**
     * Returns token if authenticated to HALO ITSM API
     * @return string
     */
    protected function authenticate()
    {
        global $CFG;
        // Required connection data for HALO AUTH
        $data = [
            'tenant' => $CFG->halo_tenant,
            'grant_type' => 'client_credentials',
            'client_id' => $CFG->halo_client_id,
            'client_secret' => $CFG->halo_client_secret,
            'scope' => 'all'
        ];
print_object($data);
        $url = $CFG->halo_auth_url;

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            return false;
        } else {
            $result_array = json_decode($result);
            return $result_array->access_token;
        }
    }

    /**
     * Returns API response
     * @param $method
     * @param $params
     * @return bool|string|void
     */
    protected function get_data($method, $params = [])
    {
        global $CFG;
        $token = $this->authenticate();
        if ($token) {
            $query = http_build_query($params);
            $request_url = $CFG->halo_api_url . $method . $params;

            $curl = curl_init($request_url);
            curl_setopt($curl, CURLOPT_URL, $request_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer $token",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);
            // Return object
            return json_decode($response);
        }
    }

    public function get_users() {
        return $this->get_data('Users', []);
    }
}