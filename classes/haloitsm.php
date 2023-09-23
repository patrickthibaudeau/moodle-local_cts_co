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
    public function get_cts_statuses_names()
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
     * Array of status IDS used by CTS
     * @return array
     */
    private function get_cts_status_ids()
    {
        $statuses = array();
        $statuses[] = 1;
        $statuses[] = 44;
        $statuses[] = 48;
        $statuses[] = 17;
        $statuses[] = 18;
        $statuses[] = 5;
        $statuses[] = 47;
        $statuses[] = 46;
        $statuses[] = 8;

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
        // Set the default timezone to America/Toronto
        date_default_timezone_set($CFG->timezone);
        // Convert the GMT date to a DateTime object
        $gmt_date = new \DateTime($date, new \DateTimeZone('GMT'));
        // Set the timezone to America/Toronto
        $gmt_date->setTimezone(new \DateTimeZone($CFG->timezone));
        // Get the timestamp
        return $gmt_date->getTimestamp();
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

        $day = $day > 0 ? $day . ' day(s)' : '';
        $hour = $hour > 0 ? $hour . ' hr(s)' : '';
        $minutes = $minutes > 0 ? $minutes . ' min(s)' : '';
        $seconds = $seconds > 0 ? $seconds . ' sec(s)' : '';

        return "$day  $hour  $minutes  $seconds ";

    }

    public function x($ticket_id)
    {

        echo "=== Start Aladin testing ===<br>";
        $actions = $this->get_actions($ticket_id);
        $actions_reversed = array_reverse($actions->actions);

        $temp = array();
        $statuses = ["New", "New Request", "Quote Processing", "Awaiting Approval", "Approved", "With Supplier", "Computer Preparation", "Pending Deployment", "Resolved"];

        foreach ($actions_reversed as $action) {
            if (in_array($action->new_status_name, $statuses)) {
                array_push($temp, $action);
            }
        }
        $timeline = array();

        for ($i = 0; $i < count($temp); $i++) {
            if ($i == 0) {
                array_push($timeline, $temp[$i]);
            } else {
                if ($timeline[count($timeline) - 1]->new_status_name != $temp[$i]->new_status_name) {
                    array_push($timeline, $temp[$i]);
                }
            }
        }

        foreach ($timeline as $action) {
            echo $action->new_status_name;
            echo "|" . $action->datetime . "<br>";
        }

        echo "=== End Aladin Testing ===";

    }

    /**
     * Returns timeline data for a ticket
     * @return \stdClass
     * @throws \Exception
     */
    public function get_timeline($ticket_id)
    {
        $data = new \stdClass();
        // get the actions for the ticket
        $actions = $this->get_actions($ticket_id);
        // If no actions, return false
        if ($actions->record_count == 0) {
            $data->timeline = false;
            $data->number_of_items = 0;
            return $data;
        }

        // Put actions in decending order
        $actions_reversed = array_reverse($actions->actions);
        // Get all accepted statuses
        $accepted_statuses = $this->get_cts_status_ids();
        // Get last status in list
        $last_status_type_key = count($accepted_statuses) - 1;
        // Set variables
        $timeline = array();
        $i = 0;
        $compare_key = 0;
        // Loop through all actions and create the timeline
        foreach ($actions_reversed as $action) {
            // Only include those actions from accepted statuses
            // Always compare to the last status to avoid duplicates
            if (
                in_array($action->new_status, $accepted_statuses) &&
                ($i == 0 || $action->new_status != $actions_reversed[$compare_key]->new_status)
            ) {
                $timeline[$i]['date'] = $action->new_status_name;
                $timeline[$i]['timestamp'] = isset($action->datetime) ? $this->convert_halo_date_to_timestamp
                ($action->datetime
                ) : 0;
                $timeline[$i]['content'] = '<h5><span class="badge badge-success text-light">Completed</span></h5>';
                $timeline[$i]['content'] .= '<span style="font-size: 0.8rem;"><i class="nav-icon bi-alarm"></i> Start: ';
                $timeline[$i]['content'] .= isset($action->datetime) ? date(
                    'M j, Y h:i A',
                    $this->convert_halo_date_to_timestamp($action->datetime)
                ) : '';
                $timeline[$i]['content'] .= '</span>';
                $timeline[$i]['status_id'] = $action->new_status;
                $compare_key = $i;
            }
            $i++;
        }
        //Reset array keys
        $timeline = array_values($timeline);
        // Add time taken to last status
        for ($t = 0; $t < count($timeline); $t++) {
            if (isset($timeline[$t + 1]['timestamp'])) {
                $time_taken_for_step = $this->convert_seconds_to_days(
                    ($timeline[$t + 1]['timestamp'] - $timeline[$t]['timestamp'])
                );
                $timeline[$t]['content'] .= '<br><span style="font-size: 0.8rem;"><i class="nav-icon bi-clock-history"></i> Duration: '
                    . $time_taken_for_step . '</span>';
            } else {
                // If this is the last key and the status is not completed
                // Else if this is the last key and the status is completed
                if ($t == count($timeline) - 1 && $timeline[$t]['status_id'] != $accepted_statuses[$last_status_type_key]) {
                    $time_taken_for_step = $this->convert_seconds_to_days(
                        (time() - $timeline[$t]['timestamp'])
                    );
                    $timeline[$t]['content'] .= '<br><span style="font-size: 0.8rem;"><i class="nav-icon bi-clock-history"></i> Duration: '
                        . $time_taken_for_step . '</span>';
                } elseif ($t == count($timeline) - 1 && $timeline[$t]['status_id'] == $accepted_statuses[$last_status_type_key]) {
                    $time_taken_for_step = $this->convert_seconds_to_days(
                        ($timeline[$t]['timestamp'] - $timeline[0]['timestamp'])
                    );
                    $timeline[$t]['content'] = '<h5><span class="badge badge-success text-light">Completed</span></h5>'
                        . '<span style="font-size: 0.8rem;"><i class="nav-icon bi-alarm"></i> Resolved: '
                        . date('M j, Y h:i A', $timeline[$t]['timestamp'])
                        . '<br><span style="font-size: 0.8rem;"><i class="nav-icon bi-clock-history"></i> Ticket duration: '
                        . $time_taken_for_step . '</span>';
                }
            }
        }

        // Get last key
        if (count($timeline) > 0) {
            $last_key = count($timeline) - 1;
        } else {
            $last_key = 0;
        }

        // Get last key Status
        if (count($timeline) > 0) {
            $last_status = $timeline[$last_key]['status_id'];
        } else {
            $last_status = 0;
        }
        // Get key of accepted statuses based on $last_status id
        $status_start_key = array_search($last_status, $accepted_statuses);
//        print_object($accepted_statuses);
//        print_object($status_start_key);
        // Make the last status in timeline In Progress if not completed
        if (count($timeline) > 0 && $timeline[$last_key]['status_id'] != $accepted_statuses[$last_status_type_key]) {
            $timeline[$last_key]['content'] = str_replace(
                '<span class="badge badge-success text-light">Completed</span>',
                '<span class="badge badge-info text-light">In Progress</span>',
                $timeline[$last_key]['content']);

            // Add remaining steps in timeline
            $z = count($timeline);
            for ($x = $status_start_key + 1; $x < count($accepted_statuses); $x++) {
                $timeline[$z]['date'] = $this->get_status($accepted_statuses[$x])->name;
                $timeline[$z]['timestamp'] = 0;
                $timeline[$z]['content'] = '<h5><span class="badge badge-warning text-light">Pending</span></h5>';
                $timeline[$z]['status_id'] = $accepted_statuses[$x];
                $z++;
            }
        } else {
            // Add remaining steps in timeline
            for ($x = $i + 1; $x < count($accepted_statuses); $x++) {
                $timeline[$x]['date'] = $this->get_status($accepted_statuses[$x])->name;
                $timeline[$x]['timestamp'] = 0;
                $timeline[$x]['content'] = '<h4><span class="badge badge-warning text-light">Pending</span></h4>';
                $timeline[$x]['status_id'] = $accepted_statuses[$x];
            }
        }

        $data->timeline = $timeline;
        $data->number_of_items = count($timeline);

        return $data;
    }
}
