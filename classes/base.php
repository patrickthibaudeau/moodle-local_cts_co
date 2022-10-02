<?php
/**
 * *************************************************************************
 * *                           YULearn ELMS                               **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  yulearn                                                   **
 * @name        YULearn ELMS                                              **
 * @copyright   UIT - Innovation lab & EAAS                               **
 * @link                                                                  **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

namespace local_cts_co;

//TO DO: Change this into a Singleton and get rid of static functions
class base
{

    /**
     * Creates the Moodle page header
     * @param string $url Current page url
     * @param string $pagetitle Page title
     * @param string $pageheading Page heading (Note hard coded to site fullname)
     * @param array $context The page context (SYSTEM, COURSE, MODULE etc)
     * @param string $pagelayout The page context (SYSTEM, COURSE, MODULE etc)
     * @return HTML Contains page information and loads all Javascript and CSS
     * @global \stdClass $CFG
     * @global \moodle_database $DB
     * @global \moodle_page $PAGE
     * @global \stdClass $SITE
     */
    public static function page($url, $pagetitle, $pageheading, $context = null, $pagelayout = 'base')
    {
        global $CFG, $PAGE, $SITE;


        $context = \context_system::instance();

        $PAGE->set_url($url);
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pageheading);
        $PAGE->set_pagelayout($pagelayout);
        $PAGE->set_context($context);
        // We need datatables to work. So we load it from cdn
        // We also load one JS file that initialises all datatables.
        // This same file is used throughout, including in the blocks
        self::loadJQueryJS();
    }

    public static function loadJQueryJS()
    {
        global $CFG, $PAGE;
        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings('local_yulearn', current_language());

        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');
        $PAGE->requires->js(new \moodle_url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js'), true);
        $PAGE->requires->js(new \moodle_url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js'), true);
        $PAGE->requires->js(new \moodle_url('https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/af-2.4.0/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/cr-1.5.6/date-1.1.2/fc-4.1.0/fh-3.2.4/kt-2.7.0/r-2.3.0/rg-1.2.0/rr-1.2.8/sc-2.0.7/sb-1.3.4/sp-2.0.2/sl-1.4.0/sr-1.1.1/datatables.min.js'), true);
        $PAGE->requires->js(new \moodle_url($CFG->wwwroot . '/local/yulearn/js/yulearn_datatables.js'), true);
        $PAGE->requires->css(new \moodle_url('https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/af-2.4.0/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/cr-1.5.6/date-1.1.2/fc-4.1.0/fh-3.2.4/kt-2.7.0/r-2.3.0/rg-1.2.0/rr-1.2.8/sc-2.0.7/sb-1.3.4/sp-2.0.2/sl-1.4.0/sr-1.1.1/datatables.min.css'));
//        $PAGE->requires->css(new \moodle_url('/local/yulearn/css/yulearn.css'));

//        mdbtreeview
//        $PAGE->requires->css(new \moodle_url($CFG->wwwroot . '/local/yulearn/css/tree.css'));
//        $PAGE->requires->js(new \moodle_url($CFG->wwwroot . '/local/yulearn/js/tree.js'));
        $PAGE->requires->strings_for_js(array_keys($strings), 'local_yulearn');
    }

    /**
     * Sets filemanager options
     * @param \stdClass $context
     * @param int $maxfiles
     * @return array
     * @global \stdClass $CFG
     */
    public static function getFileManagerOptions($context, $maxfiles = 1)
    {
        global $CFG;
        return array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => $maxfiles);
    }


    public static function getEditorOptions($context)
    {
        global $CFG;
        return array('subdirs' => 1, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => -1,
            'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0);
    }
}
