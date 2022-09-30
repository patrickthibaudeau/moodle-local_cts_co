<?php
/**
 * *************************************************************************
 * *                          cos_approval                                **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  cos_approval                                              **
 * @name        cos_approval                                              **
 * @copyright   Glendon College York University                           **
 * @link        http://oohoo.biz                                          **
 * @author      Glendon ITS                                               **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

$capabilities = array(

    'local/cts_co:access_jira' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
 
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
    ),
);