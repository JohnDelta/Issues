<?PHP header('Content-Type: text/html; charset=utf-8'); ?>

<?PHP include 'header.php'; ?>

<?PHP

session_start();

$logged_in_session_key = "loggedin";

if((isset($_SESSION[$logged_in_session_key]) && $_SESSION[$logged_in_session_key] == true) ||
	(count($_COOKIE) > 0 && isset($_COOKIE[$logged_in_session_key]) && $_COOKIE[$logged_in_session_key] == true)){
    
		if (count($_COOKIE) > 0 && isset($_COOKIE[$logged_in_session_key]) && $_COOKIE[$logged_in_session_key] == true) {
			setcookie($logged_in_session_key, true, time() + (86400 * 30), "/"); // refresh
		}

		header("location: admin.php");
		exit;
}
 
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/config/database.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/userController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/userModel.php");

$username = $password = $feedback_message = "";
$error_code = 0;
$remember_me = false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if(empty(trim($_POST["username"]))){
        $feedback_message = "Username requried";
		$error_code = 1;
    } else {
        $username = trim($_POST["username"]);
    }
	
	if(empty(trim($_POST["password"]))){
        $feedback_message = "User password required";
		$error_code = 1;
    } else {
        $password = trim($_POST["password"]);
    }
	
	if(isset($_POST["remember_me"]) && !empty(trim($_POST["remember_me"])) && trim($_POST["remember_me"]) == "true"){
        $remember_me = true;
    }
	
	if ($error_code != 0) exit;
	
	$database = new Database();
	$connection = $database->get_connection();
	$user = new User(-1, $username, $password);
	$user_controller = new UserController($connection);
	
	if ($user_controller->user_exists($user)) {
		session_start();                            
		$_SESSION[$logged_in_session_key] = true;
		if (count($_COOKIE) > 0 && $remember_me) {
			setcookie($logged_in_session_key, true, time() + (86400 * 30), "/"); // expires in 1 month
		}
		header("location: admin.php");
	} else {
		$error_code = 1;
		$feedback_message = "Wrong username or password";
	}
	
}

?>

<script> $('#page_title_id').html('Ticket Support System | Admin Login'); </script>

<div class="form-wrapper d-flex flex-column justify-content-center align-items-center">
	<div class="form-header bg-primary container-fluid d-flex align-items-center">
		<p class="h5 text-white text-left p-0 m-0 mt-2 mb-2">Admin Login</p>
	</div>
	<form class="bg-white p-4 container-fluid" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<?PHP 
			if ($error_code == 1) {
				echo '<div class="alert alert-danger" role="alert">' . $feedback_message . '</div>';
			}
		?>
		<div class="form-group">
			<label for="usernameTextId">Username</label>
			<input type="text" class="form-control" id="usernameTextId" placeholder="Username" name="username" required>
		</div>
		<div class="form-group">
			<label for="passwordTextId">Password</label>
			<input type="password" class="form-control" id="passwordTextId" placeholder="Password" name="password" required>
		</div>
		<div class="form-check mb-2">
			<input type="checkbox" class="form-check-input" id="rememberMeCheckId" name="remember_me" value="true">
			<label class="form-check-label" for="rememberMeCheckId">Stay Logged in</label>
		</div>
		<div class="container-fluid d-flex justify-content-center">
			<button type="submit" class="btn btn-primary" title="Login">Login</button>
		</div>
	</form>
</div>
		
<?PHP include 'footer.php'; ?>