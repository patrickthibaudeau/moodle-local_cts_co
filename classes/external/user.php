<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once("$CFG->dirroot/config.php");

class local_cts_co_external_user extends external_api
{
    //**************************** GET USERS **********************
    /**
     * Returns users parameters
     * @return external_function_parameters
     */
    public static function get_users_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'User id', false, -1),
                'name' => new external_value(PARAM_TEXT, 'User first or last name', false)
            )
        );
    }

    /**
     * Returns users
     * @return string users
     * @global moodle_database $DB
     */
    public static function get_users($id, $name = "")
    {
        global $DB;

        $params = self::validate_parameters(self::get_users_parameters(), array(
                'id' => $id,
                'name' => $name
            )
        );

        if (strlen($name) >= 3) {
            $sql = "select * from {user} u where ";
            $name = str_replace(' ', '%', $name);
            $sql .= " (Concat(u.firstname, ' ', u.lastname ) like '%$name%' or (u.idnumber like '%$name%') or (u.email like '%$name%') or (u.username like '%$name%'))"; //How the ajax call with search via the form autocomplete
            $sql .= " Order by u.lastname";
            //How the ajax call with search via the form autocomplete
            $mdlUsers = $DB->get_records_sql($sql, array($name));
        } else {
            $mdlUsers = [];
        }

        $users = [];
        $i = 0;
        foreach ($mdlUsers as $u) {
            $users[$i]['id'] = $u->id;
            $users[$i]['firstname'] = $u->firstname;
            $users[$i]['lastname'] = $u->lastname;
            $users[$i]['email'] = $u->email;
            $users[$i]['idnumber'] = $u->idnumber;
            $i++;
        }
        return $users;
    }

    /**
     * Get Users
     * @return single_structure_description
     */
    public static function user_details()
    {
        $fields = array(
            'id' => new external_value(PARAM_INT, 'Record id', false),
            'firstname' => new external_value(PARAM_TEXT, 'User first name', true),
            'lastname' => new external_value(PARAM_TEXT, 'User last name', true),
            'email' => new external_value(PARAM_TEXT, 'email', true),
            'idnumber' => new external_value(PARAM_TEXT, 'ID Number', true)
        );
        return new external_single_structure($fields);
    }

    /**
     * Returns users result value
     * @return external_description
     */
    public static function get_users_returns()
    {
        return new external_multiple_structure(self::user_details());
    }
}
