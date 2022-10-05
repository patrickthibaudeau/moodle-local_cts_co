<?php
namespace local_cts_co;

use local_cts_co\request;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/config.php');

class request_form extends \moodleform
{

    protected function definition()
    {
        global $DB;

        $formdata = $this->_customdata['formdata'];
        // Create form object
        $mform = &$this->_form;

        $user_options = [
            'multiple' => false,
            'ajax' => 'local_cts_co/user_selector',
            'noselectionstring' => get_string('user')
        ];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        //Header: General
        $mform->addElement('header', 'request_data', get_string('request_header', 'local_cts_co'));

        // Request for : userid
        $mform->addElement('autocomplete', 'userid', get_string('requested_for', 'local_cts_co'), [], $user_options);
        $mform->setType('userid', PARAM_INT);

        // Existing HALO ticket
        $mform->addElement('text', 'halo_ticket_id', get_string('halo_ticket_id', 'local_cts_co'), '');
        $mform->setType('halo_ticket_id', PARAM_TEXT);

        // Summary
        $mform->addElement('text', 'summary', get_string('summary', 'local_cts_co'), '');
        $mform->addHelpButton('summary', 'summary', 'local_cts_co');
        $mform->setType('summary', PARAM_TEXT);
        $mform->addRule('summary', get_string('required_field', 'local_cts_co'), 'required');

        $mform->addElement('editor', 'description_editor', get_string('description','local_cts_co' ));
        $mform->addHelpButton('description_editor', 'description', 'local_cts_co');
        $mform->setType('description_editor', PARAM_RAW);
        $mform->addRule('description_editor', get_string('required_field', 'local_cts_co'), 'required');

        $this->add_action_buttons();
        $this->set_data($formdata);
    }

    // Perform some extra moodle validation
    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

//        if (is_null($data['id'])) {
//            $id = -1;
//        } else {
//            $id = $data['id'];
//        }
//
//        if ($data['yulearncategoryid'] == 0) {
//            $errors['yulearncategoryid'] = get_string('field_required', 'local_yulearn');
//        }
//
//        if ($data['id'] < 1) {
//            $sql = 'SELECT * FROM {yulearn_course} WHERE shortname = "'
//                . trim($data['shortname']) . '" AND '
//                . 'id != ' . $id;
//            if ($foundcourses = $DB->get_records_sql($sql)) {
//
//                if (!empty($foundcourses)) {
//                    foreach ($foundcourses as $foundcourse) {
//                        $foundcoursenames[] = $foundcourse->fullname;
//                    }
//                    $foundcoursenamestring = implode(',', $foundcoursenames);
//                    $errors['shortname'] = get_string('shortnametaken', '', $foundcoursenamestring);
//                }
//            }
//
//            if ($foundMoodleCourse = $DB->get_record('course', ['shortname' => trim($data['shortname'])])) {
//                $errors['shortname'] = get_string('shortnametaken');
//            }
//
//
//            $sql = 'SELECT * FROM {yulearn_course} WHERE externalcode = "'
//                . trim($data['externalcode']) . '" AND '
//                . 'id != ' . $id;
//            if ($foundcourses = $DB->get_records_sql($sql)) {
//
//                if (!empty($foundcourses)) {
//                    foreach ($foundcourses as $foundcourse) {
//                        $foundcoursenames[] = $foundcourse->fullname;
//                    }
//                    $foundcoursenamestring = implode(',', $foundcoursenames);
//                    $errors['externalcode'] = get_string('externalcode_taken', 'local_yulearn', $foundcoursenamestring);
//                }
//            }
//
//            if ($data['hascertificate']) {
//                if (!$data['certificatetemplateid']) {
//                    $errors['certificatetemplateid'] = get_string('required', 'local_yulearn');
//                }
//            }
//        }
//
//        // Certificate notifications
//        for ($i = 0; $i < count($data['rnotificationruleid']); $i++) {
//            if ($data['remailtemplateid'][$i] == 0) {
//                $errors['remailtemplateid'][$i] = get_string('required', 'local_yulearn');
//            }
//        }
//
//        for ($i = 0; $i < count($data['remailtemplateid']); $i++) {
//            if (isset($data['rnotificationruleid'])) {
//                if ($data['rnotificationruleid'][$i] == 0) {
//                    $errors['rnotificationruleid'][$i] = get_string('required', 'local_yulearn');
//                }
//            } else {
//                $errors['rnotificationruleid'][$i]  = get_string('required', 'local_yulearn');
//            }
//        }

        return $errors;
    }

}
