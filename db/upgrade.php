<?php
defined('MOODLE_INTERNAL') || die();

/**
 *
 * @param type $oldversion
 * @return boolean
 * @global type $CFG
 * @global \moodle_database $DB
 */
function xmldb_local_cts_co_upgrade($oldversion)
{
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023091800) {

        // Define table cts_co_request to be dropped.
        $table = new xmldb_table('cts_co_request');

        // Conditionally launch drop table for cts_co_request.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table cts_co_status to be dropped.
        $table = new xmldb_table('cts_co_status');

        // Conditionally launch drop table for cts_co_status.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2023091800, 'local', 'cts_co');
    }


    return true;
}