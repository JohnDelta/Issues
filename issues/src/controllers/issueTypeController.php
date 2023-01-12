<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/issueTypeModel.php");

class IssueTypeController {
	
	private $conn;
    public $tableName = "issue_types";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }
	
	public function get_by_value($issue_type) {
		$fetched_issue_type = null;
        $query = "SELECT issue_type_id, value, is_default FROM ".$this->tableName." 
			WHERE value=:value;";
        
        $stmt = $this->conn->prepare($query);
        
		$issue_type->sanitize();
		$stmt->bindParam(":value", $issue_type->value);

        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_issue_type = new IssueType($row["issue_type_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_issue_type;
    }

    public function get_default() {
        $fetched_issue_type = null;
        $query = "SELECT issue_type_id, value, is_default FROM ".$this->tableName." WHERE is_default = '1'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_issue_type = new IssueType($row["issue_type_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_issue_type;
    }

    public function get_by_value_or_default($issue_type_to_check) {
        $fetched_issue_type = $this->get_by_value($issue_type_to_check);
        if ($fetched_issue_type == null) {
            $fetched_issue_type = $this->get_default();
        }
        return $fetched_issue_type;
    }

    public function get_all() {
        $fetched_issue_types = array();

        $query = "SELECT issue_type_id, value, is_default FROM ".$this->tableName." ORDER BY issue_type_id ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push(
                    $fetched_issue_types, 
                    new IssueType($row["issue_type_id"], $row["value"], $row["is_default"]));
            }
        }

        return $fetched_issue_types;
    }
	
}

?>