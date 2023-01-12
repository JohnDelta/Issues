<?php

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/stateModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/priorityModel.php");

class Issue {

    public $issue_id;
    public $create_date;
	public $title;
	public $full_name;
	public $department;
	public $issue_type;
	public $office;
	public $description;
	public $comment;
	public $state;
	public $priority;
	public $is_deleted;

    public function __construct(
		$issue_id, 
		$create_date,
		$title,
		$full_name,
		$department,
		$issue_type,
		$office,
		$description,
		$comment,
		$state,
		$priority,
		$is_deleted)
    {
        $this->issue_id = $issue_id;
		$this->create_date = $create_date;
		$this->title = $title;
		$this->full_name = $full_name;
		$this->department = $department;
		$this->issue_type = $issue_type;
		$this->office = $office;
		$this->description = $description;
		$this->comment = $comment;
		$this->state = $state;
		$this->priority = $priority;
		$this->is_deleted = $is_deleted;
    }

	public static function construct_create(
		$title,
		$full_name,
		$department,
		$issue_type,
		$office,
		$description) {
			return new Issue(
				-1, 
				"",
				$title,
				$full_name,
				$department,
				$issue_type,
				$office,
				$description,
				"",
				State::construct_null(),
				Priority::construct_null(),
				false
			);
	}
	
	public static function construct_update(
		$issue_id,
		$issue_type,
		$description,
		$comment,
		$state,
		$priority) {
			return new Issue(
				$issue_id, 
				"",
				"",
				"",
				Department::construct_null(),
				$issue_type,
				"",
				$description,
				$comment,
				$state,
				$priority,
				false
			);
	}
	
	public function sanitize() {
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->full_name = htmlspecialchars(strip_tags($this->full_name));
		$this->office = htmlspecialchars(strip_tags($this->office));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->comment = htmlspecialchars(strip_tags($this->comment));
		$this->priority->sanitize();
		$this->state->sanitize();
		$this->issue_type->sanitize();
		$this->department->sanitize();
	}

}

?>