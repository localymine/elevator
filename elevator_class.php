<?php

/**
 *
 * @author Kantoh
 */

/**
 * direction status: up, down, stand, maintenance
 */
interface direction {

    public function move_up($floor);

    public function move_down($floor);

    public function stand($floor);

    public function maintennace($floor);
}

interface box {

    public function direction($status);

    /**
     * signal: Alarm, Door Open, Door Close
     * @param type $signal
     */
    public function set_signal($signal);
}

class elevator implements box {

    public $status;
    public $signal;
    //
    public $current_stadning;   /* person */
    public $floor_request = array();
    public $current_status;   /* elevator */
    public $floors = array(1, 2, 3, 4, 5, 6, 7, 8);

    public function direction($status) {
        return;
    }

    public function set_signal($signal) {
        return;
    }

}
