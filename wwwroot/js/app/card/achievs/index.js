
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

	period = $('#period option:selected').attr('period');

	Card.init(period);

	//alert(period);

	Tabs = new Js.TabPanel();
	Tabs.addTab('tabs-item-tasks', 'tabs-body-tasks');
	Tabs.addTab('tabs-item-compets', 'tabs-body-compets');
	Tabs.addTab('tabs-item-trains', 'tabs-body-trains');
	Tabs.addTab('tabs-item-comments', 'tabs-body-comments');
	Tabs.addTab('tabs-item-personal', 'tabs-body-personal');
	if(has_statistics)
		Tabs.addTab('tabs-item-statistics', 'tabs-body-statistics');


	Toolbar = new Js.Toolbar('toolbarBox');

	if (count_func > 0)	 Card.displayRatio();		// ������� ����������� �����

	switch (elems.status_id.value) {
		case 'NEW':			// ����� ��������
		case 'PLN':			// ������������ (����������� ������-����� � ���������)

			if (USER_ROLE & ROLE_EMPLOYEE) {

				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModePersonalPlan();
				Card.setEditEmpComments();
			}
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Toolbar.addItem({text: '�����������<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
				Card.setModePlan();
			}
			if (USER_ROLE & ROLE_FUNC_MANAGER) {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModePlanFuncMng();
			}

			break;

		case 'CPN':			// ������������ ������������
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: '�������������<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value != '1') {
				Toolbar.addItem({text: '�����������<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value == '' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value == '') {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectPlan}).addClassName('toolbar-item-reject');
			}
			break;

		case 'RTG':			// ������ (����������� ���������)
			if (USER_ROLE & ROLE_EMPLOYEE) {

				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModeRatePersonal();
				Card.setEditEmpComments();

			}

			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Toolbar.addItem({text: '�������������<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
				Toolbar.addItem({text: '�����������<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
				Card.setModeRate();
			}
			if (USER_ROLE & ROLE_FUNC_MANAGER) {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');
				Card.setModeRateFuncMng();
			}

			break;

		case 'CRG':			// ������������ ������
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: '�������������<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value != '1') {
				Toolbar.addItem({text: '�����������<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
			}
			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value == '' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value == '') {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectRate}).addClassName('toolbar-item-reject');
			}
			if (USER_ROLE & ROLE_EMPLOYEE) {
				Toolbar.addItem({text: '���������<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: '�������<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');

			}

			break;

		case 'CLS':			// �������� ��������
			if (USER_ROLE & ROLE_MANAGER) {
				Toolbar.addItem({text: '�������������<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}
			break;

		default:
			break;
	}
	Toolbar.addItem({text: '��������<br><span class="translate_toolbar">Refresh</span>', onclick: toolbarItemRefresh}).addClassName('toolbar-item-refresh');
	Toolbar.addItem({text: '�������� �����<br><span class="translate_toolbar">Print</span>', onclick: toolbarItemPrint}).addClassName('toolbar-item-print');


	var posts = User.viewposts,
		IS_HR = false;
	for (var key in posts) {
		if(posts[key].pid == ' ')
		{
			IS_HR = true;
		}
	}
	if(IS_HR)
	{
		Toolbar.addItem({text: '������� ��������<br><span class="translate_toolbar">Create Card</span>', onclick: toolbarItemCreateCard}).addClassName('toolbar-item-card_create');
	
		var blocked_text;
		if(is_blocked)
		{
			 blocked_text_ru = '��������������';
			 blocked_text_en = 'Unblock';
		}
		else
		{
			blocked_text_ru = '�����������';
			blocked_text_en = 'Block';
		}

		Toolbar.addItem({text: blocked_text_ru + ' ��������<br><span class="translate_toolbar">' + blocked_text_en +' Card</span>', onclick: toolbarItemCardBlock}).addClassName('toolbar-item-card_block');
	}
	// ���� ��� ������������ �������� ���� ������������ ��������� � ����� ����������������� ������������ �� ������ ������� ��������

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

	var subject = "������������ �������� � ������� ���������� ������������";

	var bodytext = '��������� �������!%0A%0A���������� ��� � ���, ��� �������� ���������� ' + employee_fio_value + ' ���������� �� ������������.%0A������������, ����������, � ���������� ������� � ���������� ��.%0A%0A�������!%0A%0A%0A%0ADear colleagues,%0A%0APlease, be informed that performance management card of ' + employee_fio_value + ' is waiting for your approval.%0AAcquaint with information and submit it.%0A%0AThank you.';

	parent.location.href="mailto:" + emails + "?subject=" + subject + "&body=" + bodytext;

}

function toolbarItemSave()
{
	Card.save();

}

function toolbarItemSaveClose()
{
	Card.save();
	if (confirm("�� �������, ��� ������ ����� ����� ���������� ��������?\nAre you sure to quit after card save?"))
	{
		window.parent.close();
	}

}



function toolbarItemEditPlan()
{
	var msg = '��������! ��� �������� � ������ ������������ ��� ����� ���������� ������������ ����� �����!\n\nAttention! When shifting to the planning status all previous plan confirmations will be removed!';
	if (window.confirm(msg)) {
		elems['approvals[plan_mng_status]'].value = '';
		Card.save();
		//document.forms.card.submit();
	}
}

function toolbarItemEditRate()
{
	var msg = '��������! ��� �������� � ������ ������ ��� ����� ���������� ������������ ������ ����� �����!\n\nAttention! When shifting to rating status all previous rating confirmations will be removed!';
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
	var period = $('#period option:selected').attr('period');

	var url = BASE_URL + '/card/achievs/print/personid/' + personId + '/period/' + period;

	//Js.open(url, '', 1000, 714, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
	var hei = screen.height-250;
	Js.open(url, '',1000, hei, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
}

function toolbarItemCreateCard()
{
	var personId = elems.person_id.value;

	var url = BASE_URL + '/card/index/create/personid/' + personId;

	Js.open(url, '', 440, 210);
}

function toolbarItemCardBlock()
{
	$.ajax({
	    url: BASE_URL + '/card/card/block/',
		data: 'card_id=' + card_id,
		    success: function(data) {
				location.reload();
		    }
	});
}

function periodOnchangeHandler()
{
	var personId = elems.person_id.value;
	var cardid = elems.period.options[elems.period.selectedIndex].value;

	var url = BASE_URL + '/card/achievs/index/personid/' + personId + '/cardid/' + cardid;
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


$(document).ready(function(){
	$('#common_rating_confirmation_buttons .conf_button').click(function(){
		var common_rating_confirmed = $(this).attr('value');
		$.ajax({
			url: BASE_URL + '/card/card/agreement/',
			data: {
				'id'         :common_rating.id,
				'person_id'  :common_rating.person_id,
				'period_year':common_rating.period_year,
				'rating_id'  :common_rating.rating_id,
				'confirmed'  :common_rating_confirmed
			},
			success: function(data) {
					location.reload();
			}
		});

	});

	if(is_blocked == true)
	{
		$('.tabs-body').prepend('<div class="overlay">').css({overflow:'hidden'});

		$('.comments-body').css({'z-index':1});

		$('.overlay').css({
			width:'100%',
			height:'100%',
			background:'#ccc',
			'z-index':1000,
			position:'absolute',
			display: 'block',
			opacity:0.4
		})

	}
});