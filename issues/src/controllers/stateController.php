<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/stateModel.php");

class StateController {
	
	private $conn;
    public $tableName = "states";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }
	
	public function get_by_value($state) {
		$fetched_state = null;
        $query = "SELECT state_id, value, is_default FROM ".$this->tableName." 
			WHERE value=:value;";
        
        $stmt = $this->conn->prepare($query);
        
		$state->sanitize();
		$stmt->bindParam(":value", $state->value);

        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_state = new State($row["state_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_state;
    }

    public function get_default() {
        $fetched_state = null;
        $query = "SELECT state_id, value, is_default FROM ".$this->tableName." WHERE is_default = '1'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		if ($numberOfRows > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fetched_state = new State($row["state_id"], $row["value"], $row["is_default"]);
        }
        
        return $fetched_state;
    }

    public function get_by_value_or_default($state_to_check) {
        $fetched_state = $this->get_by_value($state_to_check);
        if ($fetched_state == null) {
            $fetched_state = $this->get_default();
        }
        return $fetched_state;
    }

    public function get_all() {
        $fetched_states = array();

        $query = "SELECT state_id, value, is_default FROM ".$this->tableName." ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

        if($numberOfRows > 0) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push(
                    $fetched_states, 
                    new State($row["state_id"], $row["value"], $row["is_default"]));
            }
        }

        return $fetched_states;
    }
	
}

?>