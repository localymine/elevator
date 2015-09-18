<?php

/**
 * 
 * @param int current_floor
 * @param int request_floor
 * @param int maintenance
 * 
 * current_floor=1&request_floor=0,6,5,7,3,2,4&maintenance=2,4
 */
if (isset($_GET['current_floor']) && isset($_GET['request_floor'])) {

    require_once 'elevator_class.php';

    $elevator = new my_elevator;

    $current_floor = (int) $_GET['current_floor'];
    $request_floor = explode(',', $_GET['request_floor']);
    $maintenance = isset($_GET['maintenance']) ? explode(',', $_GET['maintenance']) : array();

    $elevator->floors = array(-1, 0, 1, 2, 3, 4, 5, 6, 7, 8);
    $elevator->current_floor = $current_floor;
    $elevator->request_floor = $request_floor;
    $elevator->maintenance($maintenance);

    $elevator->call_evelator();

    echo 'Please check log.';
} else {
    die('try again');
}

