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

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'local_cts_co\task\update_status',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ),
);
