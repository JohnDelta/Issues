<?php

class Department {

    public $department_id;
    public $value;
    public $is_default;

    public function __construct($department_id, $value, $is_default) {
		$this->department_id = $department_id;
        $this->value = $value;
        $this->is_default = $is_default;
    }

    public static function construct_create($value) {
        return new Department(-1, $value, false);
    }

    public static function construct_null() {
        return Department::construct_create("");
    }
	
    public function sanitize() {
        $this->value = htmlspecialchars(strip_tags($this->value));
    }

}

?>