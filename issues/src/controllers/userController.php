<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/userModel.php");

class UserController {
	
	private $conn;
    public $tableName = "users";
	
	public function __construct($conn) {
        $this->conn = $conn;
    }
	
	public function user_exists($user) {
		$query = "SELECT username, password FROM ".$this->tableName." 
			WHERE username=:username AND password=:password;";
        
        $stmt = $this->conn->prepare($query);
        
		$user->sanitize();
		$stmt->bindParam(":username", $user->username);
		$stmt->bindParam(":password", $user->password);

        $stmt->execute();
        $numberOfRows = $stmt->rowCount();

		return $numberOfRows > 0;
    }
	
}

?>