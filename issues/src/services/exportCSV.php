<?php

# https://www.php.net/manual/en/function.fputcsv.php FROM nate at example dot com <3
function fputcsv2 ($fh, array $fields, $delimiter = ';', $enclosure = '"', $mysql_null = false) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $output = array();
    foreach ($fields as $field) {
        if ($field === null && $mysql_null) {
            $output[] = 'NULL';
            continue;
        }

        $output[] = preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field) ? (
            $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure
        ) : $field;
    }

    fwrite($fh, join($delimiter, $output) . "\n");
}

include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/config/database.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/issueController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/stateController.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/issues/src/controllers/priorityController.php");

$database = new Database();
$connection = $database->get_connection();

$filter_priority_session_key = "priority";
$filter_state_session_key = "state";

$issue_controller = new IssueController($connection);
$state_controller = new StateController($connection);
$priority_controller = new PriorityController($connection);

session_start();

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

$issues = $issue_controller->get_by_filter($filter_priority_value, $filter_state_value, -1, true);

$fileName = "issues_" . date('Y-m-d') . ".csv"; 
 
// $fields = array(
//     'Issue ID', 
//     'Create Date', 
//     'title', 
//     'Fullname', 
//     'Infantry Sector', 
//     'office', 
//     'Issue Type',
//     'Description',
//     'Comments',
//     'State', 
//     'Priority'
// );

$fields = array(
    'Κωδικός Αναφοράς', 
    'Ημερομηνία Δημιουργίας', 
    'Βαθμός', 
    'Ονοματεπώνυμο', 
    'Τομέας ΠΖ', 
    'Τμήμα', 
    'Κατηγορία Προβλήματος',
    'Περιγραφή',
    'Σχόλια',
    'Κατάσταση', 
    'Προτεραιότητα'
);

header('Content-Type: application/xls; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$fileName\""); 

$output = fopen('php://output', 'w');

fputs($output, (chr(0xEF) . chr(0xBB) . chr(0xBF))); // support unicode
fputcsv2($output, $fields);

foreach($issues as $issue) {
    $line_data = array(
        $issue->issue_id, 
        $issue->create_date, 
        $issue->title, 
        $issue->full_name, 
        $issue->department->value, 
        $issue->office, 
        $issue->issue_type->value,
        $issue->description,
        $issue->comment,
        $issue->state->value,
        $issue->priority->value
    ); 
    fputcsv2($output, $line_data);
}

die();

?>