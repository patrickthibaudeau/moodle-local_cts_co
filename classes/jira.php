<?php

namespace local_cts_co;

class jira extends webservice
{
    /**
     * Returns token if authenticated to HALO ITSM API
     * @return string
     */
    protected function authenticate()
    {
        global $CFG;

        return $CFG->jira_personal_access_token;
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

            $request_url = $CFG->jira_api_url . $function . $params;
            $result = self::send_curl_request($method, $headers, $request_url, $params);

            return json_decode($result);
        }
    }

    /**
     * Returns all users based on name entered
     * @param string $name
     * @return array
     */
    public function get_issue($issue_id)
    {
        global $CFG;
        return $this->get_data('issue/', 'GET', $issue_id);
    }
}