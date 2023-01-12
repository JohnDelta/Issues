<?PHP

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/config/database.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueTypeController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/departmentController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/issueModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/departmentModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/issueTypeModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/stateModel.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/models/priorityModel.php");

$database = new Database();
$connection = $database->get_connection();

$issue_type_controller = new IssueTypeController($connection);
$department_controller = new DepartmentController($connection);
$issue_controller = new IssueController($connection);

$issue_types = $issue_type_controller->get_all();
$departments = $department_controller->get_all();

$error_code = 0;
$success_code = 0;

if($_SERVER["REQUEST_METHOD"] == "POST") {

	$error_code = 0;
	$success_code = 0;

	if (empty(trim($_POST["title"])) ||
		empty(trim($_POST["full_name"])) ||
		empty(trim($_POST["department"])) ||
		empty(trim($_POST["office"])) ||
		empty(trim($_POST["description"])) ||
		empty(trim($_POST["issue_type"]))) {
			$error_code = 1;
	}
	
	$issue_type = IssueType::construct_create(trim($_POST["issue_type"]));
	$department = Department::construct_create(trim($_POST["department"]));
	$issue = Issue::construct_create(
		trim($_POST["title"]),
		trim($_POST["full_name"]),
		$department,
		$issue_type,
		trim($_POST["office"]),
		trim($_POST["description"])
	);

	if ($issue_controller->create($issue)) {
		$success_code = 1;
	} else {
		$error_code = 1;
	}

}

?>

<?PHP include 'header.php'; ?>

<script> $('#page_title_id').html('Ticket Support System | Submit Report Form'); </script>

<div class="form-wrapper d-flex flex-column justify-content-center align-items-center" style="max-width: 1300px;">
	<div class="form-header bg-primary container-fluid d-flex align-items-center">
		<p class="h5 text-white text-left p-0 m-0 mt-2 mb-2">Submit Report Form</p>
	</div>

	<?PHP
		$alert_class = 'alert-success';
		$feedback_message = 'Your report have been submitted successfuly!';
		if ($error_code == 1) {
			$alert_class = 'alert-danger';
			$feedback_message = 'An error occurred while submitting your report. Please try again.';
		}
		if ($error_code == 1 || $success_code == 1) {
			echo '
				<div class="bg-white p-4 container-fluid d-flex flex-column justify-content-center align-items-center">
				<div class="alert ' . $alert_class . '" role="alert">' . $feedback_message . '</div>
				<button type="click" class="btn btn-primary" onclick="window.location.replace(\'\/issues\')">Return</button>	
				</div>
			';
		} else {
	?>

	<form class="bg-white p-4 container-fluid" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="row">
			<div class="form-group col-sm-4">
				<label for="titleTextId">Title</label>
				<input type="text" name="title" class="form-control" id="titleTextId" placeholder="title" required>
			</div>
			<div class="form-group col-sm-8">
				<label for="fullnameTextId">Fullname</label>
				<input type="text" name="full_name" class="form-control" id="fullnameTextId" placeholder="Fullname" required>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-6">
				<label for="departmentSelectId">Department</label>
				<select class="form-control" id="departmentSelectId" name="department">
					<?PHP
						foreach($departments as $department) {
							$selected = '';
							if ($department->is_default) $selected = 'selected';
							echo '<option ' . $selected . ' >' . $department->value . '</option>';
						}
					?>
				</select>
			</div>
			<div class="form-group col-sm-6">
				<label for="officeTextId">Office</label>
				<input type="text" name="office" class="form-control" id="officeTextId" placeholder="Office" required>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-6">
				<label for="issueTypeSelectId">Issue Type</label>
				<select class="form-control" id="issueTypeSelectId" name="issue_type">
					<?PHP
						foreach($issue_types as $issue_type) {
							$selected = '';
							if ($issue_type->is_default) $selected = 'selected';
							echo '<option ' . $selected . ' >' . $issue_type->value . '</option>';
						}
					?>
				</select>
			</div>
			<div class="col-sm-6"></div>
		</div>
		<div class="form-group">
			<label for="issueDescriptionTextareaId">Description</label>
			<textarea class="form-control" name="description" id="issueDescriptionTextareaId" rows="3" required></textarea>
		</div>
		<div class="container-fluid d-flex justify-content-center">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</form>

	<?PHP } ?>
</div>

<?PHP include 'footer.php'; ?>