<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/priorityModel.php");

class PriorityController {
	
	private $conn;
    public $tableName = "priorities";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }
	
	public function get_by_value($priority) {
		$fetched_priority = null;
        $query = "SELECT priority_id, value, is_default FROM ".$this->tableName." 
			WHERE value=:value;";
        
        $stmt = $this->conn->prepare($query);
        
		$priority->sanitize();
		$stmt->bindParam(":value", $priority->value);

        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_priority = new Priority($row["priority_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_priority;
    }

    public function get_default() {
        $fetched_priority = null;
        $query = "SELECT priority_id, value, is_default FROM ".$this->tableName." WHERE is_default = '1'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_priority = new Priority($row["priority_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_priority;
    }

    public function get_by_value_or_default($priority_to_check) {
        $fetched_priority = $this->get_by_value($priority_to_check);
        if ($fetched_priority == null) {
            $fetched_priority = $this->get_default();
        }
        return $fetched_priority;
    }

    public function get_all() {
        $fetched_priorities = array();

        $query = "SELECT priority_id, value, is_default FROM ".$this->tableName." ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push(
                    $fetched_priorities, 
                    new Priority($row["priority_id"], $row["value"], $row["is_default"]));
            }
        }

        return $fetched_priorities;
    }
	
}

?>