<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/departmentModel.php");

class DepartmentController {
	
	private $conn;
    public $tableName = "departments";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }
	
	public function get_by_value($department) {
		$fetched_department = null;
        $query = "SELECT department_id, value, is_default FROM ".$this->tableName." 
			WHERE value=:value;";
        
        $stmt = $this->conn->prepare($query);
        
		$department->sanitize();
		$stmt->bindParam(":value", $department->value);

        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_department = new Department($row["department_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_department;
    }

    public function get_default() {
        $fetched_department = null;
        $query = "SELECT department_id, value, is_default FROM ".$this->tableName." WHERE is_default = '1'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_department = new Department($row["department_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_department;
    }

    public function get_by_value_or_default($department_to_check) {
        $fetched_department = $this->get_by_value($department_to_check);
        if ($fetched_department == null) {
            $fetched_department = $this->get_default();
        }
        return $fetched_department;
    }

    public function get_all() {
        $fetched_departments = array();

        $query = "SELECT department_id, value, is_default FROM ".$this->tableName." ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push(
                    $fetched_departments, 
                    new IssueType($row["department_id"], $row["value"], $row["is_default"]));
            }
        }

        return $fetched_departments;
    }
	
}

?>