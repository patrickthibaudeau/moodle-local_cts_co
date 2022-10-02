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

    if ($oldversion < 2022092600) {

        // Define table cts_co_request to be created.
        $table = new xmldb_table('cts_co_request');

        // Adding fields to table cts_co_request.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('summary', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('halo_ticket_id', XMLDB_TYPE_INTEGER, '16', null, null, null, '0');
        $table->add_field('jira_issue_id', XMLDB_TYPE_INTEGER, '16', null, null, null, '0');
        $table->add_field('jira_issue_key', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('jira_issue_url', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table cts_co_request.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for cts_co_request.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cts_co_status to be created.
        $table = new xmldb_table('cts_co_status');

        // Adding fields to table cts_co_status.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('request_id', XMLDB_TYPE_INTEGER, '16', null, null, null, '0');
        $table->add_field('status', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '16', null, null, null, null);

        // Adding keys to table cts_co_status.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for cts_co_status.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022092600, 'local', 'cts_co');
    }

    if ($oldversion < 2022092601) {

        // Define index idx_request_id (not unique) to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $index = new xmldb_index('idx_request_id', XMLDB_INDEX_NOTUNIQUE, ['request_id']);

        // Conditionally launch add index idx_request_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022092601, 'local', 'cts_co');
    }

    if ($oldversion < 2022092607) {

        // Define field latest_status to be added to cts_co_request.
        $table = new xmldb_table('cts_co_request');
        $field = new xmldb_field('latest_status', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'jira_issue_url');

        // Conditionally launch add field latest_status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022092607, 'local', 'cts_co');
    }

    if ($oldversion < 2022093003) {

        // Define field halo_action_id to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $field = new xmldb_field('halo_action_id', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'status');

        // Conditionally launch add field halo_action_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022093003, 'local', 'cts_co');
    }
    return true;
}