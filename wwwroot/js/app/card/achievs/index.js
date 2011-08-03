
var elems   = null;
var Tabs    = null;
var Toolbar = null;

var period = null;

function init()
{
	JsCalendarInit(BASE_URL + '/js/lib/JsCalendar');
	JsCalendar.handler = Card.calendarHandler;

	
	
	elems = window.document.card.elements;
	
	elems.period.onchange = periodOnchangeHandler;
	
	period = elems.period.options[elems.period.selectedIndex].value;
	
	Card.init(period);
	
	//alert(period);
	
	Tabs = new Js.TabPanel();
	Tabs.addTab('tabs-item-tasks', 'tabs-body-tasks');
	Tabs.addTab('tabs-item-compets', 'tabs-body-compets');
	Tabs.addTab('tabs-item-trains', 'tabs-body-trains');
	Tabs.addTab('tabs-item-comments', 'tabs-body-comments');
	Tabs.addTab('tabs-item-personal', 'tabs-body-personal');
	
	
	Toolbar = new Js.Toolbar('toolbarBox');
	
	if (count_func > 0)	 Card.displayRatio();		// выводим соотношение весов
	
	switch (elems.status_id.value) {
		case 'NEW':			// новая карточка
		case 'PLN':			// планирование (выставление бизнес-целей и тренингов)
			
			if (USER_ROLE & ROLE_EMPLOYEE) {
				
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModePersonalPlan();
				Card.setEditEmpComments();
			}
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
				Card.setModePlan();
			}
			if (USER_ROLE & ROLE_FUNC_MANAGER) {
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModePlanFuncMng();
			}

			break;
			
		case 'CPN':			// согласование планирования
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
			}	
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value != '1') {
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value == '' || 
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value == '') {
				Toolbar.addItem({text: 'Отклонить<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectPlan}).addClassName('toolbar-item-reject');
			}
			break;
			
		case 'RTG':			// оценка (выставление рейтингов)
			if (USER_ROLE & ROLE_EMPLOYEE) {
				
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModeRatePersonal();
				Card.setEditEmpComments();

			}
			
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
				Card.setModeRate();
			}
			if (USER_ROLE & ROLE_FUNC_MANAGER) {
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModeRateFuncMng();
			}
			
			break;
			
		case 'CRG':			// согласование оценки
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value != '1') {
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value == '' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value == '') {
				Toolbar.addItem({text: 'Отклонить<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectRate}).addClassName('toolbar-item-reject');
			}
			if (USER_ROLE & ROLE_EMPLOYEE) {
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				
			}
			
			break;
			
		case 'CLS':			// закрытая карточка
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}
			break;
			
		default:
			break;
	}
	Toolbar.addItem({text: 'Обновить<br><span class="translate_toolbar">Refresh</span>', onclick: toolbarItemRefresh}).addClassName('toolbar-item-refresh');
	Toolbar.addItem({text: 'Печатная форма<br><span class="translate_toolbar">Print</span>', onclick: toolbarItemPrint}).addClassName('toolbar-item-print');
	
	// если для определенной карточки роль пользователь совпадает с ролью непосредственного руководителя то делаем заметки видимыми
	
	if (USER_ROLE & ROLE_EMPLOYEE) {
		Card.setEditPersonalNotes();
	}
	
	if (USER_ROLE & ROLE_MANAGER) {
		Card.setEditNotes();			
		Card.setEditCompetenceNotes();
	}				
	
	if (USER_ROLE & ROLE_FUNC_MANAGER) {
		Card.setEditFuncNotes();
	}
	
	
	
}

function sendEmail()
{
	var employee_fio_div = document.getElementById('employee_fio');
	var employee_fio_value = employee_fio_div.value;
	
	var subject = "Согласование карточки в системе Управление Достижениями";
	
	var bodytext = 'Уважаемые коллеги!%0A%0AИнформирую вас о том, что карточка сотрудника ' + employee_fio_value + ' выставлена на согласование.%0AОзнакомьтесь, пожалуйста, с внесенными данными и согласуйте их.%0A%0AСпасибо!%0A%0A%0A%0ADear colleagues,%0A%0APlease, be informed that performance management card of ' + employee_fio_value + ' is waiting for your approval.%0AAcquaint with information and submit it.%0A%0AThank you.';

	parent.location.href="mailto:" + emails + "?subject=" + subject + "&body=" + bodytext;
	
}

function toolbarItemSave()
{
	Card.save();
	
}

function toolbarItemSaveClose()
{
	Card.save();
	if (confirm("Вы уверены, что хотите выйти после сохранения карточки?\nAre you sure to quit after card save?")) 
	{
		window.parent.close();
	}

}



function toolbarItemEditPlan()
{
	var msg = 'Внимание! При переходе в статус планирования все ранее полученные согласования будут сняты!\n\nAttention! When shifting to the planning status all previous plan confirmations will be removed!';
	if (window.confirm(msg)) {
		elems['approvals[plan_mng_status]'].value = '';
		Card.save();
		//document.forms.card.submit();
	}
}

function toolbarItemEditRate()
{
	var msg = 'Внимание! При переходе в статус оценки все ранее полученные согласования оценки будут сняты!\n\nAttention! When shifting to rating status all previous rating confirmations will be removed!';
	if (window.confirm(msg)) {
		elems['approvals[rate_mng_status]'].value = '';
		Card.save();
		//document.forms.card.submit();

	}
}

function toolbarItemApprovalPlan()
{
	if (USER_ROLE & ROLE_MANAGER) {
		if (!Card.checkSetPlan(count_func)) return;
		elems['approvals[plan_mng_id]'].value = elems.userId.value;
		elems['approvals[plan_mng_status]'].value = 1;
		
		sendEmail();
	}
	if (USER_ROLE & ROLE_EMPLOYEE) {
		elems['approvals[plan_emp_status]'].value = 1;
	}
	if (USER_ROLE & ROLE_HIGH_MANAGER) {
		elems['approvals[plan_hmg_id]'].value = elems.userId.value;
		elems['approvals[plan_hmg_status]'].value = 1;
	}
	
	if (count_func == 0) {		
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 1;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER) {							
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 1;
	}	
	
	Card.save();
}

function toolbarItemApprovalRate()
{
	
	if (USER_ROLE & ROLE_MANAGER) {
		if (!Card.checkSetRatings(count_func)) {
			return;
		}
		if (!Card.checkBalanceTasks()) return;
		if (!Card.checkBalanceCompetences()) return;
		elems['approvals[rate_mng_id]'].value = elems.userId.value;
		elems['approvals[rate_mng_status]'].value = 1;
		
		sendEmail();
	}
	if (USER_ROLE & ROLE_EMPLOYEE) {
		elems['approvals[rate_emp_status]'].value = 1;
	}
	if (USER_ROLE & ROLE_HIGH_MANAGER) {
		elems['approvals[rate_hmg_id]'].value = elems.userId.value;
		elems['approvals[rate_hmg_status]'].value = 1;
	}
	
	if (count_func == 0) {		
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 1;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER) {							
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 1;
	}	
	
	Card.save();
}

function toolbarItemRejectPlan()
{
	if (USER_ROLE & ROLE_EMPLOYEE) {
		elems['approvals[plan_emp_status]'].value = 0;
	}
	if (USER_ROLE & ROLE_HIGH_MANAGER) {
		elems['approvals[plan_hmg_id]'].value = elems.userId.value;
		elems['approvals[plan_hmg_status]'].value = 0;
	}
	
	if (count_func == 0) {		
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 0;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER) {							
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 0;
	}
	
	Card.save();
} 

function toolbarItemRejectRate()
{
	if (USER_ROLE & ROLE_EMPLOYEE) {
		elems['approvals[rate_emp_status]'].value = 0;
	}
	if (USER_ROLE & ROLE_HIGH_MANAGER) {
		elems['approvals[rate_hmg_id]'].value = elems.userId.value;
		elems['approvals[rate_hmg_status]'].value = 0;
	}
	
	if (count_func == 0) {		
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 0;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER) {							
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 0;
	}
	
	Card.save();
}

function toolbarItemRefresh()
{
	location.reload();
}

function toolbarItemPrint()
{
	var personId = elems.person_id.value;
	var period = elems.period.options[elems.period.selectedIndex].value;
	
	var url = BASE_URL + '/card/achievs/print/personid/' + personId + '/period/' + period;
	
	//Js.open(url, '', 1000, 714, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
	var hei = screen.height-250;
	Js.open(url, '',1000, hei, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
}

function periodOnchangeHandler()
{
	/*
	var msg = 'Внимание! При переходе в другой период все несохраненные изменения';
	msg += ' в карточке за текущий период будут потеряны!';
	if (!window.confirm(msg)) {
		return false;
	}
	*/
	$('#loading', parent.document.body).css({
		display: 'block'
	});
	
	var personId = elems.person_id.value;
	var period = elems.period.options[elems.period.selectedIndex].value;
	
	var url = BASE_URL + '/card/achievs/index/personid/' + personId + '/period/' + period;
	location.replace(url);
}

function openNotes(taskId, is_personal)
{
	var url = BASE_URL + '/card/achievs-task-notes/index/taskid/' + taskId + '/is_personal/' + is_personal;

	Js.open(url, '', 400, 510);
}

function openNotesCompetence(competId, is_personal)
{
	var url = BASE_URL + '/card/achievs-competence-notes/index/competid/' + competId + '/is_personal/' + is_personal;
	
	Js.open(url, '', 400, 510);
}

function openNotesTraining(trainId)
{
	var url = BASE_URL + '/card/achievs-training-notes/index/trainId/' + trainId;
	
	Js.open(url, '', 400, 510);
}


