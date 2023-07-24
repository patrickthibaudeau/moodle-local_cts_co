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

    if ($oldversion < 2022100400) {

        // Define field agent to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $field = new xmldb_field('agent', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'status');

        // Conditionally launch add field agent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field due_date to be added to cts_co_request.
        $table = new xmldb_table('cts_co_request');
        $field = new xmldb_field('due_date', XMLDB_TYPE_INTEGER, '16', null, null, null, '0', 'jira_issue_url');

        // Conditionally launch add field due_date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022100400, 'local', 'cts_co');
    }

    if ($oldversion < 2022112300) {

        // Define field jira_comment_id to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $field = new xmldb_field('jira_comment_id', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'halo_action_id');

        // Conditionally launch add field jira_comment_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field jira_comment to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $field = new xmldb_field('jira_comment', XMLDB_TYPE_TEXT, null, null, null, null, null, 'jira_comment_id');

        // Conditionally launch add field jira_comment.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index idx_comment_id (unique) to be added to cts_co_status.
        $table = new xmldb_table('cts_co_status');
        $index = new xmldb_index('idx_comment_id', XMLDB_INDEX_NOTUNIQUE, ['jira_comment_id']);

        // Conditionally launch add index idx_comment_id.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2022112300, 'local', 'cts_co');
    }

    if ($oldversion < 2023072001) {

        // Define field status_code to be added to cts_co_request.
        $table = new xmldb_table('cts_co_request');
        $field = new xmldb_field('status_code', XMLDB_TYPE_INTEGER, '2', null, null, null, '1', 'due_date');

        // Conditionally launch add field status_code.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $sql_quote_process = "UPDATE {cts_co_request} SET status_code=2 WHERE latest_status='Quote Process'";
        $sql_pending_approval = "UPDATE {cts_co_request} SET status_code=3 WHERE latest_status='Pending Approval (SmartBuy)'";
        $sql_with_vendor = "UPDATE {cts_co_request} SET status_code=4 WHERE latest_status='With Vendor'";
        $sql_order_received = "UPDATE {cts_co_request} SET status_code=5 WHERE latest_status='Order Received (CTS)'";
        $sql_inventory_preperation = "UPDATE {cts_co_request} SET status_code=6 WHERE latest_status='Inventory Preperation'";
        $sql_deployment = "UPDATE {cts_co_request} SET status_code=7 WHERE latest_status='Deployment'";
        $sql_completed = "UPDATE {cts_co_request} SET status_code=8 WHERE latest_status='Request Completed'";

        $DB->execute($sql_quote_process);
        $DB->execute($sql_pending_approval);
        $DB->execute($sql_with_vendor);
        $DB->execute($sql_order_received);
        $DB->execute($sql_inventory_preperation);
        $DB->execute($sql_deployment);
        $DB->execute($sql_completed);


        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2023072001, 'local', 'cts_co');
    }

    if ($oldversion < 2023072002) {

        // Define field agent_id to be added to cts_co_request.
        $table = new xmldb_table('cts_co_request');
        $field = new xmldb_field('agent_id', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'due_date');

        // Conditionally launch add field agent_id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Cts_co savepoint reached.
        upgrade_plugin_savepoint(true, 2023072002, 'local', 'cts_co');
    }

    return true;
}