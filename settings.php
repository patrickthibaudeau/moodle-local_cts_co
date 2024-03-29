<?php
/**
 * *************************************************************************
 * *                   CTS Computer Order System                          **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  cts_co                                                    **
 * @name        CTS Computer Order System                                 **
 * @copyright   UIT - Innovation lab & EAAS                               **
 * @link                                                                  **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */
defined('MOODLE_INTERNAL') || die;

$systemcontext = context_system::instance();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_cts_co', get_string('pluginname', 'local_cts_co'));
    $ADMIN->add('localplugins',$settings );

    // HALO Settings
    $settings->add(new admin_setting_heading(
        'halo_setting',
        get_string('halo_settings', 'local_cts_co'),
        ''
    ));
    // Halo Authentication URL
    $settings->add(new admin_setting_configtext(
        'halo_url',
        get_string('halo_url', 'local_cts_co'),
        get_string('halo_url_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo tenant
    $settings->add(new admin_setting_configtext(
        'halo_tenant',
        get_string('tenant', 'local_cts_co'),
        get_string('tenant_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo client_id
    $settings->add(new admin_setting_configtext(
        'halo_client_id',
        get_string('client_id', 'local_cts_co'),
        get_string('client_id_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo client_secret
    $settings->add(new admin_setting_configpasswordunmask(
        'halo_client_secret',
        get_string('client_secret', 'local_cts_co'),
        get_string('client_secret_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo Authentication URL
    $settings->add(new admin_setting_configtext(
        'halo_auth_url',
        get_string('auth_url', 'local_cts_co'),
        get_string('auth_url_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo API URL
    $settings->add(new admin_setting_configtext(
        'halo_api_url',
        get_string('api_url', 'local_cts_co'),
        get_string('api_url_help', 'local_cts_co'),
        '',
        PARAM_TEXT
    ));
    // Halo Site ID
    $settings->add(new admin_setting_configtext(
        'halo_site_id',
        get_string('site_id', 'local_cts_co'),
        get_string('site_id_help', 'local_cts_co'),
        20,
        PARAM_INT
    ));

    // Halo Timezone adjustment
    $settings->add(new admin_setting_configtext(
        'halo_timezone_adjustment',
        get_string('timezone_adjustment', 'local_cts_co'),
        get_string('timezone_adjustment_help', 'local_cts_co'),
        5,
        PARAM_INT
    ));

}





