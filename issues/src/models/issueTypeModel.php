<?php

class IssueType {

    public $issue_type_id;
    public $value;
    public $is_default;

    public function __construct($issue_type_id, $value, $is_default) {
		$this->issue_type_id = $issue_type_id;
        $this->value = $value;
        $this->is_default = $is_default;
    }

    public static function construct_create($value) {
        return new IssueType(-1, $value, false);
    }

    public static function construct_null() {
        return construct_create("");
    }

    public function sanitize() {
        $this->value = htmlspecialchars(strip_tags($this->value));
    }
	
}

?>