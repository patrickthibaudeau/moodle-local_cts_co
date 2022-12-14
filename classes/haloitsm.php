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
            $headers = self::get_headers('GET', $token);
            $request_url = $CFG->halo_api_url . $function .  $params;
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
        return $this->get_data('Users', 'GET', '?search=' . $name . '&site_id=' . $CFG->halo_site_id);
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

    public function get_user_by_id($id) {
        global $CFG;
        return $this->get_data('Users/', 'GET', $id);
    }

    public function get_agent_by_id($id) {
        global $CFG;
        return $this->get_data('Agent/', 'GET', $id);
    }

    /**
     * Returns all users based on name entered
     * @param string $name
     * @return array
     */
    public function get_ticket($ticket_id)
    {
        global $CFG;
        return $this->get_data('Tickets/', 'GET',  $ticket_id);
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

    /**
     * Create aticket in HALO
     * Returns stdClass object
     * @param $username
     * @param $summary
     * @param $details
     * @return false|mixed
     */
    public function create_ticket($username, $summary, $details)
    {
        global $CFG;
        $token = $this->authenticate();

        $user = $this->get_user_by_username($username);

        if ($token && is_object($user)) {
            $data = [
                'summary' => $summary,
                'details' => $details,
                'tickettype_id' => 29, // Computers, Printers  Hardware;
                'status_id' => 1,
                'client_id' => $user->client_id,
                'client_name' => $user->client_name,
                'site_id' => $user->site_id,
                'site_name' => $user->site_name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'team' => 'UIT - CTS - Orders/Deployment',
                'category_3' => 'Hardware>Purchase - Quotation', // UIT - CTS - Orders/Deployment
            ];

            $data = "[" . json_encode($data) . "]";

            $headers = self::get_headers('POST', $token);
            $new_ticket = self::send_curl_request('POST', $headers, $CFG->halo_api_url . 'Tickets', $data);

            return json_decode($new_ticket);
        } else {
            return false;
        }
    }

    /**
     * @param $username
     * @param $ticket_id
     * @param $note
     * @return false|mixed
     */
    public function add_action($username, $ticket_id, $note)
    {
        global $CFG;
        $token = $this->authenticate();
        $user = $this->get_user_by_username($username);

        if ($token && is_object($user)) {
            $data = [
                'ticket_id' => $ticket_id,
                'note_html' => $note,
                'outcome' => "Status updated",
                'who_agentid' => $user->id,
                'note' => $note,
                'emailbody' => $note,
                'emailsubject' => "Status updated",
                'emailbody_html' => $note
            ];

            $data = "[" . json_encode($data) . "]";

            $headers = self::get_headers('POST', $token);
            $new_action = self::send_curl_request('POST', $headers, $CFG->halo_api_url . 'Actions', $data);

            return json_decode($new_action);
        } else {
            return false;
        }
    }
}