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
            $headers = self::get_headers($token, 'GET');
            $request_url = $CFG->halo_api_url . $function . $params;
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

    public function get_user_by_id($id)
    {
        global $CFG;
        return $this->get_data('Users/', 'GET', $id);
    }

    public function get_agent_by_id($id)
    {
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
        return $this->get_data('Tickets/', 'GET', $ticket_id);
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

            $headers = self::get_headers($token, 'POST');
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

            $headers = self::get_headers($token, 'POST');
            $new_action = self::send_curl_request('POST', $headers, $CFG->halo_api_url . 'Actions', $data);

            return json_decode($new_action);
        } else {
            return false;
        }
    }

    /**
     * Get all actions for a ticket
     * @param $ticket_id
     * @return false|mixed
     */
    public function get_actions($ticket_id)
    {
        global $CFG;
        $token = $this->authenticate();
        return $this->get_data('Actions', 'GET', '?ticket_id=' . $ticket_id);
    }

    /**
     * @return array
     */
    public function get_cts_statuses()
    {
        global $CFG;
        $token = $this->authenticate();
        $statuses = array();
        $statuses[] = $this->get_status(1);
        $statuses[] = $this->get_status(44);
        $statuses[] = $this->get_status(48);
        $statuses[] = $this->get_status(17);
        $statuses[] = $this->get_status(18);
        $statuses[] = $this->get_status(5);
        $statuses[] = $this->get_status(47);
        $statuses[] = $this->get_status(46);
        $statuses[] = $this->get_status(8);

        return $statuses;
    }

    /**
     * Convert date to timestamp
     * @param $date
     * @return false|int
     * @throws \Exception
     */
    public function convert_halo_date_to_timestamp($date)
    {
        global $CFG;

        $diff = $CFG->halo_timezone_adjustment * 3600; //3600 = seconds in an hour
        $timestamp = strtotime($date) - $diff;
        $date_time = \DateTime::createFromFormat('U', (int)$timestamp);
        $date_time->setTimezone(new \DateTimeZone($CFG->timezone));
        return strtotime($date_time->format('Y-m-d H:i:s'));
    }

    /**
     * Given the number of seconds, convert to days, hours, minutes, seconds
     * @param $seconds
     * @return void
     */
    public function convert_seconds_to_days($seconds)
    {
        $day = floor($seconds / (24 * 3600));
        $day = (int)$day;

        $seconds = ($seconds % (24 * 3600));
        $hour = $seconds / 3600;
        $hour = (int)$hour;

        $seconds %= 3600;
        $minutes = $seconds / 60;
        $minutes = (int)$minutes;

        $seconds %= 60;
        $seconds = (int)$seconds;

        return "$day day(s) $hour hour(s) $minutes minute(s) $seconds second(s)";

    }

    /**
     * Returns timeline data for a ticket
     * @return \stdClass
     * @throws \Exception
     */
    public function get_timeline($ticket_id)
    {
        // get the actions for the ticket
        $actions = $this->get_actions($ticket_id);
        // Put actions in decending order
        $actions_reversed = array_reverse($actions->actions);
        $timeline = array();
        $i = 0;
        foreach ($actions_reversed as $action) {
            if ($i > 0) {
                if (($action->new_status != $actions_reversed[$i - 1]->new_status)) {
                    if (isset($action->datetime)){
                        $timeline[$i]['date'] = date(
                            'M j, Y h:i A',
                            $this->convert_halo_date_to_timestamp($action->datetime)
                        );
                        $timeline[$i]['timestamp'] = $this->convert_halo_date_to_timestamp($action->datetime);
                    } else {
                        $timeline[$i]['date'] = '';
                        $timeline[$i]['timestamp'] = 0;
                    }
                    $timeline[$i]['content'] = $action->new_status_name;
                    $timeline[$i]['status_id'] = $action->new_status;
                }
            } else {
                if (isset($action->datetime)){
                    $timeline[$i]['date'] = date(
                        'M j, Y h:i A',
                        $this->convert_halo_date_to_timestamp($action->datetime)
                    );
                    $timeline[$i]['timestamp'] = $this->convert_halo_date_to_timestamp($action->datetime);
                } else {
                    $timeline[$i]['date'] = '';
                    $timeline[$i]['timestamp'] = 0;
                }
                $timeline[$i]['content'] = $action->new_status_name;
                $timeline[$i]['status_id'] = $action->new_status;
            }
            $i++;
        }
        //Reset array keys
        $timeline = array_values($timeline);
        // Remove blanks, in progress, updated
        foreach ($timeline as $key => $value) {
            if (
                ($value['status_id'] == 0) ||
                ($value['status_id'] == 2) ||
                ($value['status_id'] == 21)
            ) {
                unset($timeline[$key]);
            }
        }

        // Calculate time taken
        $timeline = array_values($timeline);

        // Reiterate to remove any anomilies
        $remove_keys = array();
        foreach ($timeline as $key => $value) {
            if (
                (($key > 0) && ($value['status_id'] == $timeline[$key - 1]['status_id']))
            ) {
                $remove_keys[] = $key;
            }
        }

        foreach($remove_keys as $key){
            unset($timeline[$key]);
        }
        // Reset array keys
        $timeline = array_values($timeline);

        if (count($timeline) > 1) {
            $end_key = count($timeline) - 1;
            $time_taken = $timeline[$end_key]['timestamp'] - $timeline[0]['timestamp'];
        } else {
            $time_taken = 0;
        }

        $data = new \stdClass();
        $data->timeline = $timeline;
        $data->time_taken = $time_taken;

        return $data;
    }
}