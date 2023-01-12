<?php

class State {

    public $state_id;
    public $value;
    public $is_default;

    public function __construct($state_id, $value, $is_default) {
		$this->state_id = $state_id;
        $this->value = $value;
        $this->is_default = $is_default;
    }

    public static function construct_create($value) {
        return new State(-1, $value, false);
    }

    public static function construct_null() {
        return State::construct_create("");
    }

    public function sanitize() {
        $this->value = htmlspecialchars(strip_tags($this->value));
    }
	
}

?>