<?php

class Priority {

    public $priority_id;
    public $value;
    public $is_default;

    public function __construct($priority_id, $value, $is_default) {
		$this->priority_id = $priority_id;
        $this->value = $value;
        $this->is_default = $is_default;
    }

    public static function construct_create($value) {
        return new Priority(-1, $value, false);
    }

    public static function construct_null() {
        return Priority::construct_create("");
    }

    public function sanitize() {
        $this->value = htmlspecialchars(strip_tags($this->value));
    }
	
}

?>