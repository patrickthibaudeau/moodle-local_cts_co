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
        // Set Headers
        $options = [
            "Content-type: application/x-www-form-urlencoded",
            'Accept: application/form-data'
        ];
        // Set POST data
        $data = 'tenant=' . $CFG->halo_tenant .
            '&grant_type=client_credentials' .
            '&client_id=' . $CFG->halo_client_id .
            '&client_secret=' . $CFG->halo_client_secret .
            '&scope=all';
        // Authenticate
        $result = self::send_curl_request('POST', $options, $CFG->halo_auth_url, $data);
        $result_array = json_decode($result);
        return $result_array->access_token;
    }

    /**
     * Retrieve data from the API
     * @param $function
     * @param $method
     * @param $params
     * @return mixed|void
     */
    protected function get_data($function, $method = 'GET', $params = '')
    {
        global $CFG;
        $token = $this->authenticate();
        if ($token) {
            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer $token",
            );

            $request_url = $CFG->halo_api_url . $function . '?' . $params;
            $result = self::send_curl_request($method, $headers, $request_url, $params);

            return json_decode($result);
        }
    }

    /**
     * Returns all users based on name entered
     * @param string $name
     * @return array
     */
    public function get_users($name) {
        global $CFG;
        return $this->get_data('Users', 'GET', 'search=' . $name . '&site_id=' . $CFG->halo_site_id);
    }

    /**
     * Returns user object based on username
     * @param $username
     * @return false|mixed
     */
    public function get_user_by_username($username) {
        $results = $this->get_users($username);
        $users = $results->users;
        // Only return the user if the login matches the username
        foreach ($users as $user) {
            if ($user->login == $username) {
                return $user;
            }
        }

        return false;
    }
}