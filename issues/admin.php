<?PHP

$logged_in_session_key = "loggedin";
$page_session_key = "page";
$filter_priority_session_key = "priority";
$filter_state_session_key = "state";
$filter_order_session_key = "order";

session_start();

if((!isset($_SESSION[$logged_in_session_key]) || 
	$_SESSION[$logged_in_session_key] != true) &&
	(count($_COOKIE) <= 0 ||
	!isset($_COOKIE[$logged_in_session_key]) || 
	$_COOKIE[$logged_in_session_key] != true)){

    header("location: login.php");
    exit;
}

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/config/database.php");

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueTypeController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/departmentController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/stateController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/priorityController.php");

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
$state_controller = new StateController($connection);
$priority_controller = new PriorityController($connection);

$order_options = array("Latest", "Oldest");
$filter_order_value = $order_options[0];
if(isset($_SESSION[$filter_order_session_key])) {
	$filter_order_value = $_SESSION[$filter_order_session_key];
} else {
	$_SESSION[$filter_order_session_key] = $filter_order_value;
}

$filter_priority_value = "";
if(isset($_SESSION[$filter_priority_session_key])) {
	$filter_priority_value = $_SESSION[$filter_priority_session_key];
} else {
	$filter_priority_value = $priority_controller->get_default()->value;
	$_SESSION[$filter_priority_session_key] = $filter_priority_value;
}

$filter_state_value = "";
if(isset($_SESSION[$filter_state_session_key])) {
	$filter_state_value = $_SESSION[$filter_state_session_key];
} else {
	$filter_state_value = $state_controller->get_default()->value;
	$_SESSION[$filter_state_session_key] = $filter_state_value;
}

$filter_page_value = 1;
if(isset($_SESSION[$page_session_key])) {
	$filter_page_value = $_SESSION[$page_session_key];
}

$error_code = 0;
$success_code = 0;
$feedback_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST" &&
	$_POST["form_action"] == "DELETE_ROW") {
	if ($issue_controller->delete_issue(trim($_POST["issue_id"]))) {
		$success_code = 1;
	} else {
		$error_code = 1;
	}
}

if($_SERVER["REQUEST_METHOD"] == "POST" &&
	$_POST["form_action"] == "SAVE_ROW") {
	
	$issue_id = $_POST["issue_id"];
	
	if (empty(trim($_POST["no_" . $issue_id . "_state"])) ||
		empty(trim($_POST["no_" . $issue_id . "_comment"])) ||
		empty(trim($_POST["no_" . $issue_id . "_description"])) ||
		empty(trim($_POST["no_" . $issue_id . "_priority"])) ||
		empty(trim($_POST["no_" . $issue_id . "_issue_type"]))) {
			$error_code = 1;
	}
	
	$issue_type = IssueType::construct_create(trim($_POST["no_" . $issue_id . "_issue_type"]));
	$state = State::construct_create(trim($_POST["no_" . $issue_id . "_state"]));
	$priority = Priority::construct_create(trim($_POST["no_" . $issue_id . "_priority"]));
	$comment = $_POST["no_" . $issue_id . "_comment"];
	$description = $_POST["no_" . $issue_id . "_description"];
	
	$issue = Issue::construct_update(
		$issue_id,
		$issue_type,
		$description,
		$comment,
		$state,
		$priority
	);
	
	if ($issue_controller->update($issue)) {
		$success_code = 1;
	} else {
		$error_code = 1;
	}
}

if($_SERVER["REQUEST_METHOD"] == "POST" &&
	$_POST["form_action"] == "FILTER") {

	$filter_priority_value = $_POST["filter_priority_value"];
	$filter_state_value = $_POST["filter_state_value"];
	$filter_page_value = $_POST["filter_page_value"];
	$filter_order_value = $_POST["filter_order_value"];

	$_SESSION[$filter_state_session_key] = $filter_state_value;
	$_SESSION[$filter_priority_session_key] = $filter_priority_value;
	$_SESSION[$filter_order_session_key] = $filter_order_value;
	$_SESSION[$page_session_key] = $filter_page_value;
}

$filter_pages = $issue_controller->get_by_filter_pages($filter_priority_value, $filter_state_value);
if ($filter_page_value > $filter_pages) {
	$filter_page_value--;
	if ($filter_page_value == 0) $filter_page_value = 1;
}
$_SESSION[$page_session_key] = $filter_page_value;

$issue_types = $issue_type_controller->get_all();
$departments = $department_controller->get_all();
$states = $state_controller->get_all();
$priorities = $priority_controller->get_all();
$filter_is_ascending = false;
if ($filter_order_value == $order_options[1]) $filter_is_ascending = true;
$issues = $issue_controller->get_by_filter($filter_priority_value, $filter_state_value, $filter_page_value, $filter_is_ascending);
$filter_pages = $issue_controller->get_by_filter_pages($filter_priority_value, $filter_state_value);

?>

<?PHP include 'header.php'; ?>

<script> $('#page_title_id').html('Ticket Support System | Administrator Panel'); </script>

<div class="form-wrapper d-flex flex-column justify-content-start align-items-center container-fluid" style="min-height: 70vh;max-width: 1800px;">
	
	<div class="form-header container-fluid">
		<a href="/issues/logout.php" class="link-light text-white float-right mb-2">Logout</a>
	</div>

	<div class="form-header bg-primary container-fluid d-flex align-items-center">
		<p class="h5 text-white text-left p-0 m-0 mt-2 mb-2">Administrator Panel</p>
		<div>
			
		</div>
	</div>
	
	<div class="form-header container-fluid bg-white pt-2 pb-2 d-flex justify-content-between">
		<div class="row">
			<form class="d-none" id="filter_form_id" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"></form>
			<input type="hidden" name="filter_priority_value" id="filter_priority_value_id" value="<?= $filter_priority_value ?>" form="filter_form_id">
			<input type="hidden" name="filter_state_value" id="filter_state_value_id" value="<?= $filter_state_value ?>" form="filter_form_id">
			<input type="hidden" name="filter_page_value" id="filter_page_value_id" value="<?= $filter_page_value ?>" form="filter_form_id">
			<input type="hidden" name="filter_order_value" id="filter_order_value_id" value="<?= $filter_order_value ?>" form="filter_form_id">
			<input type="hidden" name="filter_pages" id="filter_pages_id" value="<?= $filter_pages ?>" form="filter_form_id">
			<input type="hidden" name="form_action" id="filter_form_action_id" value="FILTER" form="filter_form_id">
			<div class="col-sm-4">
				<label for="filter_priority_select_id">Priority</label>
				<select class="form-control" id="filter_priority_select_id" onchange="TriggerFilter();">
					<?PHP
						foreach($priorities as $priority) {
							$selected = '';
							if ($priority->value == $filter_priority_value) $selected = 'selected';
							echo '<option ' . $selected . ' >' . $priority->value . '</option>';
						}
					?>
				</select>
			</div>
			<div class="col-sm-4 pl-0">
				<label for="filter_state_select_id">State</label>
				<select class="form-control" id="filter_state_select_id" onchange="TriggerFilter();">
					<?PHP
						foreach($states as $state) {
							$selected = '';
							if ($state->value == $filter_state_value) $selected = 'selected';
							echo '<option ' . $selected . ' >' . $state->value . '</option>';
						}
					?>
				</select>
			</div>
			<div class="col-sm-4 pl-0">
				<label for="filter_order_select_id">Order</label>
				<select class="form-control" id="filter_order_select_id" onchange="TriggerFilter();">
					<?PHP
						foreach($order_options as $order) {
							$selected = '';
							if ($order == $filter_order_value) $selected = 'selected';
							echo '<option ' . $selected . ' >' . $order . '</option>';
						}
					?>
				</select>
			</div>
		</div>
		<div>
			<button type="button" class="btn btn-primary" title="Refresh" onclick="window.location.replace('/issues/admin');">Refresh</button>
			<form class="d-none" id="export_form_id" action="./src/services/exportCSV.php" method="post"></form>
			<button type="button" class="btn btn-primary ml-2" title="Export" onclick="$('#export_form_id').submit();">Export</button>
		</div>
	</div>
	
	<!-- POPUP WINDOW MODAL
		<div class="d-flex flex-column justify-content-center align-items-center position-fixed popup-border bg-white d-none" style="z-index:10;" id="popup_window_id">
			<div class="form-header bg-primary container-fluid d-flex align-items-center">
				<p class="h5 text-white text-left p-0 m-0 mt-2 mb-2">Μήνυμα Συστήματος</p>
			</div>
			<div class="bg-white p-4 container-fluid"><?= $feedback_message ?></div>
			<button type="button" class="btn btn-primary mb-3" title="Εξαγωγή" onclick="$('#popup_window_id').toggleClass('d-none')">Κλείσιμο</button>
		</div>
	-->
	
	<table class="table table-striped container-fluid bg-white pb-0 mb-0">
		<thead>
			<tr>
				<th scope="col align-top">ID</th>
				<th scope="col">
					<p class="mb-1">Priority</p>
					<p class="mb-0">State</p>
				</th>
				<th scope="col">Category</th>
				<th scope="col">Description</th>
				<th scope="col">
					<p class="mb-1">Office</p>
					<p class="mb-0">Department</p>
				</th>
				<th scope="col">
					<p class="mb-1">Title</p>
					<p class="mb-0">Fullname</p>
				</th>
				<th scope="col">
					<p class="mb-1">Submitted</p>
					<p class="mb-0">Date</p>
				</th>
				<th scope="col">Comments</th>
				<th scope="col">Actions</th>
			</tr>
		</thead>
		<tbody>
			
			<?PHP if (count($issues) < 1) { 
				echo '<tr><td colspan="9">
						<p class="d-flex justify-content-center mb-0">No records found</p>
					</td></tr>'; 
			} ?>
			
			<?PHP foreach($issues as $issue) { ?>

				<tr>
					<th scope="row">
						<?= $issue->issue_id ?>
						<form class="d-none" id="no_<?= $issue->issue_id ?>_form_id" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"></form>
						<input type="hidden" name="issue_id" id="<?= $issue->issue_id ?>" value="<?= $issue->issue_id ?>" form="no_<?= $issue->issue_id ?>_form_id">
						<input type="hidden" name="form_action" id="no_<?= $issue->issue_id ?>_form_action_id" value="" form="no_<?= $issue->issue_id ?>_form_id">
					</th>
					<td>
						<?PHP
							$htmlPriorities = '';
							$selectedPriorityValue = '';
							foreach($priorities as $priority) {
								$selected = '';
								if ($issue->priority->priority_id == $priority->priority_id) {
									$selected = 'selected';
									$selectedPriorityValue = $priority->value;
								}
								$htmlPriorities .= '<option ' . $selected . ' >' . $priority->value . '</option>';
							}
						?>
						<select class="row form-control mb-2 disabled-input" form="no_<?= $issue->issue_id ?>_form_id" name="no_<?= $issue->issue_id ?>_priority" id="no_<?= $issue->issue_id ?>_priority_id" readonly value="<?= $selectedPriorityValue ?>">
							<?= $htmlPriorities ?>
						</select>
						<?PHP
							$htmlStates = '';
							$selectedStateValue = '';
							foreach($states as $state) {
								$selected = '';
								if ($issue->state->state_id == $state->state_id) {
									$selected = 'selected';
									$selectedStateValue = $state->value;
								}
								$htmlStates .= '<option ' . $selected . ' >' . $state->value . '</option>';
							}
						?>
						<select class="row form-control disabled-input" form="no_<?= $issue->issue_id ?>_form_id" name="no_<?= $issue->issue_id ?>_state" id="no_<?= $issue->issue_id ?>_state_id" readonly value="<?= $selectedStateValue ?>">
							<?= $htmlStates ?>
						</select>
					</td>
					<td>
						<?PHP
							$htmlIssueTypes = '';
							$selectedIssueTypeValue = '';
							foreach($issue_types as $issue_type) {
								$selected = '';
								if ($issue->issue_type->issue_type_id == $issue_type->issue_type_id) {
									$selected = 'selected';
									$selectedIssueTypeValue = $issue_type->value;
								}
								$htmlIssueTypes .= '<option ' . $selected . ' >' . $issue_type->value . '</option>';
							}
						?>
						<select class="row form-control disabled-input" form="no_<?= $issue->issue_id ?>_form_id" name="no_<?= $issue->issue_id ?>_issue_type" id="no_<?= $issue->issue_id ?>_issue_type_id" readonly value="<?= $selectedIssueTypeValue ?>">
							<?= $htmlIssueTypes ?>
						</select>
					</td>
					<td>
						<textarea class="form-control" form="no_<?= $issue->issue_id ?>_form_id" name="no_<?= $issue->issue_id ?>_description" id="no_<?= $issue->issue_id ?>_description_id" rows="3" readonly value="<?= $issue->description ?>"><?= $issue->description ?></textarea>
					</td>
					<td><?= $issue->department->value ?>/<?= $issue->office ?></td>
					<td><?= $issue->title ?> <?= $issue->full_name ?></td>
					<td><?= $issue->create_date ?></td>
					<td>
						<textarea class="form-control" form="no_<?= $issue->issue_id ?>_form_id" name="no_<?= $issue->issue_id ?>_comment" id="no_<?= $issue->issue_id ?>_comment_id" rows="3" readonly value="<?= $issue->comment ?>"><?= $issue->comment ?></textarea>
					</td>
					<td>
						<button type="button" class="btn btn-primary btn-sm" id="no_<?= $issue->issue_id ?>_edit_id" onclick="UnlockRow(<?= $issue->issue_id ?>);" title="Επεξεργασία" style="margin:2px;">
							<i class="fa-solid fa-pen-to-square"></i>
						</button>
						<button type="button" class="btn btn-primary btn-sm d-none" id="no_<?= $issue->issue_id ?>_cancel_id" onclick="LockRow(<?= $issue->issue_id ?>);" title="Ακύρωση" style="margin:2px;">
							<i class="fa-solid fa-xmark"></i>
						</button>
						<button type="button" class="btn btn-primary btn-sm" id="no_<?= $issue->issue_id ?>_delete_id" onclick="DeleteRow(<?= $issue->issue_id ?>)" title="Διαγραφή" style="margin:2px;">
							<i class="fa-solid fa-trash"></i>
						</button>
						<button type="button" class="btn btn-primary btn-sm" id="no_<?= $issue->issue_id ?>_save_id" onclick="SaveRow(<?= $issue->issue_id ?>)" title="Αποθήκευση" style="margin:2px;">
							<i class="fa-solid fa-floppy-disk"></i>
						</button>
					</td>
				</tr>
			
			<?PHP } ?>					

		</tbody>
	</table>
	
	<div class="container-fluid bg-white mb-4">
		<?php
			$filtered_issues_counter = $issue_controller->get_count_by_filter($filter_priority_value, $filter_state_value);
		?>
		<p class="font-weight-light" style="font-size:14px;">Number of reports: <?= $filtered_issues_counter ?></p>
	</div>
	
	<div class="container-fluid d-flex justify-content-end">
		<nav aria-label="">
			<ul class="pagination text-dark">
				<?PHP
					$disabled_left = "";
					if ($filter_page_value <= 1) { $disabled_left = "disabled"; }
					$disabled_right = "";
					if ($filter_page_value >= $filter_pages) { $disabled_right = "disabled"; }
					//echo '<script>alert('.$filter_pages.')</script>';
				?>
				<li class="page-item <?= $disabled_left ?>"><button class="page-link text-dark" onclick="PageLeft();">Previous</button></li>
				<?PHP if ($filter_page_value > 1) 
					echo '<li class="page-item <?= $disabled_left ?>">
							<button class="page-link text-dark" onclick="GoToPage(1);">1</button>
						</li>'; 
				?>
				<li class="page-item active"><a class="page-link text-dark"><?= $filter_page_value ?></a></li>
				<?PHP if ($filter_page_value < $filter_pages) 
					echo '<li class="page-item <?= $disabled_left ?>">
							<button class="page-link text-dark" onclick="GoToPage(' . $filter_pages . ');">' . $filter_pages . '</button>
						</li>'; 
				?>
				<li class="page-item <?= $disabled_right ?>"><button class="page-link text-dark" onclick="PageRight();">Next</button></li>
			</ul>
		</nav>
	</div>
	
</div>

<?PHP include 'footer.php'; ?>