<?php

/**
 *
 * @author Khang Le
 */

/**
 * direction status: up, down, stand, maintenance
 */
interface direction {

    public function move_up($current, $floor);

    public function move_down($current, $floor);

    public function move($current, $floor);

    public function stand($floor);

    public function maintenance($floors);
}

/**
 * signal: Alarm, Door Open, Door Close
 * @param type $signal
 */
interface signal {

    const ALARM = 999;
    const OPEN = 1;
    const CLOSE = 0;
    const MOVING = 9;

    public function set_signal($signal);
}

/**
 * 
 */
class elevator implements direction, signal {

    public $floors = array();

    public function move_up($current, $floor) {
        while ($current < $floor && $current <= count($this->floors)) {
            $current++;
        }
        return $current;
    }

    public function move_down($current, $floor) {
        while ($current > $floor && $current > 1) {
            $current--;
        }
        return $current;
    }

    /**
     * 
     * Elevator at current floor go to the specified floor
     * @param type $current
     * @param type $floor
     * @return type
     * 
     */
    public function move($current, $floor) {
        if ($current < $floor) {
            $to_the_floor = $this->move_up($current, $floor);
        } else if ($current > $floor) {
            $to_the_floor = $this->move_down($current, $floor);
        } else {
            $to_the_floor = $this->stand($floor);
        }
        return $to_the_floor;
    }

    public function stand($floor) {
        return $floor;
    }

    public function maintenance($floors) {
        if (is_array($floors)) {
            sort($floors);
            return $floors;
        } else {
            return array($floors);
        }
    }

    public function set_signal($signal = CLOSE) {
        return $signal;
    }

}

/**
 * 
 */
class log {

    private $log_dir;
    private $log_data = '';
    public $readable = FALSE;
    public $data = array();
    private $signal = array(999 => 'maintenance', 0 => 'close', 1 => 'open', 9 => 'keep moving');

    public function __construct($file) {
        $this->log_dir = dirname($file) . '/';
    }

    public function record() {
        $log_data = '';
        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                $d = '';
                foreach ($value as $v) {
                    $d .= $this->signal[$v] . ' ';
                }
                $log_data .= $key . ' ' . $d . ' ';
            } else {
                $log_data .= $key . ' ' . $value . ' ';
            }
        }
        //
        $this->log_data[] = $log_data;
        //
        // save to file
        $log_file = $this->log_dir . 'elevator.log';
        file_put_contents($log_file, $log_data . PHP_EOL, FILE_APPEND);
        chmod($log_file, 0644);
        //
        //
        if ($this->readable){
            $this->readable();
        }
    }

    public function readable() {
        print_r($this->log_data[0] . '<br>');
    }

}

/**
 * 
 */
class my_elevator extends elevator {

    private $maintenance = array();
    public $request_floor = array(); /* person who is stading at floor */
    public $current_floor;   /* elevator at floor */
    public $floors = array(1, 2, 3, 4, 5, 6, 7, 8);

    /**
     * 
     * @param type $sort
     * @return type
     */
    private function __to_floors($sort = SORT_ASC) {
        if (is_array($this->request_floor)) {
            $floors = $this->request_floor;
            array_multisort($floors, $sort);
            return $floors;
        } else {
            return array($this->request_floor);
        }
    }

    /**
     * 
     * @param type $floors
     */
    public function maintenance($floors) {
        $this->maintenance = parent::maintenance($floors);
    }

    /**
     * 
     * @param type $current
     * @param type $to_floor
     * @return type
     */
    public function move_up($current, $to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
        //
        while ($current < $to_floor && $current <= count($this->floors)) {
            $current++;
            if ($current == $to_floor) {
                //
                $passed_floor[] = $to_floor;
                //
                foreach ($this->maintenance as $m_floor) {
                    if ($to_floor == $m_floor) {
                        // reach to maintenance floor
                        $flag_maintenance = TRUE;
                        break;
                    }
                }
                // on maintenancing
                if ($flag_maintenance) {
                    $flag_maintenance = FALSE;
                    $log->data = array(
                        'elevator' => 'Going UP',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::ALARM),
                            $this->set_signal(parent::CLOSE),
                            $this->set_signal(parent::MOVING),
                        ),
                    );
                    $log->record();
                } else {
                    $log->data = array(
                        'elevator' => 'Going UP',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::OPEN),
                            $this->set_signal(parent::CLOSE),
                        ),
                    );
                    $log->record();
                }
            }
        }
        return implode('', $passed_floor);
    }

    /**
     * 
     * @param type $current
     * @param type $to_floor
     * @return type
     */
    public function move_down($current, $to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
        //
        while ($current > $to_floor && $current > 1) {
            $current--;
            if ($current == $to_floor) {
                //
                $passed_floor[] = $to_floor;
                //
                foreach ($this->maintenance as $m_floor) {
                    if ($to_floor == $m_floor) {
                        // reach to maintenance floor
                        $flag_maintenance = TRUE;
                        break;
                    }
                }
                // on maintenancing
                if ($flag_maintenance) {
                    $flag_maintenance = FALSE;
                    $log->data = array(
                        'elevator' => 'Going Down',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::ALARM),
                            $this->set_signal(parent::CLOSE),
                            $this->set_signal(parent::MOVING),
                        ),
                    );
                    $log->record();
                } else {
                    $log->data = array(
                        'elevator' => 'Going Down',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::OPEN),
                            $this->set_signal(parent::CLOSE),
                        ),
                    );
                    $log->record();
                }
            }
        }
        return implode('', $passed_floor);
    }

    /**
     * 
     * @param type $to_floor
     */
    public function stand($to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
        //
        if ($this->current_floor == $to_floor) {
            //
            $passed_floor[] = $to_floor;
            //
            foreach ($this->maintenance as $m_floor) {
                if ($to_floor == $m_floor) {
                    // reach to maintenance floor
                    $flag_maintenance = TRUE;
                    break;
                }
            }
        }
        // on maintenancing
        if ($flag_maintenance) {
            $flag_maintenance = FALSE;
            $log->data = array(
                'elevator' => 'Stading At',
                'floor' => $to_floor,
                'signal' => array(
                    $this->set_signal(parent::ALARM),
                    $this->set_signal(parent::CLOSE),
                    $this->set_signal(parent::MOVING),
                ),
            );
            $log->record();
        } else {
            $log->data = array(
                'elevator' => 'Stading At',
                'floor' => $to_floor,
                'signal' => array(
                    $this->set_signal(parent::OPEN),
                    $this->set_signal(parent::CLOSE),
                ),
            );
            $log->record();
        }
        return implode('', $passed_floor);
    }

    /**
     * 
     */
    public function call_evelator() {
        $current = $this->current_floor;
        $passed_floor = array();
        $move_status = 'up';
        //
        // standing at current floor
        if (in_array($current, $this->__to_floors())) {
            $move_status = 'stand';
        } else {
            //
            foreach ($this->__to_floors() as $to_floor) {
                if ($current < $to_floor) {
                    // move up
                    $move_status = 'up';
                    break;
                } else if ($current > $to_floor) {
                    // move down
                    $move_status = 'down';
                    break;
                }
            }
        }
        //
        switch ($move_status) {
            case 'up':
                foreach ($this->__to_floors() as $to_floor) {
                    $passed_floor[] = $this->move_up($current, $to_floor);
                }
                break;
            case 'down':
                foreach ($this->__to_floors(SORT_DESC) as $to_floor) {
                    $passed_floor[] = $this->move_down($current, $to_floor);
                }
                break;
            case 'stand':
                $passed_floor[] = $this->stand($current);
                break;
        }
        //

        $last_floor = $passed_floor[count($passed_floor) - 1];
        $remaining_floor = array_diff($this->__to_floors(), $passed_floor);
        //
        foreach ($remaining_floor as $to_floor) {
            if ($last_floor < $to_floor) {
                $this->move_up($current, $to_floor);
            } else if ($last_floor > $to_floor) {
                $this->move_down($current, $to_floor);
            }
        }
    }

}
