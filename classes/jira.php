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
            $headers = self::get_headers('GET', $token);
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
        $jira_issue = $this->get_data('issue/', 'GET', $issue_id);

        if (!isset($jira_issue->errorMessages[0])) {
//            print_object($jira_issue);
            $issue = new \stdClass();
            $issue->id = $jira_issue->id;
            $issue->key = $jira_issue->key;
            $issue->summary = $jira_issue->fields->summary;
            $issue->description = $jira_issue->fields->description;
            $issue->lastviewed = strtotime($jira_issue->fields->lastViewed);
            $issue->created = strtotime($jira_issue->fields->created);
            $issue->updated = strtotime($jira_issue->fields->updated);
            $issue->duedate = strtotime($jira_issue->fields->duedate);
            $issue->lastviewed_hr = strftime(get_string('strftimedatetime'), strtotime($jira_issue->fields->lastViewed));
            $issue->created_hr = strftime(get_string('strftimedatetime'), strtotime($jira_issue->fields->created));
            $issue->updated_hr = strftime(get_string('strftimedatetime'), strtotime($jira_issue->fields->updated));
            if ($issue->duedate) {
                $issue->duedate_hr = strftime(get_string('strftimedate'), $issue->duedate);
            } else {
                $issue->duedate_hr = '';
            }
            $issue->project = new \stdClass();
            $issue->project->id = $jira_issue->fields->project->id;
            $issue->project->key = $jira_issue->fields->project->key;
            $issue->project->name = $jira_issue->fields->project->name;
            $issue->project->name = $jira_issue->fields->project->name;
            $issue->priority = $jira_issue->fields->priority->name;
            $issue->status = $jira_issue->fields->status->name;
            // Only add agent if assignee exists
            if (isset($jira_issue->fields->assignee->name)) {
                $issue->agent = $jira_issue->fields->assignee->name;
                $issue->agent_email = $jira_issue->fields->assignee->emailAddress;
                $issue->agent_display_name = $jira_issue->fields->assignee->displayName;
            } else {
                $issue->agent = '';
                $issue->agent_email = '';
                $issue->agent_display_name = '';
            }

            $issue->comments = $jira_issue->fields->comment->comments;
            $issue->worklog = $jira_issue->fields->worklog;
            return $issue;
        }
        return false;

    }

    public function create_issue($summary, $description)
    {
        global $CFG;

        $token = $this->authenticate();

        $data = [
            'fields' => [
                'project' => [
                    'key' => trim($CFG->jira_project_key)
                ],
                'summary' => $summary,
                'description' => $description,
                'issuetype' => [
                    'name' => trim($CFG->jira_issue_type)
                ]
            ]
        ];

        $data = json_encode($data);

        $headers = self::get_headers('POST', $token);

        $new_issue = self::send_curl_request('POST', $headers, $CFG->jira_api_url . 'issue/', $data);

        return json_decode($new_issue);
    }
}