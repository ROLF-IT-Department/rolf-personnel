var elems   = null;
var Tabs    = null;
var Toolbar = null;

var period = null;

/**
 * параметр редактирования (отката) карточки
 */
var edit   = false;

function init()
{
	JsCalendarInit(BASE_URL + '/js/lib/JsCalendar');
	JsCalendar.handler = Card.calendarHandler;

	elems = window.document.card.elements;

	elems.period.onchange = periodOnchangeHandler;

	period = elems.period.options[elems.period.selectedIndex].value;

	Card.init(period);

	Tabs = new Js.TabPanel();
	Tabs.addTab('tabs-item-tasks', 'tabs-body-tasks');
	Tabs.addTab('tabs-item-compets', 'tabs-body-compets');
	Tabs.addTab('tabs-item-trains', 'tabs-body-trains');
	Tabs.addTab('tabs-item-comments', 'tabs-body-comments');
	Tabs.addTab('tabs-item-personal', 'tabs-body-personal');
	if(has_statistics)
		Tabs.addTab('tabs-item-statistics', 'tabs-body-statistics');

	// Активация последней посещённой вкладки
	if(active_tab)
	{
		Tabs.focus('tabs-item-'+active_tab, 'tabs-body-'+active_tab);
	}

	Toolbar = new Js.Toolbar('toolbarBox');

	// выводим соотношение весов
	if (count_func > 0)	 Card.displayRatio();

	switch (elems.status_id.value) {
		case 'NEW':			// новая карточка
		case 'PLN':			// планирование (выставление бизнес-целей и тренингов)

			Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
			Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');

			if (USER_ROLE & ROLE_EMPLOYEE && ! is_blocked)
			{
				Card.setModePersonalPlan();
				Card.setEditEmpComments();
			}

			if (USER_ROLE & ROLE_MANAGER && ! is_blocked)
			{
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
				Card.setModePlan();
			}

			if (USER_ROLE & ROLE_FUNC_MANAGER && ! is_blocked)
			{
				Card.setModePlanFuncMng();
			}
			break;

		// согласование планирования
		case 'CPN':
			if (USER_ROLE & ROLE_MANAGER && ! is_blocked)
			{
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
			}

			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value != '1'
			    && ! is_blocked)
			{
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalPlan}).addClassName('toolbar-item-approval');
			}

			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[plan_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[plan_hmg_status]'].value == '' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[plan_fnc_status]'].value == ''
			    && ! is_blocked)
			{
				Toolbar.addItem({text: 'Отклонить<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectPlan}).addClassName('toolbar-item-reject');
			}
			break;

		// оценка (выставление рейтингов)
		case 'RTG':
			Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
			Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');

			if (USER_ROLE & ROLE_EMPLOYEE && ! is_blocked)
			{
				Card.setModeRatePersonal();
				Card.setEditEmpComments();
			}

			if (USER_ROLE & ROLE_MANAGER && ! is_blocked)
			{
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditPlan}).addClassName('toolbar-item-edit');
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
				Card.setModeRate();
			}

			if (USER_ROLE & ROLE_FUNC_MANAGER && ! is_blocked)
			{
				Card.setModeRateFuncMng();
			}
			break;

		// согласование оценки
		case 'CRG':
			if (USER_ROLE & ROLE_MANAGER && ! is_blocked)
			{
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}

			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value != '1' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value != '1' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value != '1'
			    && ! is_blocked)
			{
				Toolbar.addItem({text: 'Согласовать<br><span class="translate_toolbar">Approve</span>', onclick: toolbarItemApprovalRate}).addClassName('toolbar-item-approval');
			}

			if (USER_ROLE & ROLE_EMPLOYEE && elems['approvals[rate_emp_status]'].value == '' ||
				USER_ROLE & ROLE_HIGH_MANAGER && elems['approvals[rate_hmg_status]'].value == '' ||
				USER_ROLE & ROLE_FUNC_MANAGER && elems['approvals[rate_fnc_status]'].value == ''
			    && ! is_blocked)
			{
				Toolbar.addItem({text: 'Отклонить<br><span class="translate_toolbar">Reject</span>', onclick: toolbarItemRejectRate}).addClassName('toolbar-item-reject');
			}

			if (USER_ROLE & ROLE_EMPLOYEE && ! is_blocked)
			{
				Toolbar.addItem({text: 'Сохранить<br><span class="translate_toolbar">Save</span>', onclick: toolbarItemSave}).addClassName('toolbar-item-save');
				Toolbar.addItem({text: 'Закрыть<br><span class="translate_toolbar">Save & Close</span>', onclick: toolbarItemSaveClose}).addClassName('toolbar-item-save-close');

			}
			break;

		// закрытая карточка
		case 'CLS':
			if (USER_ROLE & ROLE_MANAGER && ! is_blocked)
			{
				Toolbar.addItem({text: 'Редактировать<br><span class="translate_toolbar">Edit</span>', onclick: toolbarItemEditRate}).addClassName('toolbar-item-edit');
			}
			break;

		default:
			break;
	}
	Toolbar.addItem({text: 'Обновить<br><span class="translate_toolbar">Refresh</span>', onclick: toolbarItemRefresh}).addClassName('toolbar-item-refresh');
	Toolbar.addItem({text: 'Печатная форма<br><span class="translate_toolbar">Print</span>', onclick: toolbarItemPrint}).addClassName('toolbar-item-print');

	// Отображение кнопок редактирования карт
	var posts = User.viewposts,
		IS_HR = false;
	for (var key in posts) {

		if(posts.length > 0)
			IS_HR = true;
	}
	if(IS_HR)
	{
		Toolbar.addItem({text: 'Создать карточку<br><span class="translate_toolbar">Create Card</span>', onclick: toolbarItemCreateCard}).addClassName('toolbar-item-card_create');

		var blocked_text;
		if(is_blocked)
		{
			 blocked_text_ru = 'Разблокировать';
			 blocked_text_en = 'Unblock';
		}
		else
		{
			blocked_text_ru = 'Блокировать';
			blocked_text_en = 'Block';
		}

		Toolbar.addItem({text: blocked_text_ru + ' карточку<br><span class="translate_toolbar">' + blocked_text_en +' Card</span>', onclick: toolbarItemCardBlock}).addClassName('toolbar-item-card_block');
	}

	// если для определенной карточки роль пользователь совпадает с ролью непосредственного руководителя то делаем заметки видимыми
	if (USER_ROLE & ROLE_EMPLOYEE)
		Card.setEditPersonalNotes();

	if (USER_ROLE & ROLE_MANAGER)
	{
		Card.setEditNotes();
		Card.setEditCompetenceNotes();
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER)
		Card.setEditFuncNotes();
}

/**
 * Создание и отправка сообщения по email
 */
function sendEmail()
{
	var employee_fio_div = document.getElementById('employee_fio');
	var employee_fio_value = employee_fio_div.value;

	var subject = "Согласование карточки в системе Управление Достижениями";

	var bodytext = 'Уважаемые коллеги!%0A%0AИнформирую вас о том, что карточка сотрудника ' + employee_fio_value + ' выставлена на согласование.%0AОзнакомьтесь, пожалуйста, с внесенными данными и согласуйте их.%0A%0AСпасибо!%0A%0A%0A%0ADear colleagues,%0A%0APlease, be informed that performance management card of ' + employee_fio_value + ' is waiting for your approval.%0AAcquaint with information and submit it.%0A%0AThank you.';

	parent.location.href="mailto:" + emails + "?subject=" + subject + "&body=" + bodytext;

}

/**
 * Сохранение карточки
 */
function toolbarItemSave()
{
	Card.save();

}

/**
 * Сохранение карточки и закрытие окна
 */
function toolbarItemSaveClose()
{
	Card.save();
	if (confirm("Вы уверены, что хотите выйти после сохранения карточки?\nAre you sure to quit after card save?"))
	{
		window.parent.close();
	}

}

/**
 * Редактирование карточки (откат) до этапа планирования
 */
function toolbarItemEditPlan()
{
	var msg = 'Внимание! При переходе в статус планирования все ранее полученные согласования будут сняты!\n\nAttention! When shifting to the planning status all previous plan confirmations will be removed!';
	if (window.confirm(msg))
	{
		elems['approvals[plan_mng_status]'].value = '';
		edit = true;
		Card.save();
	}
}

/**
 * Редактирование карточки (откат) до этапа оценки
 */
function toolbarItemEditRate()
{
	var msg = 'Внимание! При переходе в статус оценки все ранее полученные согласования оценки будут сняты!\n\nAttention! When shifting to rating status all previous rating confirmations will be removed!';
	if (window.confirm(msg)) {
		elems['approvals[rate_mng_status]'].value = '';
		edit = true;
		Card.save();
	}
}

function toolbarItemApprovalPlan()
{
	if (USER_ROLE & ROLE_MANAGER)
	{
		if (!Card.checkSetPlan(count_func)) return;
		elems['approvals[plan_mng_id]'].value = elems.userId.value;
		elems['approvals[plan_mng_status]'].value = 1;

		sendEmail();
	}

	if (USER_ROLE & ROLE_EMPLOYEE)
	{
		elems['approvals[plan_emp_status]'].value = 1;
	}

	if (USER_ROLE & ROLE_HIGH_MANAGER)
	{
		elems['approvals[plan_hmg_id]'].value = elems.userId.value;
		elems['approvals[plan_hmg_status]'].value = 1;
	}

	if (count_func == 0)
	{
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 1;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER)
	{
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 1;
	}

	Card.save();
}

function toolbarItemApprovalRate()
{

	if (USER_ROLE & ROLE_MANAGER)
	{
		if (!Card.checkSetRatings(count_func) || !Card.checkBalanceTasks() || !Card.checkBalanceCompetences())
			return;

		elems['approvals[rate_mng_id]'].value = elems.userId.value;
		elems['approvals[rate_mng_status]'].value = 1;

		sendEmail();
	}

	if (USER_ROLE & ROLE_EMPLOYEE)
		elems['approvals[rate_emp_status]'].value = 1;

	if (USER_ROLE & ROLE_HIGH_MANAGER)
	{
		elems['approvals[rate_hmg_id]'].value = elems.userId.value;
		elems['approvals[rate_hmg_status]'].value = 1;
	}

	if (count_func == 0)
	{
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 1;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER)
	{
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 1;
	}

	Card.save();
}

function toolbarItemRejectPlan()
{
	if (USER_ROLE & ROLE_EMPLOYEE)
		elems['approvals[plan_emp_status]'].value = 0;

	if (USER_ROLE & ROLE_HIGH_MANAGER)
	{
		elems['approvals[plan_hmg_id]'].value = elems.userId.value;
		elems['approvals[plan_hmg_status]'].value = 0;
	}

	if (count_func == 0)
	{
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 0;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER)
	{
		elems['approvals[plan_fnc_id]'].value = elems.userId.value;
		elems['approvals[plan_fnc_status]'].value = 0;
	}

	Card.save();
}

function toolbarItemRejectRate()
{
	if (USER_ROLE & ROLE_EMPLOYEE)
		elems['approvals[rate_emp_status]'].value = 0;

	if (USER_ROLE & ROLE_HIGH_MANAGER)
	{
		elems['approvals[rate_hmg_id]'].value = elems.userId.value;
		elems['approvals[rate_hmg_status]'].value = 0;
	}

	if (count_func == 0)
	{
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 0;
	}

	if (USER_ROLE & ROLE_FUNC_MANAGER)
	{
		elems['approvals[rate_fnc_id]'].value = elems.userId.value;
		elems['approvals[rate_fnc_status]'].value = 0;
	}

	Card.save();
}

function toolbarItemRefresh()
{
	location.reload();
}

/**
 * Вывод данных на печать
 */
function toolbarItemPrint()
{
	var personId = elems.person_id.value;
//	var period = elems.period.options[elems.period.selectedIndex].value;
	var period = $('#period option:selected').attr('period');

	var url = BASE_URL + '/card/achievs/print/personid/' + personId + '/period/' + period;

	//Js.open(url, '', 1000, 714, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
	var hei = screen.height-250;
	Js.open(url, '',1000, hei, null, null, ['status','resizable','menubar','toolbar','scrollbars']);
}

/**
 * Создание новой карточки
 */
function toolbarItemCreateNewCard()
{
	var url = BASE_URL + '/card/new-card/index';

	Js.open(url, 'Создание новой карточки', 400, 510);
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

/**
 * блокировка/разблокировка карточки
 *
 * @param card_id
 */
function toolbarItemBlockUnblockCard(card_id)
{
	var url = BASE_URL + '/card/index/block_unblock/'+card_id;
	location.replace(url);
}

	/**
 * Смена отображения периода просматриваемой карточки
 */
function periodOnchangeHandler()
{
	/*
	var msg = 'Внимание! При переходе в другой период все несохраненные изменения';
	msg += ' в карточке за текущий период будут потеряны!';
	if (!window.confirm(msg)) {
		return false;
	}
	*/


	var card_and_period = elems.period.options[elems.period.selectedIndex].value.split(',');

	var personId = elems.person_id.value;
	var period = card_and_period[1];
	var cardid = card_and_period[0];

//	var url = BASE_URL + '/card/achievs/index/personid/' + personId + '/period/' + period;
	var url = BASE_URL + '/card/achievs/index/personid/' + personId + '/cardid/' + cardid + '/period/' + period;
	$('#loading', parent.document.body).css({
			display: 'block'
	});
	location.replace(url);
}
/**
 * Открытие окна заметки
 * @param taskId Идентификатор задачи
 * @param is_personal параметр персональности заметки
 */
function openNotes(taskId, is_personal)
{
	var url = BASE_URL + '/card/achievs-task-notes/index/taskid/' + taskId + '/is_personal/' + is_personal;

	Js.open(url, '', 400, 510);
}
/**
 * Открытие окна заметок для компетенция
 * @param competId Идентификатор компетенции
 * @param is_personal параметр персональности заметки
 */
function openNotesCompetence(competId, is_personal)
{
	var url = BASE_URL + '/card/achievs-competence-notes/index/competid/' + competId + '/is_personal/' + is_personal;

	Js.open(url, '', 400, 510);
}
/**
 * Открытие окна заметок для плана развития
 * @param trainId Идентификатор плана развития
 */
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
			'background': "url('" + BASE_URL + "/img/achievs/status/locked.gif') center center #ccc no-repeat",
			'z-index':1000,
			position:'absolute',
			display: 'block',
			opacity:0.4
		})

	}
});