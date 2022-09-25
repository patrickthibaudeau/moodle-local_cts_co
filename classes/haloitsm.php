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
    public function get_users($name)
    {
        global $CFG;
        return $this->get_data('Users', 'GET', 'search=' . $name . '&site_id=' . $CFG->halo_site_id);
    }

    /**
     * Returns user object based on username
     * @param $username
     * @return false|mixed
     */
    public function get_user_by_username($username)
    {
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

    /**
     * Returns all Ticket Types
     * @return mixed|void
     */
    public function get_ticket_types()
    {
        global $CFG;
        return $this->get_data('TicketType', 'GET', '');
    }

    /**
     * Returns Ticket Type object
     * @param $id
     * @return mixed|void
     */
    public function get_ticket_type($id)
    {
        global $CFG;
        return $this->get_data('TicketType/' . $id, 'GET', '');
    }

    /**
     * Returns all Teams
     * @return mixed|void
     */
    public function get_teams()
    {
        global $CFG;
        return $this->get_data('Team', 'GET', '');
    }

    /**
     * Returns Team object
     * @param $id
     * @return mixed|void
     */
    public function get_team($id)
    {
        global $CFG;
        return $this->get_data('Team/' . $id, 'GET', '');
    }

    /**
     * Returns all Statuses
     * @return mixed|void
     */
    public function get_statuses()
    {
        global $CFG;
        return $this->get_data('Status', 'GET', '');
    }

    /**
     * Returns Status object
     * @param $id
     * @return mixed|void
     */
    public function get_status($id)
    {
        global $CFG;
        return $this->get_data('Status/' . $id, 'GET', '');
    }

    public function create_ticket($username, $summary, $details)
    {
        global $CFG;
        $token = $this->authenticate();
        if ($token) {
            $headers = array(
                "Content-type: application/x-www-form-urlencoded",
                'Accept: application/form-data',
                "Authorization: Bearer $token",
            );
            $user = $this->get_user_by_username($username);
            $date_occurred = date("Y-d-m\TH:i:s\Z", (time() + 18000));
            $data = 'dateoccurred=' . $date_occurred;
            $data .= '&summary=' . $summary;
            $data .= '&details=' . $details;
            $data .= '&tickettype_id=29'; // Computers, Printers & Hardware;
            $data .= '&status_id=1';
            $data .= '&client_id=' . $user->client_id;
            $data .= '&client_name=' . $user->client_name;
            $data .= '&site_id=' . $user->site_id;
            $data .= '&site_name=' . $user->site_name;
            $data .= '&user_id=' . $user->id;
            $data .= '&user_name=' . $user->name;
            $data .= '&team=105'; // UIT - CTS - Orders/Deployment
            $data .= '&category_1=7'; // UIT - CTS - Orders/Deployment
            $data .= '&category_2=178'; // UIT - CTS - Orders/Deployment
            print_object($data);
            $ticket_id = self::send_curl_request('POST', $headers, $CFG->halo_api_url . 'Tickets' , $data);

            return $ticket_id;
        }
    }
}