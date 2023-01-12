<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/issueModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/priorityController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/departmentController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/stateController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueTypeController.php");

class IssueController {
	
	private $conn;
    public $tableName = "issues";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }

    public function create($issue) {
        $query = 
        "INSERT INTO ".$this->tableName." 
            (title, 
            full_name,
            office, 
            description, 
            comment,
            issue_type_id,
            priority_id,
            state_id,
            department_id) 
        VALUES (
            :title, 
            :full_name, 
            :office, 
            :description,
            :comment,
            :issue_type_id,
            :priority_id,
            :state_id,
            :department_id
        )";
        
        $stmt = $this->conn->prepare($query);
        
        $issue->sanitize();
        
        $stmt->bindParam(":title", $issue->title);
        $stmt->bindParam(":full_name", $issue->full_name);
        $stmt->bindParam(":office", $issue->office);
        $stmt->bindParam(":description", $issue->description);
        $stmt->bindParam(":comment", $issue->comment);

        $priority_controller = new PriorityController($this->conn);
        $priority = $priority_controller->get_by_value_or_default($issue->priority);
        $stmt->bindParam(":priority_id", $priority->priority_id);

        $state_controller = new StateController($this->conn);
        $state = $state_controller->get_by_value_or_default($issue->state);
        $stmt->bindParam(":state_id", $state->state_id);

        $department_controller = new DepartmentController($this->conn);
        $department = $department_controller->get_by_value_or_default($issue->department);
        $stmt->bindParam(":department_id", $department->department_id);

        $issue_type_controller = new IssueTypeController($this->conn);
        $issue_type = $issue_type_controller->get_by_value_or_default($issue->issue_type);
        $stmt->bindParam(":issue_type_id", $issue_type->issue_type_id);

        return $stmt->execute();
    }
	
	public function update($issue) {
		$fetched_issue = $this->get_by_id($issue->issue_id);
		if ($fetched_issue == null) return false;
		
		$query = 
        "UPDATE ".$this->tableName." 
			SET
				description = :description,
				comment = :comment,
				issue_type_id = :issue_type_id,
				priority_id = :priority_id,
				state_id = :state_id
			WHERE
				issue_id = :issue_id ";
        
        $stmt = $this->conn->prepare($query);
		
		$issue->sanitize();
		
		$stmt->bindParam(":issue_id", $issue->issue_id);
		$stmt->bindParam(":description", $issue->description);
        $stmt->bindParam(":comment", $issue->comment);
		
		$priority_controller = new PriorityController($this->conn);
        $priority = $priority_controller->get_by_value_or_default($issue->priority);
        $stmt->bindParam(":priority_id", $priority->priority_id);

        $state_controller = new StateController($this->conn);
        $state = $state_controller->get_by_value_or_default($issue->state);
        $stmt->bindParam(":state_id", $state->state_id);

        $issue_type_controller = new IssueTypeController($this->conn);
        $issue_type = $issue_type_controller->get_by_value_or_default($issue->issue_type);
        $stmt->bindParam(":issue_type_id", $issue_type->issue_type_id);
		
		return $stmt->execute();
	}

    public function delete_issue($issue_id) {
		$fetched_issue = $this->get_by_id($issue_id);
		if ($fetched_issue == null) return false;
		$query = "DELETE FROM ".$this->tableName." WHERE issue_id = :issue_id";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(":issue_id", $fetched_issue->issue_id);
        return $stmt->execute();
    }
	
	public function get_by_id($issue_id) {
		$fetched_issue = null;
		
		$priority_controller = new PriorityController($this->conn);
		$state_controller = new StateController($this->conn);
		$department_controller = new DepartmentController($this->conn);
		$issue_type_controller = new IssueTypeController($this->conn);

        $query = "
            SELECT 
                " . $this->tableName . ".issue_id, 
                " . $this->tableName . ".create_date,
                " . $this->tableName . ".title,
                " . $this->tableName . ".full_name,
                " . $department_controller->tableName . ".department_id AS department_id,
				" . $department_controller->tableName . ".value AS department_value,
				" . $department_controller->tableName . ".is_default AS department_is_default,
                " . $this->tableName . ".office,
                " . $issue_type_controller->tableName . ".issue_type_id AS issue_type_id,
				" . $issue_type_controller->tableName . ".value AS issue_type_value,
				" . $issue_type_controller->tableName . ".is_default AS issue_type_is_default,
                " . $this->tableName . ".description,
                " . $this->tableName . ".comment,
                " . $state_controller->tableName . ".state_id AS state_id,
				" . $state_controller->tableName . ".value AS state_value,
				" . $state_controller->tableName . ".is_default AS state_is_default,
                " . $priority_controller->tableName . ".priority_id AS priority_id,
				" . $priority_controller->tableName . ".value AS priority_value,
				" . $priority_controller->tableName . ".is_default AS priority_is_default,
                " . $this->tableName . ".is_deleted 
            FROM 
				" . $this->tableName . " ,
				" . $department_controller->tableName . ",
				" . $issue_type_controller->tableName . ",
				" . $state_controller->tableName . ",
				" . $priority_controller->tableName . "
			WHERE 
				" . $this->tableName . ".department_id = " . $department_controller->tableName . ".department_id AND
				" . $this->tableName . ".state_id = " . $state_controller->tableName . ".state_id AND
				" . $this->tableName . ".issue_type_id = " . $issue_type_controller->tableName . ".issue_type_id AND
				" . $this->tableName . ".priority_id = " . $priority_controller->tableName . ".priority_id AND
				" . $this->tableName . ".issue_id = :issue_id ";
				
        $stmt = $this->conn->prepare($query);
		$stmt->bindParam(":issue_id", $issue_id);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
			$fetched_issue = new Issue(
				$row["issue_id"], 
				$row["create_date"],
				$row["title"],
				$row["full_name"],
				new Department(
					$row["department_id"],
					$row["department_value"],
					$row["department_is_default"]
				),
				new IssueType(
					$row["issue_type_id"],
					$row["issue_type_value"],
					$row["issue_type_is_default"]
				),
				$row["office"],
				$row["description"],
				$row["comment"],
				new State(
					$row["state_id"],
					$row["state_value"],
					$row["state_is_default"],
				),
				new Priority(
					$row["priority_id"],
					$row["priority_value"],
					$row["priority_is_default"],
				),
				$row["is_deleted"]
			);
        }

        return $fetched_issue;
	}

    public function get_all() {
        $fetched_issues = array();
		
		$priority_controller = new PriorityController($this->conn);
		$state_controller = new StateController($this->conn);
		$department_controller = new DepartmentController($this->conn);
		$issue_type_controller = new IssueTypeController($this->conn);

        $query = "
            SELECT 
                " . $this->tableName . ".issue_id, 
                " . $this->tableName . ".create_date,
                " . $this->tableName . ".title,
                " . $this->tableName . ".full_name,
                " . $department_controller->tableName . ".department_id AS department_id,
				" . $department_controller->tableName . ".value AS department_value,
				" . $department_controller->tableName . ".is_default AS department_is_default,
                " . $this->tableName . ".office,
                " . $issue_type_controller->tableName . ".issue_type_id AS issue_type_id,
				" . $issue_type_controller->tableName . ".value AS issue_type_value,
				" . $issue_type_controller->tableName . ".is_default AS issue_type_is_default,
                " . $this->tableName . ".description,
                " . $this->tableName . ".comment,
                " . $state_controller->tableName . ".state_id AS state_id,
				" . $state_controller->tableName . ".value AS state_value,
				" . $state_controller->tableName . ".is_default AS state_is_default,
                " . $priority_controller->tableName . ".priority_id AS priority_id,
				" . $priority_controller->tableName . ".value AS priority_value,
				" . $priority_controller->tableName . ".is_default AS priority_is_default,
                " . $this->tableName . ".is_deleted 
            FROM 
				" . $this->tableName . " ,
				" . $department_controller->tableName . ",
				" . $issue_type_controller->tableName . ",
				" . $state_controller->tableName . ",
				" . $priority_controller->tableName . "
			WHERE 
				" . $this->tableName . ".department_id = " . $department_controller->tableName . ".department_id AND
				" . $this->tableName . ".state_id = " . $state_controller->tableName . ".state_id AND
				" . $this->tableName . ".issue_type_id = " . $issue_type_controller->tableName . ".issue_type_id AND
				" . $this->tableName . ".priority_id = " . $priority_controller->tableName . ".priority_id";
				
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push(
                    $fetched_issues, 
                    new Issue(
                        $row["issue_id"], 
                        $row["create_date"],
                        $row["title"],
                        $row["full_name"],
						new Department(
							$row["department_id"],
							$row["department_value"],
							$row["department_is_default"]
						),
						new IssueType(
							$row["issue_type_id"],
							$row["issue_type_value"],
							$row["issue_type_is_default"]
						),
                        $row["office"],
                        $row["description"],
                        $row["comment"],
						new State(
							$row["state_id"],
							$row["state_value"],
							$row["state_is_default"],
						),
						new Priority(
							$row["priority_id"],
							$row["priority_value"],
							$row["priority_is_default"],
						),
                        $row["is_deleted"])
                    );
            }
        }

        return $fetched_issues;
    }

	public function get_by_filter($priority_value, $state_value, $page, $is_ascending) {

		$fetched_issues = array();
		
		$priority_controller = new PriorityController($this->conn);
		$state_controller = new StateController($this->conn);
		$department_controller = new DepartmentController($this->conn);
		$issue_type_controller = new IssueTypeController($this->conn);

		$rows_per_page = 4;
		$start = ($page - 1) * $rows_per_page;
		//$end = $start + $rows_per_page;
		$ascending = "ASC";
		if (!$is_ascending) $ascending = "DESC";

        $query = "
            SELECT
                " . $this->tableName . ".issue_id, 
                " . $this->tableName . ".create_date,
                " . $this->tableName . ".title,
                " . $this->tableName . ".full_name,
                " . $department_controller->tableName . ".department_id AS department_id,
				" . $department_controller->tableName . ".value AS department_value,
				" . $department_controller->tableName . ".is_default AS department_is_default,
                " . $this->tableName . ".office,
                " . $issue_type_controller->tableName . ".issue_type_id AS issue_type_id,
				" . $issue_type_controller->tableName . ".value AS issue_type_value,
				" . $issue_type_controller->tableName . ".is_default AS issue_type_is_default,
                " . $this->tableName . ".description,
                " . $this->tableName . ".comment,
                " . $state_controller->tableName . ".state_id AS state_id,
				" . $state_controller->tableName . ".value AS state_value,
				" . $state_controller->tableName . ".is_default AS state_is_default,
                " . $priority_controller->tableName . ".priority_id AS priority_id,
				" . $priority_controller->tableName . ".value AS priority_value,
				" . $priority_controller->tableName . ".is_default AS priority_is_default,
                " . $this->tableName . ".is_deleted 
            FROM 
				" . $this->tableName . " ,
				" . $department_controller->tableName . ",
				" . $issue_type_controller->tableName . ",
				" . $state_controller->tableName . ",
				" . $priority_controller->tableName . "
			WHERE 
				" . $this->tableName . ".department_id = " . $department_controller->tableName . ".department_id AND
				" . $this->tableName . ".state_id = " . $state_controller->tableName . ".state_id AND
				" . $this->tableName . ".issue_type_id = " . $issue_type_controller->tableName . ".issue_type_id AND
				" . $this->tableName . ".priority_id = " . $priority_controller->tableName . ".priority_id AND
				" . $priority_controller->tableName . ".value = :priority_value AND
				" . $state_controller->tableName . ".value = :state_value 
			ORDER BY " . $this->tableName . ".create_date " . $ascending;

		if ($page > 0) {
			$query .= " LIMIT " . $start . ", " . $rows_per_page . "";
		}
		//echo "start : " . $start . " end : " . $end;die();
		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(":priority_value", $priority_value);
        $stmt->bindParam(":state_value", $state_value);

		$stmt->execute();
		$numberOfRows = $stmt->rowCount();

		if($numberOfRows > 0) {
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				array_push(
					$fetched_issues, 
					new Issue(
						$row["issue_id"], 
						$row["create_date"],
						$row["title"],
						$row["full_name"],
						new Department(
							$row["department_id"],
							$row["department_value"],
							$row["department_is_default"]
						),
						new IssueType(
							$row["issue_type_id"],
							$row["issue_type_value"],
							$row["issue_type_is_default"]
						),
						$row["office"],
						$row["description"],
						$row["comment"],
						new State(
							$row["state_id"],
							$row["state_value"],
							$row["state_is_default"],
						),
						new Priority(
							$row["priority_id"],
							$row["priority_value"],
							$row["priority_is_default"],
						),
						$row["is_deleted"])
					);
			}
		}

		return $fetched_issues;

	}

	public function get_by_filter_pages($priority_value, $state_value) {

		$filter_pages = 0;
		$fetched_issues_count = 0;
		$rows_per_page = 4;
		
		$priority_controller = new PriorityController($this->conn);
		$state_controller = new StateController($this->conn);
		$department_controller = new DepartmentController($this->conn);
		$issue_type_controller = new IssueTypeController($this->conn);

        $query = "
            SELECT
                COUNT(" . $this->tableName . ".issue_id)
            FROM 
				" . $this->tableName . " ,
				" . $department_controller->tableName . ",
				" . $issue_type_controller->tableName . ",
				" . $state_controller->tableName . ",
				" . $priority_controller->tableName . "
			WHERE 
				" . $this->tableName . ".department_id = " . $department_controller->tableName . ".department_id AND
				" . $this->tableName . ".state_id = " . $state_controller->tableName . ".state_id AND
				" . $this->tableName . ".issue_type_id = " . $issue_type_controller->tableName . ".issue_type_id AND
				" . $this->tableName . ".priority_id = " . $priority_controller->tableName . ".priority_id AND
				" . $priority_controller->tableName . ".value = :priority_value AND
				" . $state_controller->tableName . ".value = :state_value";

		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(":priority_value", $priority_value);
        $stmt->bindParam(":state_value", $state_value);

		$stmt->execute();
		$numberOfRows = $stmt->rowCount();

		if($numberOfRows > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$fetched_issues_count = $row["COUNT(" . $this->tableName . ".issue_id)"];
		}

		$count_rows = 0;
		while ($fetched_issues_count > 0) {
			$fetched_issues_count--;
			$count_rows++;
			if ($count_rows == 4) {
				$filter_pages++;
				$count_rows = 0;
			}
		}
		if ($count_rows > 0) { $filter_pages++; }

		return $filter_pages;

	}
	
	public function get_count_by_filter($priority_value, $state_value) {
		
		$fetched_issues_count = 0;
		$priority_controller = new PriorityController($this->conn);
		$state_controller = new StateController($this->conn);
		$department_controller = new DepartmentController($this->conn);
		$issue_type_controller = new IssueTypeController($this->conn);

        $query = "
            SELECT
                COUNT(" . $this->tableName . ".issue_id)
            FROM 
				" . $this->tableName . " ,
				" . $department_controller->tableName . ",
				" . $issue_type_controller->tableName . ",
				" . $state_controller->tableName . ",
				" . $priority_controller->tableName . "
			WHERE 
				" . $this->tableName . ".department_id = " . $department_controller->tableName . ".department_id AND
				" . $this->tableName . ".state_id = " . $state_controller->tableName . ".state_id AND
				" . $this->tableName . ".issue_type_id = " . $issue_type_controller->tableName . ".issue_type_id AND
				" . $this->tableName . ".priority_id = " . $priority_controller->tableName . ".priority_id AND
				" . $priority_controller->tableName . ".value = :priority_value AND
				" . $state_controller->tableName . ".value = :state_value";

		//echo "start : " . $start . " end : " . $end;die();
		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(":priority_value", $priority_value);
        $stmt->bindParam(":state_value", $state_value);

		$stmt->execute();
		$numberOfRows = $stmt->rowCount();

		if($numberOfRows > 0) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$fetched_issues_count = $row["COUNT(" . $this->tableName . ".issue_id)"];
		}

		return $fetched_issues_count;
		
	}
	
}

?>