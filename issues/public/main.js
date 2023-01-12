
let UnlockRow = function(rowNo) {
	let descriptionTextarea = $("#no_" + rowNo + "_description_id");
	let commentsTextarea = $("#no_" + rowNo + "_comment_id");
	let stateSelect = $("#no_" + rowNo + "_state_id");
	let prioritySelect = $("#no_" + rowNo + "_priority_id");
	let issueTypeSelect = $("#no_" + rowNo + "_issue_type_id");
	let editButton = $("#no_" + rowNo + "_edit_id");
	let cancelButton = $("#no_" + rowNo + "_cancel_id");
	
	cancelButton.toggleClass("d-none");
	editButton.toggleClass("d-none");
	
	descriptionTextarea.attr('readonly', null);
	commentsTextarea.attr('readonly', null);
	stateSelect.attr('readonly', null);
	prioritySelect.attr('readonly', null);
	issueTypeSelect.attr('readonly', null);
	
	stateSelect.removeClass('disabled-input');
	prioritySelect.removeClass('disabled-input');
	issueTypeSelect.removeClass('disabled-input');
}

let LockRow = function(rowNo) {
	let descriptionTextarea = $("#no_" + rowNo + "_description_id");
	let commentsTextarea = $("#no_" + rowNo + "_comment_id");
	let stateSelect = $("#no_" + rowNo + "_state_id");
	let prioritySelect = $("#no_" + rowNo + "_priority_id");
	let issueTypeSelect = $("#no_" + rowNo + "_issue_type_id");
	let editButton = $("#no_" + rowNo + "_edit_id");
	let cancelButton = $("#no_" + rowNo + "_cancel_id");
	
	cancelButton.toggleClass("d-none");
	editButton.toggleClass("d-none");
	
	descriptionTextarea.attr('readonly', 'readonly');
	commentsTextarea.attr('readonly', 'readonly');
	stateSelect.attr('readonly', 'readonly');
	prioritySelect.attr('readonly', 'readonly');
	issueTypeSelect.attr('readonly', 'readonly');
	
	stateSelect.addClass('disabled-input');
	prioritySelect.addClass('disabled-input');
	issueTypeSelect.addClass('disabled-input');
	
	descriptionTextarea.val(descriptionTextarea.attr('value'));
	commentsTextarea.val(commentsTextarea.attr('value'));
	stateSelect.val(stateSelect.attr('value'));
	prioritySelect.val(prioritySelect.attr('value'));
	issueTypeSelect.val(issueTypeSelect.attr('value'));
}

let DeleteRow = function(rowNo) {
	let rowFormActionInput = $("#no_" + rowNo + "_form_action_id");
	let rowForm = $("#no_" + rowNo + "_form_id");
	rowFormActionInput.val("DELETE_ROW");
	rowForm.submit();
}

let SaveRow = function(rowNo) {
	let rowFormActionInput = $("#no_" + rowNo + "_form_action_id");
	let rowForm = $("#no_" + rowNo + "_form_id");
	rowFormActionInput.val("SAVE_ROW");
	rowForm.submit();
}

let TriggerFilter = function() {
	let filterPrioritySelect = $('#filter_priority_select_id');
	let filterStateSelect = $('#filter_state_select_id');
	let filterOrderSelect = $('#filter_order_select_id');
	
	let filterPriorityValue = $('#filter_priority_value_id');
	let filterOrderValue = $('#filter_order_value_id');
	let filterStateValue = $('#filter_state_value_id');
	
	let filterForm = $('#filter_form_id');
	
	filterPriorityValue.val(filterPrioritySelect.val());
	filterStateValue.val(filterStateSelect.val());
	filterOrderValue.val(filterOrderSelect.val());
	
	filterForm.submit();
}

let PageRight = function() {
	let filterPages = $('#filter_pages_id');
	let filterPageValue = $('#filter_page_value_id');
	if (Number(filterPages.val()) > Number(filterPageValue.val())) {
		filterPageValue.val(Number(filterPageValue.val()) + 1);
		let filterForm = $('#filter_form_id');
		filterForm.submit();
	}
}

let PageLeft = function() {
	let filterPageValue = $('#filter_page_value_id');
	if (Number(filterPageValue.val()) > 1) {
		filterPageValue.val(Number(filterPageValue.val()) - 1);
		let filterForm = $('#filter_form_id');
		filterForm.submit();
	}
}

let GoToPage = function (page) {
	let filterPageValue = $('#filter_page_value_id');
	filterPageValue.val(Number(page));
	let filterForm = $('#filter_form_id');
	filterForm.submit();
}