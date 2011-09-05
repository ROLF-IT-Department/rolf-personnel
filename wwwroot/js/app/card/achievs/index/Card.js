var Card = new function()
{
	var _tasks         = null;			// таблица бизнес-целей
	var _func_tasks    = null;			// таблица функциональных бизнес целей
	var _standsCompets = null;
	var _additsCompets = null;
	var _trains        = null;
	var _ratings       = null;
	var _comments      = null;
	var _func_comment  = null;
	var _func_div	   = null;

	var _fieldRatingTasks   = null;
	var _fieldRatingFunc    = null;
	var _fieldRatingCompets = null;
	var _fieldRatingTotal   = null;

	var _buttonAddTask = null;
	var _buttonAddTraining = null;

	var ratio_mng = null;
	var ratio_fnc = null;
	var lbl_mng   = null;
	var lbl_fnc   = null;
	var _period   = null;

	var saving = 0;

	this.init = function(period)
	{
		_tasks                 = document.getElementById('tasks');
		_personaltasks         = document.getElementById('personaltasks');
		_managertasks          = document.getElementById('managertasks');
		_personalCompetence    = document.getElementById('personalCompetence');
		_personalTraining      = document.getElementById('personalTraining');
		_func_tasks            = document.getElementById('functasks');
		_standsCompets         = document.getElementById('standsCompets');
		_personalStandsCompets = document.getElementById('personalStandsCompets');
		_additsCompets         = document.getElementById('additsCompets');
		_personalAdditsCompets = document.getElementById('personalAdditsCompets');
		_trains                = document.getElementById('trains');
		_ratings               = document.getElementById('ratings');
		_comments              = document.getElementById('comments');
		_emp_comment           = document.getElementById('comment_employee');
		_sum_rtg_tasks         = document.getElementById('sum_tasks');
		_rtg_tasks             = document.getElementById('ratings[rtg_tasks_id]');
		_rtg_competens         = document.getElementById('ratings[rtg_competens_id]');
		_func_comment          = document.getElementById('comments[fnc_comment]');
		_func_div              = document.getElementById('func_div');

		ratio_mng = document.getElementById('ratio[ratio_mng]');
		ratio_fnc = document.getElementById('ratio[ratio_fnc]');
		if (ratio_mng && ratio_fnc)
			if ((!ratio_mng.value) && (!ratio_fnc.value)){
				ratio_mng.value = 50;
				ratio_fnc.value = 50;
			}

		lbl_mng = document.getElementById('ratio_mng');
		lbl_fnc = document.getElementById('ratio_fnc');

		_fieldRatingTasks   = document.getElementById('fieldRatingTasks');
		_fieldRatingFunc    = document.getElementById('fieldRatingFunc');
		_fieldRatingCompets = document.getElementById('fieldRatingCompets');
		_fieldRatingTotal   = document.getElementById('fieldRatingTotal');

		_getControl(_fieldRatingTasks).onchange   = _ratingTasksOnchange;
		_getControl(_fieldRatingCompets).onchange = _ratingCompetsOnchange;

		_buttonAddTask         = document.getElementById('buttonAddTask');
		_buttonAddPersonalTask = document.getElementById('buttonAddPersonalTask');
		_buttonAddTraining     = document.getElementById('buttonAddTraining');

		_period = period;

		$('textarea').change(function(){
			var row_class = this.parentNode.parentNode.className;
			var new_class = ' row-not-saved';

			var expr_class = /\srow-not-saved/;
			if( ! expr_class.test(row_class))
			{
				this.parentNode.parentNode.className += new_class;
			}
			changes = true;
		});
	}

	this.displayRatio = function()
	{
		ratio_mng.style.display = 'none';
		ratio_fnc.style.display = 'none';

		if(ratio_mng.value) {
			lbl_mng.innerHTML = ratio_mng.value;
		}
		else {
			lbl_mng.innerHTML = '50';
		}
		if(ratio_fnc.value) {
			lbl_fnc.innerHTML = ratio_fnc.value;
		}
		else {
			lbl_fnc.innerHTML = '50';
		}
	}

	this.CalculateRating = function()
	{
		var rows  = _tasks.rows;
		var len   = _tasks.rows.length-1;
		var i, cells, index, weight, rate, status;
		var sum = 0;
		var fsum = 0;
		for(i=0; i<len; i++)
		{
			cells = rows[i].cells;
			index = _getControl(cells[6]).selectedIndex;
			rate = this.returnWeight(_getControl(cells[6]).options[index].text);
			weight = _getControl(cells[3]).value;
			status = _getControl(cells[0]).value;

			if((!rate) || (!weight) || (rate == 0) || (status == 0))
				continue;

			sum += weight * rate;
		}
		if (count_func > 0)
		{
			var func_rows = _func_tasks.rows;
			var flen = _func_tasks.rows.length - 1;
			for(i=0; i<flen; i++)
			{
				cells = func_rows[i].cells;
				index = _getControl(cells[6]).selectedIndex;
				rate = this.returnWeight(_getControl(cells[6]).options[index].text);
				weight = _getControl(cells[3]).value;
				status = _getControl(cells[0]).value;
				if ((!rate) || (!weight) || (rate == 0) || (status == 0))
					continue;

				fsum += weight * rate;
			}
		}
		var ret = 0;
		sum /= 100;
		fsum /= 100;

		if ((count_func > 0) && (fsum != 0))
		{
			var rate_sum = Math.round(sum);
			var rate_fsum = Math.round(fsum);
			var mng = parseInt(ratio_mng.value, 10);
			var fnc = parseInt(ratio_fnc.value, 10);
			var total = (rate_sum * mng + rate_fsum * fnc) / 100;
			ret = Math.round(total);
		}
		else {
			ret = Math.round(sum);
		}
		return this.returnName(ret);
	}

	this.CalculateCompetences = function()
	{
		var srows = _standsCompets.rows;
		var arows = _additsCompets.rows;
		var slen  = _standsCompets.rows.length;
		var alen  = _additsCompets.rows.length;
		var i, cells, index, rate;
		var sum = 0;
		var count = 0;
		for(i=0; i<slen ;i++)
		{
			cells = srows[i].cells;
			index = _getControl(cells[5]).selectedIndex;
			rate = parseInt(this.returnWeight(_getControl(cells[5]).options[index].text), 10);
			sum += rate;
			count++;
		}
		for(i = 0; i < alen; i++) {
			cells = arows[i].cells;
			index = _getControl(cells[5]).selectedIndex;
			rate = parseInt(this.returnWeight(_getControl(cells[5]).options[index].text), 10);
			sum += rate;
			count++;
		}
		var ret = 0;

		if(count)
			ret = Math.round(sum / count);

		return this.returnName(ret);
	}

	this.returnWeight = function(name)
	{
		var i;
		var len = Rate_Names.length;
		for(i = 0; i < len; i++) {
			if(Rate_Names[i] == name) {
				break;
			}
		}
		return Rate_Weights[i];
	}

	this.returnName = function(weight)
	{
		var i;
		var len = Rate_Weights.length;
		for(i = 0; i < len; i++) {
			if(Rate_Weights[i] == weight) {
				break;
			}
		}
		return Rate_Names[i];
	}

	this.toggleCancel = function(node, tableID)
	{
		var row = node.parentNode.parentNode;
		var toggle = _getControl(node.parentNode);

		if (toggle.value == '0')
		{
			var table = document.getElementById(tableID);

			var count = Card.countActiveObjectivesByStatus(table);

			if (count > 6)
			{
				alert("Внимание! Ограничение по количеству целей не более 6 целей! Вам нужно удалить или отменить другую цель!");
				return;
			}

			toggle.value = 1;
			row.className = row.className.replace(/\s*\brow-canceled\b/ig, '');
		}
		else
		{
			toggle.value = 0;
			row.className += ' row-canceled';
		}
	}

	this.removeRow = function(node)
	{
		var row = node.parentNode.parentNode;

		row.parentNode.removeChild(row);
	}

	this.addTask = function()
	{
		var row   = _cloneLastRow(_tasks);
		var name  = 'newTasks[' + row.rowIndex + ']';
		var cells = row.cells;

		_getControl(cells[0]).name = name + '[status]';
		_getControl(cells[1]).name = name + '[description]';
		_getControl(cells[3]).name = name + '[weight]';
		_getControl(cells[4]).name = name + '[is_functional]';
		_getControl(cells[5]).name = name + '[result]';
		_getControl(cells[6]).name = name + '[rating_id]';

		var input = _getControl(cells[2]).nextSibling;
		if(!input.name) {
			input = input.nextSibling;
		}
		input.name = name + '[date_term]';
//		_getControl(cells[2]).onclick = Card.calendar;
		_getControl(cells[6]).parentNode.className =
		_getControl(cells[6]).parentNode.className.replace(/\s*\bfield-activated\b/ig, '');
		row.className = row.className.replace(/\s*\brow-pattern\b/ig, 'row-not-saved');
	}

	this.addPersonalTask = function()
	{
		var row = _cloneLastRow(_personaltasks);
		var name = 'newTasks[' + row.rowIndex + ']';
		var cells = row.cells;
		_getControl(cells[0]).name = name + '[status]';
		_getControl(cells[1]).name = name + '[description]';
		_getControl(cells[3]).name = name + '[weight]';
		_getControl(cells[4]).name = name + '[is_personal]';
		_getControl(cells[5]).name = name + '[result]';
		_getControl(cells[6]).name = name + '[rating_id]';

		var input = _getControl(cells[2]).nextSibling;
		if(!input.name) {
			input = input.nextSibling;
		}
		input.name = name + '[date_term]';
//		_getControl(cells[2]).onclick = Card.calendar;
		_getControl(cells[6]).parentNode.className =
		_getControl(cells[6]).parentNode.className.replace(/\s*\bfield-activated\b/ig, '');
		row.className = row.className.replace(/\s*\brow-pattern\b/ig, 'row-not-saved');
		$(_getControl(cells[2])).datepicker(
		{
			altField: $(_getControl(cells[2])).next(),
			altFormat: 'yy-mm-dd'});
	}

	this.addFuncTask = function()
	{
		var row = _cloneLastRow(_func_tasks);
		var name = 'newTasks[' + row.rowIndex + ']';
		var cells = row.cells;

		_getControl(cells[0]).name = name + '[status]';
		_getControl(cells[1]).name = name + '[description]';
		_getControl(cells[3]).name = name + '[weight]';
		_getControl(cells[4]).name = name + '[is_functional]';
		_getControl(cells[5]).name = name + '[result]';
		_getControl(cells[6]).name = name + '[rating_id]';

		var input = _getControl(cells[2]).nextSibling;
		if(!input.name) {
			input = input.nextSibling;
		}
		input.name = name + '[date_term]';
//		_getControl(cells[2]).onclick = Card.calendar;
		_getControl(cells[6]).parentNode.className =
		_getControl(cells[6]).parentNode.className.replace(/\s*\bfield-activated\b/ig, '');
		row.className = row.className.replace(/\s*\brow-pattern\b/ig, 'row-not-saved');
		$(_getControl(cells[2])).datepicker(
		{
			altField: $(_getControl(cells[2])).next(),
			altFormat: 'yy-mm-dd'});
	}

	this.addTrain = function()
	{
		var row = _cloneLastRow(_trains);
		var name = 'newTrainings[' + row.rowIndex + ']';
		var cells = row.cells;

		_getControl(cells[1]).name = name + '[situation]';
		_getControl(cells[2]).name = name + '[objective]';
		_getControl(cells[3]).name = name + '[method_id]';
		_getControl(cells[4]).name = name + '[responsible_id]';
		_getControl(cells[5]).name = name + '[month_term_id]';
		_getControl(cells[6]).name = name + '[result]';

		cells[3].getElementsByTagName('textarea')[0].name = name + '[method_comment]';
		cells[4].getElementsByTagName('textarea')[0].name = name + '[responsible_comment]';
		row.className = row.className.replace(/\s*\brow-pattern\b/ig, 'row-not-saved');
	}

	this.setPlanTask = function(row)
	{
		var cells = row.cells;
		var status = _getControl(cells[0]).value;

		row.className += ' row-planning';
		switch(status)
		{
			case '':
				//cells[0].className += ' field-activated';
				cells[1].className += ' field-activated';
				cells[2].className += ' field-activated';
				cells[3].className += ' field-activated';
				cells[5].className += ' field-activated';
				//cells[6].className += ' field-activated';
				_getControl(cells[1]).readOnly = false;
//				_getControl(cells[2]).onclick = this.calendar;
				if( ! $(row).hasClass('row-pattern'))
					$(_getControl(cells[2])).datepicker({altField: $(_getControl(cells[2])).next(), altFormat: 'yy-mm-dd'});
				_getControl(cells[3]).readOnly = false;
				_getControl(cells[5]).readOnly = false;
				break;
			case '1':
//				cells[1].className += ' field-activated';
//				cells[2].className += ' field-activated';
				cells[3].className += ' field-activated';
				_getControl(cells[3]).readOnly = false;
				cells[5].className += ' field-activated';
				_getControl(cells[5]).readOnly = false;
				break;
			case '2':
				cells[3].className += ' field-activated';
				cells[5].className += ' field-activated';
				_getControl(cells[3]).readOnly = false;
				_getControl(cells[5]).readOnly = false;
				break;
			default:
				return;
		}
		/*if (status != '0')
		 {
		 cells[1].className += ' field-activated';
		 cells[2].className += ' field-activated';
		 cells[3].className += ' field-activated';
		 _getControl(cells[1]).readOnly = false;
		 _getControl(cells[2]).onclick  = this.calendar;
		 _getControl(cells[3]).readOnly = false;
		 cells[5].className += ' field-activated';
		 _getControl(cells[5]).readOnly = false;
		 }*/
		return;
	}

	this.setPlanTaskPersonal = function(row)
	{
		var cells = row.cells;
		var status = _getControl(cells[0]).value;

		row.className += ' row-planning';
		switch(status)
		{
			case '':
				//cells[0].className += ' field-activated';
				cells[1].className += ' field-activated';
				cells[2].className += ' field-activated';
				cells[3].className += ' field-activated';
				//cells[5].className += ' field-activated';
				//cells[6].className += ' field-activated';
				_getControl(cells[1]).readOnly = false;
//				_getControl(cells[2]).onclick = this.calendar;
				if( ! $(row).hasClass('row-pattern'))
					$(_getControl(cells[2])).datepicker({altField: $(_getControl(cells[2])).next(), altFormat: 'yy-mm-dd'});
				_getControl(cells[3]).readOnly = false;
				//_getControl(cells[5]).readOnly = false;
				break;
			case '1':
//				cells[1].className += ' field-activated';
//				cells[2].className += ' field-activated';
				cells[3].className += ' field-activated';
				_getControl(cells[3]).readOnly = false;
				cells[5].className += ' field-activated';
				_getControl(cells[5]).readOnly = false;
				break;
			case '2':
				cells[3].className += ' field-activated';
				cells[5].className += ' field-activated';
				_getControl(cells[3]).readOnly = false;
				_getControl(cells[5]).readOnly = false;
				break;
			default:
				return;
		}
		/*if (status != '0')
		 {
		 cells[1].className += ' field-activated';
		 cells[2].className += ' field-activated';
		 cells[3].className += ' field-activated';
		 _getControl(cells[1]).readOnly = false;
		 _getControl(cells[2]).onclick  = this.calendar;
		 _getControl(cells[3]).readOnly = false;
		 cells[5].className += ' field-activated';
		 _getControl(cells[5]).readOnly = false;
		 }*/
		return;
	}

	this.setRateTask = function(row)
	{
		var cells = row.cells;
		var status = _getControl(cells[0]).value;

		switch(status)
		{
			case '1':
				cells[5].className += ' field-activated';
				cells[6].className += ' field-activated';
				_getControl(cells[5]).readOnly = false;
				_getControl(cells[6]).readOnly = false;
				break;
			case '2':
				cells[5].className += ' field-activated';
				cells[6].className += ' field-activated';
				_getControl(cells[5]).readOnly = false;
				_getControl(cells[6]).readOnly = false;
				break;
			default:
				return;
		}
	}

	this.setRateTask2 = function(row)
	{
		var cells = row.cells;
//		var status = _getControl(cells[0]).value;
//
//		if (status != '0')
//		{
		cells[3].className += ' field-activated';
		cells[4].className += ' field-activated';
		_getControl(cells[3]).readOnly = false;
		_getControl(cells[4]).readOnly = false;
//		}
//		switch (status) {
//			case '1':
//				cells[5].className += ' field-activated';
//				cells[6].className += ' field-activated';
//				_getControl(cells[5]).readOnly = false;
//				_getControl(cells[6]).readOnly = false;
//				break;
//			case '2':
//				cells[5].className += ' field-activated';
//				cells[6].className += ' field-activated';
//				_getControl(cells[5]).readOnly = false;
//				_getControl(cells[6]).readOnly = false;
//				break;
//			default:
//				return;
//		}
	}

	this.setPlanTask2 = function(row)
	{
		var cells = row.cells;
		cells[5].className += ' field-activated';
		_getControl(cells[5]).readOnly = false;
	}

	this.setPlanTask3 = function(row)
	{
		var cells = row.cells;
		cells[3].className += ' field-activated';
		_getControl(cells[3]).readOnly = false;
	}

	// увеличиваем индекс cells на 1 (=4), так как добавляется столбец заметок к компетенциям
	this.setPlanCompet = function(row)
	{
		var cells = row.cells;
		cells[4].className += ' field-activated';
		_getControl(cells[4]).readOnly = false;
	}

	// увеличиваем индекс cells на 1 (=4), так как добавляется столбец заметок к компетенциям
	this.setRateCompet = function(row)
	{
		var cells = row.cells;
		cells[4].className += ' field-activated';
		cells[5].className += ' field-activated';
		_getControl(cells[4]).readOnly = false;
	}

	this.setPlanTrain = function(row)
	{
		var cells = row.cells;

		cells[1].className += ' field-activated';
		cells[2].className += ' field-activated';
		cells[3].className += ' field-activated';
		cells[4].className += ' field-activated';
		cells[5].className += ' field-activated';
		cells[6].className += ' field-activated';

		_getControl(cells[1]).readOnly = false;
		_getControl(cells[2]).readOnly = false;
		_getControl(cells[6]).readOnly = false;

		cells[3].getElementsByTagName('textarea')[0].readOnly = false;
		cells[4].getElementsByTagName('textarea')[0].readOnly = false;

		this.setEditCompetenceNotes();
		this.activateButtons();

	}

	this.setRateTrain = function(row)
	{
		var cells = row.cells;

		cells[6].className += ' field-activated';
		_getControl(cells[6]).readOnly = false;
	}

	this.setEditRatings = function()
	{
		_fieldRatingTasks.className   += ' field-activated';
		_fieldRatingCompets.className += ' field-activated';
		_fieldRatingTotal.className   += ' field-activated';
	}

	this.setEditMngComments = function()
	{
		var rows = _comments.rows;

		rows[1].cells[0].className += ' field-activated';
		rows[3].cells[0].className += ' field-activated';
		rows[4].cells[0].className += ' field-activated';
		rows[6].cells[0].className += ' field-activated';

		_getControl(rows[1].cells[0]).readOnly = false;
		_getControl(rows[4].cells[0]).readOnly = false;
		_getControl(rows[6].cells[0]).readOnly = false;

		var flags = rows[3].cells[0].getElementsByTagName('input');
		for (var i = 0; i < flags.length; i++)
		{
			flags[i].disabled = false;
		}
	}

	this.setEditEmpComments = function()
	{
		var cell = _emp_comment.rows[1].cells[0];

		cell.className += ' field-activated';
		_getControl(cell).readOnly = false;
	}

	// вставляет функцию-пиктограмму заметки к бизнес-цели в карточку
	this.setEditNotes = function()
	{
		var rows = _tasks.rows;
		var div = null;
		for (var i = 0; i < rows.length; i++)
		{
			div = rows[i].cells[4].getElementsByTagName('div')[0];
			if (div)
			{
				div.style.display = 'block';
			}
		}
	}

	this.setEditPersonalNotes = function()
	{
		var rows = _personaltasks.rows;
		var div = null;
		for (var i = 0; i < rows.length; i++)
		{
			div = rows[i].cells[4].getElementsByTagName('div')[0];
			if (div)
			{
				div.style.display = 'block';
			}
		}

		if (_managertasks != null)
		{
			var mrows = _managertasks.rows;
			var mdiv = null;
			for (var i = 0; i < mrows.length; i++)
			{
				mdiv = mrows[i].cells[4].getElementsByTagName('div')[0];
				if (mdiv)
				{
					mdiv.style.display = 'block';
				}
			}
		}

		var compNote = document.getElementById('competencePersonalNote');
		if (compNote)
			compNote.style.display = 'block';

		var trainNote = document.getElementById('trainingPersonalNote');
		if (trainNote)
			trainNote.style.display = 'block';

	}

	this.setEditFuncNotes = function()
	{
		if(_func_tasks == null) return;

		var rows = _func_tasks.rows;
		var div = null;
		for (var i = 0; i < rows.length; i++)
		{
			div = rows[i].cells[4].getElementsByTagName('div')[0];
			if (div)
			{
				div.style.display = 'block';
			}
		}
	}

	this.setEditCompetenceNotes = function()
	{
		var rowsAdd = _additsCompets.rows;
		var rowsSt = _standsCompets.rows;
		if(rowsSt.length)
		{
			for(var i = 0; i < rowsSt.length; i++)
			{
				div = rowsSt[i].cells[3].getElementsByTagName('div')[0];
				div.style.display = 'block';
			}
		}
		if(rowsAdd.length)
		{
			for(var i = 0; i < rowsAdd.length; i++)
			{
				div = rowsAdd[i].cells[3].getElementsByTagName('div')[0];
				div.style.display = 'block';
			}
		}
	}

	this.setEditPersonalCompetenceNotes = function()
	{
		var rowsAdd = _personalAdditsCompets.rows;
		var rowsSt = _personalStandsCompets.rows;

		if(rowsSt.length)
		{
			for(var i = 0; i < rowsSt.length; i++) {
				div = rowsSt[i].cells[2].getElementsByTagName('div')[0];
				div.style.display = 'block';
			}
		}

		if(rowsAdd.length)
		{
			for(var i = 0; i < rowsAdd.length; i++) {
				div = rowsAdd[i].cells[2].getElementsByTagName('div')[0];
				div.style.display = 'block';
			}
		}
	}

	this.activateButtons = function()
	{
		_buttonAddTask.onclick        = Card.saveTasks; //Card.addTask;
		_buttonAddTraining.onclick    = Card.addTrain;
		_buttonAddTask.className     += ' button-activated';
		_buttonAddTraining.className += ' button-activated';
	}

	this.setModePlan = function()
	{
		var rows = _tasks.rows;
		for(var i = 0; i < rows.length; i++)
		{
			this.setPlanTask(rows[i]);
		}

		rows = _standsCompets.rows;
		for(var i = 0; i < rows.length; i++)
		{
			this.setPlanCompet(rows[i]);
		}

		rows = _additsCompets.rows;
		for(var i = 0; i < rows.length; i++)
		{
			this.setPlanCompet(rows[i]);
		}

		rows = _trains.rows;
		for(var i = 0; i < rows.length; i++)
		{
			this.setPlanTrain(rows[i]);
		}

		this.setEditMngComments();
		this.activateButtons();

		if(count_func > 0)
			this.setEditRatio();
	}

	// этап планирования для персональной части сотрудника
	this.setModePersonalPlan = function()
	{
		var rows = _personaltasks.rows;

		for (var i = 0; i < rows.length; i++) {
			this.setPlanTaskPersonal(rows[i]);
		}

		if(_managertasks != null)
		{
			var rows = _managertasks.rows;

			for (var i = 0; i < rows.length; i++) {
				this.setPlanTask2(rows[i]);
			}
		}

		var rows = _personalCompetence.rows;
		rows[1].cells[1].className += ' field-activated';
		_getControl(rows[1].cells[1]).readOnly = false;

		var rows = _personalTraining.rows;
		rows[1].cells[1].className += ' field-activated';
		_getControl(rows[1].cells[1]).readOnly = false;

		var rows = _personalStandsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setPlanTask3(rows[i]);
		}

		var rows = _personalAdditsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setPlanTask3(rows[i]);
		}

		this.setEditPersonalCompetenceNotes();

		_buttonAddPersonalTask.onclick = Card.createPersonalTasks;
		_buttonAddPersonalTask.className += ' button-activated';


	}

	this.setEditRatio = function()
	{


		ratio_mng.style.display = 'block';
		ratio_fnc.style.display = 'block';

		lbl_mng.innerHTML = '';
		lbl_fnc.innerHTML = '';

	}

	this.setModePlanFuncMng = function()
	{
		if (_func_tasks == null) return;

		var rows = _func_tasks.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setPlanTask(rows[i]);
		}

		// комментарии
		_func_comment.style.display = 'block';
		_func_div.style.display = 'none';


		// кнопка добавить
		_buttonAddTask.onclick = Card.saveFunctionalTasks; //Card.addFuncTask;
		_buttonAddTask.className += ' button-activated';

	}

	this.setModeRate = function()
	{
		var rows = _tasks.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateTask(rows[i]);
		}

		rows = _standsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateCompet(rows[i]);
		}

		rows = _additsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateCompet(rows[i]);
		}

		rows = _trains.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateTrain(rows[i]);
		}

		this.setEditRatings();
		this.setEditMngComments();
	}


	this.setModeRateFuncMng = function()
	{
		var rows = _func_tasks.rows;

		for (var i = 0; i < rows.length; i++) {
			this.setRateTask(rows[i]);
		}

		_fieldRatingFunc.className   += ' field-activated';

		_func_comment.style.display = 'block';
		_func_div.style.display = 'none';

	}

	this.setModeRatePersonal = function()
	{
		var rows = _personaltasks.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateTask(rows[i]);
		}

		var mrows = _managertasks.rows;
		for (var i = 0; i < mrows.length; i++) {
			this.setRateTask(mrows[i]);
		}

		var rows = _personalStandsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateTask2(rows[i]);
		}

		var rows = _personalAdditsCompets.rows;
		for (var i = 0; i < rows.length; i++) {
			this.setRateTask2(rows[i]);
		}

		var rows = _personalCompetence.rows;
		rows[1].cells[1].className += ' field-activated';
		_getControl(rows[1].cells[1]).readOnly = false;

		// выводим Select Box для компетенции
		var cmp_rtg_form = document.getElementById('cmp_rtg_form');
		cmp_rtg_form.style.display = 'block';

		// прячем Div с оценкой
		var cmp_rtg = document.getElementById('cmp_rtg');
		cmp_rtg.style.display = 'none';

		var rows = _personalTraining.rows;
		rows[1].cells[1].className += ' field-activated';
		_getControl(rows[1].cells[1]).readOnly = false;

		this.setEditPersonalCompetenceNotes();


	}

	// Калькуляция суммы весов
	this.checkSumWeights = function(table)
	{
		var rows = table.rows;
		var cells = null;
		var status = null;
		var sum = 0;
		var value = 0;

		for(var i = 0; i < rows.length - 1; i++) {
			cells = rows[i].cells;
			status = _getControl(cells[0]).value;
			value = parseInt(_getControl(cells[3]).value, 10);
			if(status != '0') {
				sum += value;
			}
		}
		return sum;
	}

	// Калькуляция отличия суммы весов от 100%
	this.weights_sum_diff = function(tasks_weight_sum)
	{
		var msg = [
		];
		var diff = 0;
		if(tasks_weight_sum > 100) {
			diff = tasks_weight_sum - 100;
			msg[0] = 'больше 100% на ' + diff + '%';
			msg[1] = 'is more than 100% on ' + diff + '%';
		}
		if(tasks_weight_sum < 100) {
			diff = 100 - tasks_weight_sum;
			msg[0] = 'меньше 100% на ' + diff + '%';
			msg[1] = 'is less than 100% on ' + diff + '%';
		}
		return msg;
	}
	this.checkWeightValue = function(value)
	{
		if(isNaN(value)) {
			return 0;
		}
		if(value > 100) {
			return 1;
		}
		if(value < 0) {
			return -1;
		}
	}
	// Проверка на заполненность необходимых полей в новом плане развития
	this.checkTrainsDataFill = function(trains, card_action)
	{
		var rows = trains.rows;
		var cells = null;
		var msg = [
		];
		if(rows.length > 1) {
			for(var i = 0; i < rows.length - 1; i++) {
				cells = rows[i].cells;
				status = rows[i].className;

				// Проверка нового плана
				if(card_action == 'new') {
					if(status == 'row-not-saved') {
						for(var j = 0; j < (cells.length - 1); j++) {
							if(j != 0 && j != 6) {
								var content = _getControl(cells[j]).value;
								if(content == '') {
									msg[0] = '- Не заполнено одно из обязательных полей в новом плане развития!';
									msg[1] = '- Not filled one of the nessessary field in development plan!';
								}
							}
						}
					}
				}
				if(card_action == 'all') {
					if(status == 'row-not-saved') {
						for(var j = 0; j < (cells.length - 1); j++) {
							if(j != 0 && j != 6) {
								var content = _getControl(cells[j]).value;
								if(content == '') {
									msg[0] = '- Не заполнено одно из обязательных полей в новом плане развития!';
									msg[1] = '- Not filled one of the nessessary field in development plan!';
								}
							}
						}
					}
					if(status == '' && edit == false) {
						for(var j = 0; j < (cells.length - 1); j++) {
							if(j != 0 && j != 6) {
								var content = _getControl(cells[j]).value;
								if(content == '') {
									msg[0] = '- Не заполнено одно из обязательных полей в одном из планов развития!';
									msg[1] = '- Not filled one of the nessessary field in one of the development plan!';
								}
							}
						}
					}
				}

				// Этап оценки
				if(card_action == 'approve') {
					for(var k = 0; k < cells.length - 1; k++) {
						if(k != 0) {
							var content = _getControl(cells[k]).value;
							if(content == '') {
								msg[0] = '- Не заполнена информация о достижении цели у плана развития! Заполните, '
								+ 'пожалуйста, соответствующее поле.';
								msg[1] =
								'- Not filled the information about achieving the objective of a development plan! '
								+ 'Fill in required field please.';
							}
						}
					}
				}
			}
		}
		return msg;
	}

	this.checkRatio = function()
	{
		var mng = parseInt(ratio_mng.value, 10);
		var fnc = parseInt(ratio_fnc.value, 10);
		if((this.checkWeightValue(mng) == 0) || (this.checkWeightValue(fnc) == 0)) {
			alert('Соотношение веса должно быть целым числом!\nWeight ratio shall be a whole number!');
			return false;
		}
		if((this.checkWeightValue(mng) > 0) || (this.checkWeightValue(fnc) > 0)) {
			alert('Соотношение веса не может быть больше 100!\nWeight ratio cannot be more than 100%!');
			return false;
		}
		if((this.checkWeightValue(mng) < 0) || (this.checkWeightValue(fnc) < 0)) {
			alert('Соотношение веса не может быть меньше 0!\nWeight ratio cannot be less than 0%!');
			return false;
		}
		ratio_mng.value = Math.round(mng, 10);
		ratio_fnc.value = Math.round(fnc, 10);
		return true;
	}

	// Согласование планирования
	this.checkSetPlan = function(count_func)
	{
		var msg = [];
		var emsg = [];

		if( ! checkAllWeights)
			return false;

		// по умолчанию количество строк у пустой таблицы = 1 - паттерн
		if(_tasks.rows.length == 1)
		{
			msg.push('- Не прописаны бизнес-цели!');
			emsg.push('- Business objectives are not added!!');
		}

		// Сумма весов и отличие от 100% бизнес-целей
		var tasks_weight_sum = this.checkSumWeights(_tasks);
		var tasks_weight_sum_diff = this.weights_sum_diff(tasks_weight_sum);

		if(tasks_weight_sum != '100')
		{
			msg.push('- Сумма весов бизнес-целей должна составлять 100%! Сумма весов ' + tasks_weight_sum_diff[0]
			         + '. Пересчитайте, пожалуйста, веса.');
			emsg.push('- The sum of the weights of the business objectives should be 100%! At this moment the sum of '
			          + 'the weights ' + tasks_weight_sum_diff[1] + '.  Recount the weights please.');
		}

		/*
		 * Рекомендация по созданию плана развития ввиду того, что в предыдущем периоде по компетенциям или целям были
		 * оценки C или D, а в текущем периоде не задано плана развития
		 */
		if(previous_bad_rates == 'yes' && _trains.rows.length == 1)
		{
			msg.push('- Необходимо заполнить план развития сотрудника на текущий год с учетом того, '
			         + 'что в прошлом году у сотрудника есть оценки C или D в целях или компетенциях.');
			emsg.push('- Due to the employees business tasks or competences in the previous year have C or D rates, '
			          + 'you must fill the development plan for the employee for the current year.');
		}

		if(_tasks)
		{
			var count = Card.countActiveObjectivesByStatus(_tasks);

			// проверка на то, что активная цель не должна иметь вес = 0. Пока не активно
			var hasZero = Card.checkZeroInActiveObjectivesByStatus(_tasks);

			if(count > 7)
			{
				msg.push('- Ограничение по количеству целей не более 6 целей!');
				emsg.push('- There is a limit of 6 active objectives!');
			}
			if(Card.checkCorrectDateTerm(_tasks) == false)
			{
				msg.push('- Заполните <Срок> достижения всех бизнес-целей!');
				emsg.push('- Fill <Timing> for all objectives!');
			}
			// проверка на то, что активная цель не должна иметь вес = 0. Пока не активно
			if(hasZero == true)
			{
				msg.push('- Вес цели не может быть равен нулю! Пересчитайте, пожалуйста, веса.');
				emsg.push('- Business objective weight must be more than 0! Recount the weights please.');
			}
		}

		if(_trains)
		{
			// Проверка заполненности данных в на этапе оценки развития
			var trains_data_fill = this.checkTrainsDataFill(_trains, 'all');
			//alert(trains_data_fill)
			if(trains_data_fill.length) {
				msg.push(trains_data_fill[0]);
				emsg.push(trains_data_fill[1]);
			}
		}
		if(count_func > 0)
		{
			if(_func_tasks.rows.length == 1)
			{
				msg.push('- Не прописаны функциональные бизнес-цели!');
				emsg.push('- Functional business objectives are not added!');
			}

			var total_ratio = parseInt(ratio_mng.value, 10) + parseInt(ratio_fnc.value, 10);
			this.checkRatio();

			if(total_ratio != '100')
			{
				msg.push('- Сумма соотношения весов должна составлять 100! Пересчитайте, пожалуйста, веса.');
				emsg.push('- The sum of the weights of the business objectives should be 100%! Recount the weights '
				          + 'please.');
			}

			// Сумма весов и отличие от 100% функциональныхбизнес-целей
			var func_tasks_weight_sum = this.checkSumWeights(_func_tasks);
			var func_tasks_weight_sum_diff = this.weights_sum_diff(func_tasks_weight_sum);
			if(func_tasks_weight_sum != '100' && count_func > 0)
			{
				msg.push('- Сумма весов функциональных бизнес-целей должна составлять 100%! Сумма весов '
				         + func_tasks_weight_sum_diff[0] + '. Пересчитайте, пожалуйста, веса.');
				emsg.push('- The sum of the weights of the functional business objectives should be 100%! At this '
				          + 'moment the sum of the weights ' + func_tasks_weight_sum_diff[1]
				          + '.  Recount the weights please.');
			}

			if(_func_tasks)
			{
				var count = Card.countActiveObjectivesByStatus(_func_tasks);
				// проверка на то, что активная цель не должна иметь вес = 0. Пока не активно
				var hasZero = Card.checkZeroInActiveObjectivesByStatus(_func_tasks);
				if(count > 7) {
					msg.push('- Ограничение по количеству целей не более 6 целей!');
					emsg.push('- There is a limit of 6 active objectives!');
				}
				if(Card.checkCorrectDateTerm(_func_tasks) == false)
				{
					msg.push('- Заполните <Срок> достижения всех функциональных бизнес-целей!');
					emsg.push('- Fill <Timing> for all functional objectives!');
				}
				// проверка на то, что активная цель не должна иметь вес = 0. Пока не активно
				if(hasZero == true)
				{
					msg.push('- Вес функциональной цели не может быть равен нулю! Пересчитайте, пожалуйста, веса.');
					emsg
					.push('- Functional business objective weight must be more than 0! Recount the weights please.');
				}
			}
		}
		if(msg.length > 0)
		{
			msg = 'Невозможно согласовать план:' + "\n" + msg.join("\n");
			emsg = '\n\nImpossible to confirm a plan:' + "\n" + emsg.join("\n");
			alert(msg + emsg);
			return false;
		}
		return true;
	}

	// Проверка выставления рейтингов
	this.checkSetRatings = function(count_func)
	{
		var msg = [];
		var emsg = [];

		var select = null;
		var rows = _tasks.rows;
		var row_comment = _comments.rows;
		var cells = null;
		var status = null;

		for(var i = 0; i < rows.length; i++)
		{
			cells = rows[i].cells;
			status = _getControl(cells[0]).value;
			if(status == '1') {
				if(_getControl(cells[5]).value.length < 2)
				{
					msg.push('- Не заполнены комментарии к бизнес-целям!');
					emsg.push('- Comments to business objectives are not added!');
					break;
				}
				select = _getControl(cells[6]);

				if(select.options[select.selectedIndex].value == '')
				{
					msg.push('- Не выставлены рейтинги бизнес-целей!');
					emsg.push('- Business objectives ratings are not set out!');
					break;
				}
			}
		}

		if(count_func > 0)
		{
			var select = null;
			var rows = _func_tasks.rows;
			var cells = null;
			var status = null;

			for(var i = 0; i < rows.length; i++)
			{
				cells = rows[i].cells;
				status = _getControl(cells[0]).value;
				if(status == '1')
				{
					if(_getControl(cells[5]).value.length < 2)
					{
						msg.push('- Не заполнены комментарии к функциональным бизнес-целям!');
						emsg.push('- Comments to functional business objectives are not added!');
						break;
					}
					select = _getControl(cells[6]);

					if(select.options[select.selectedIndex].value == '')
					{
						msg.push('- Не выставлены рейтинги функциональных бизнес-целей!');
						emsg.push('- Functional business objectives ratings are not set out!');
						break;
					}
				}
			}
		}
		rows = _standsCompets.rows;
		for(var i = 0; i < rows.length; i++)
		{
			cells = rows[i].cells;
			if(_getControl(cells[4]).value.length < 2)
			{
				msg.push('- Не заполнены комментарии к корпоративным компетенциям!');
				emsg.push('- Comments to corporate competences are not added!');
				break;
			}
			select = _getControl(cells[5]);

			if(select.options[select.selectedIndex].value == '')
			{
				msg.push('- Не выставлены рейтинги корпоративных компетенций!');
				emsg.push('- Corporate competences ratings are not set out!');
				break;
			}
		}

		rows = _additsCompets.rows;
		for(var i = 0; i < rows.length; i++)
		{
			cells = rows[i].cells;
			if(_getControl(cells[4]).value.length < 2) {
				msg.push('- Не заполнены комментарии к компетенциям группы должностей!');
				emsg.push('- Comments to job families competences are not added!');
				break;
			}
			select = _getControl(cells[5]);

			if(select.options[select.selectedIndex].value == '')
			{
				msg.push('- Не выставлены рейтинги компетенций групп должностей!');
				emsg.push('- Job families competences ratings are not set out!');
				break;
			}
		}

		rows = _trains.rows;
		for(var i = 0; i < rows.length - 1; i++)
		{
			cells = rows[i].cells;
			if(_getControl(cells[6]).value.length < 2)
			{
				msg.push('- Не заполнены комментарии к целям плана развития!');
				emsg.push('- Comments to plan development objectives are not added!');
				break;
			}
		}

		select = _getControl(_fieldRatingTasks);
		if(select.options[select.selectedIndex].value == '')
		{
			msg.push('- Не выставлен итоговый рейтинг бизнес-целей!');
			emsg.push('- Total rating of business objective is not set out!');
		}

		select = _getControl(_fieldRatingCompets);
		if(select.options[select.selectedIndex].value == '') {
			msg.push('- Не выставлен итоговый рейтинг компетенций!');
			emsg.push('- Total rating of competences is not set out!');
		}
		select = _getControl(_fieldRatingTotal);

		if(select.options[select.selectedIndex].value == '')
		{
			msg.push('- Не выставлен общий рейтинг!');
			emsg.push('- Common rating is not set out!');
		}

		if(_trains)
		{
			// Проверка заполненности данных в на этапе оценки развития
			var trains_data_fill = this.checkTrainsDataFill(_trains, 'approve');
			if(trains_data_fill.length)
			{
				msg.push(trains_data_fill[0]);
				emsg.push(trains_data_fill[1]);
			}
		}

		if(!_getControl(row_comment[1].cells[0]).value)
		{
			msg.push('- Не заполнены комментарии руководителя!');
			emsg.push('- Manager comment is not set out!');
		}

		if(!_getControl(row_comment[4].cells[0]).value)
		{
			msg.push('- Не заполнены рекомендации по развитию карьеры!');
			emsg.push('- Career Development Recommendations are not set out!');
		}

		if(!_getControl(row_comment[6].cells[0]).value)
		{
			msg.push('- Не заполнены карьерные ожидания сотрудника!');
			emsg.push('- Employee’s Career Expectations are not set out!');
		}
		if(msg.length > 0)
		{
			msg = 'Невозможно согласовать оценку:' + "\n" + msg.join("\n");
			emsg = '\n\nImpossible to confirm the rating:' + "\n" + emsg.join("\n");
			alert(msg + emsg);
			return false;
		}
		return true;
	}

	// проверяем корректность введенных весов
	this.checkWeights = function(table)
	{
		var rows = table.rows;
		var len = rows.length - 1;
		var cells = null;
		var status = null;
		var weight = null;

		for(var i = 0; i < len; i++)
		{
			cells = rows[i].cells;
			status = _getControl(cells[0]).value;

			// если бизнес-цель отклонена, мы ее не рассматриваем
			if(status == '0')
			{
				continue;
			}

			weight = parseInt(_getControl(cells[3]).value, 10);
			if(this.checkWeightValue(weight) == 0)
			{
				alert('Вес цели должен быть целым числом!\nWeight of objective shall be a whole number!');
				return false;
			}

			if(this.checkWeightValue(weight) > 0)
			{
				alert('Вес цели не может быть больше 100%!\nWeight of objective cannot be more than 100%!');
				return false;
			}

			if(this.checkWeightValue(weight) < 0)
			{
				alert('Вес цели не может быть меньше 0%!\nWeight of objective cannot be less than 0%!');
				return false;
			}

			_getControl(cells[3]).value = Math.round(weight, 10);
		}

		return true;
	}

	this.checkWeightValue = function(value)
	{
		if (isNaN(value)) return 0;
		if (value > 100) return 1;
		if (value < 0) return -1;
	}

	this.checkRatio = function()
	{
		var mng = parseInt(ratio_mng.value, 10);
		var fnc = parseInt(ratio_fnc.value, 10);

		if((this.checkWeightValue(mng) == 0) || (this.checkWeightValue(fnc) == 0))
		{
			alert('Соотношение веса должно быть целым числом!\nWeight ratio shall be a whole number!');
			return false;
		}

		if((this.checkWeightValue(mng) > 0) || (this.checkWeightValue(fnc) > 0))
		{
			alert('Соотношение веса не может быть больше 100!\nWeight ratio cannot be more than 100%!');
			return false;
		}

		if((this.checkWeightValue(mng) < 0) || (this.checkWeightValue(mng) < 0))
		{
			alert('Соотношение веса не может быть меньше 0!\nWeight ratio cannot be less than 0%!');
			return false;
		}

		ratio_mng.value = Math.round(mng, 10);
		ratio_fnc.value = Math.round(fnc, 10);
		return true;
	}

	// Функция сохранения изменений карточки
	this.save = function()
	{
		if(saving)
		{
			alert('Внимание, идёт сохранение данных!\nAttention! Data is saving!');
			return false;
		}

//		_removeLastRow(_tasks);
//		_removeLastRow(_func_tasks);
//		_removeLastRow(_trains);

		if(period > 2008)
		{
			if(_tasks)
			{
				var count = Card.countActiveObjectivesByStatus(_tasks);
				if(count > 7)
				{
					alert("Внимание! Ограничение по количеству целей не более 6 целей!");
					return;
				}

				if(Card.checkCorrectDateTerm(_tasks) == false)
				{
					alert("Внимание! Заполните <Срок> достижения всех бизнес-целей!");
					return;
				}
			}

			if(_func_tasks)
			{
				var count = Card.countActiveObjectivesByStatus(_func_tasks);
				if(count > 6)
				{
					alert("Внимание! Ограничение по количеству целей не более 6 целей!");
					return;
				}

				if(Card.checkCorrectDateTerm(_func_tasks) == false)
				{
					alert("Внимание! Заполните <Срок> достижения всех функциональных бизнес-целей!");
					return;
				}
			}

			if(_trains)
			{
				// Проверка заполненности данных в новом плане развития
				var trains_data_fill = Card.checkTrainsDataFill(_trains, 'all');
				if(trains_data_fill.length)
				{
					alert(trains_data_fill[0]);
					return;
				}
			}
		}

		if(!(Card.checkWeights(_tasks)))
			return;

		if(_personaltasks && (! Card.checkWeights(_personaltasks)))
				return;

		if(count_func > 0)
		{
			if(!(Card.checkWeights(_func_tasks)))
				return;

			if(!(Card.checkRatio()))
				return;
		}

		changes = false;

		document.forms.card.submit();
		saving = 1;
	}

	this.countActiveObjectivesByStatus = function (table)
	{
		var rows = table.rows;
		var status;
		var count = rows.length;

		for(var i = 0; i < rows.length; i++)
		{
			status = rows[i].cells[0].getElementsByTagName('input')[0];
			if(status.value == '0')
			{
				count--;
			}
		}
		return count;
	}

	/**
	 * проверка на наличие нулевых весов у бизнес-целей
	 * @param table таблица бизнес-целей
	 * @return boolean
	 */
	this.checkZeroInActiveObjectivesByStatus = function (table)
	{
		var rows = table.rows;
		var row_class;
		var weight;
		for(var i = 0; i < rows.length - 1; i++) {
			row_class = rows[i].className;
			weight = rows[i].cells[3].getElementsByTagName('textarea')[0];
			if(row_class != 'row-pattern row-planning' && row_class != 'row-canceled row-planning') {
				if(weight.value == 0) {
					return true;
				}
			}
		}
		return false;
	}

	// проверяем заполнено ли поле Срок у бизнес-целей
	this.checkCorrectDateTerm = function (table)
	{
		var rows = table.rows;
		var term;
		var pattern;

		for(var i = 0; i < rows.length; i++)
		{
			term = rows[i].cells[2].getElementsByTagName('input')[0];
			pattern = rows[i].cells[2].getElementsByTagName('input')[1];
			if(pattern.name == 'taskPattern[date_term]')
				continue;

			if(term.value == '')
				return false;

			if(term.value == '[дата]')
				return false;
		}

		return true;
	}

	this.saveTasks = function()
	{
		if(period > 2008)
		{
			var count = Card.countActiveObjectivesByStatus(_tasks);
			if(count > 6)
			{
				alert("Внимание! Ограничение по количеству целей не более 6 целей! Вы не можете добавить новую "
				      + "цель!");
				return;
			}
			else
			{
				Card.addTask();
			}
		}
		else {
			Card.addTask();
		}
		//document.forms.card.submit();
	}

	// сохраняем персональные цели сотрудников, проверяем их количество - не больше 6 целей
	this.saveFunctionalTasks = function()
	{
		if(period > 2008)
		{
			var count = Card.countActiveObjectivesByStatus(_func_tasks);
			if(count > 6)
			{
				alert("Внимание! Ограничение по количеству целей не более 6 целей! Вы не можете добавить новую "
				      + "цель!");
				return;
			}
			else
			{
				Card.addFuncTask();
			}
		}
		else
		{
			Card.addFuncTask();
		}
		//document.forms.card.submit();
	}

	// сохраняем персональные цели сотрудников, проверяем их количество - не больше 6 целей
	this.createPersonalTasks = function()
	{
		if(_personaltasks && (! Card.checkWeights(_personaltasks)))
			return;

		if(period > 2008)
		{
			var count = Card.countActiveObjectivesByStatus(_personaltasks);
			if(count > 6)
			{
				alert("Внимание! Ограничение по количеству целей не более 6 целей! Вы не можете добавить новую цель!");
				return;
			}
			else
			{
				Card.addPersonalTask();
			}
		}
		else
		{
			Card.addPersonalTask();
		}
		//document.forms.card.submit();
	}

	this.checkBalanceTasks = function()
	{
		var index = _rtg_tasks.selectedIndex;
		var calculate = this.CalculateRating();

		if(calculate != _rtg_tasks.options[index].text)
		{
			var rows = _comments.rows;
			var msg;
			if(!_getControl(rows[1].cells[0]).value)
			{
				msg = "Согласование невозможно! Итоговый рейтинг бизнес-целей " + _rtg_tasks.options[index].text
				      + " и вычисленный рейтинг " + calculate
				      + " не совпадают! Вам необходимо аргументировать свой выбор в поле \"Комментарий "
				      + "руководителя\"!\n\nImpossible to confirm! Total rating of business objective " + _rtg_tasks
				.options[index].text + " and calculated rating " + calculate
				+ " do not coincide! You have to add your arguments in the field \"Manager’s comment\"!";
				alert(msg);
				return false;
			}
			msg =
			"Внимание! Итоговый рейтинг бизнес-целей " + _rtg_tasks.options[index].text + " и вычисленный рейтинг "
			+ calculate
			+ " не совпадают! Аргументируйте свой выбор в поле \"Комментарий руководителя\"!\n\nAttention! Total "
			+ "rating of business objective " + _rtg_tasks.options[index].text + " and calculated rating " + calculate
			+ " do not coincide! Explain your choice in the field \"Manager’s comment\"! ";

			alert(msg);
			return true;
		}
		return true;
	}

	this.checkBalanceCompetences = function()
	{
		var index = _rtg_competens.selectedIndex;
		var calculate = this.CalculateCompetences();

		if(calculate != _rtg_competens.options[index].text)
		{
			var rows = _comments.rows;
			var msg;
			if(!_getControl(rows[1].cells[0]).value)
			{
				msg = "Согласование невозможно! Итоговый рейтинг компетенций " + _rtg_competens.options[index].text
				      + " и вычисленный рейтинг " + calculate
				      + " не совпадают! Вам необходимо аргументировать свой выбор в поле \"Комментарий "
				      + "руководителя\"!\n\nImpossible to confirm! Total rating of competences " + _rtg_competens
				.options[index].text + " and calculated rating " + calculate
				+ " do not coincide! You have to add your arguments in the field \"Manager’s comment\"! ";
				alert(msg);
				return false;
			}

			msg =
			"Внимание! Итоговый рейтинг компетенций " + _rtg_competens.options[index].text + " и вычисленный рейтинг "
			+ calculate
			+ " не совпадают! Аргументируйте свой выбор в поле \"Комментарий руководителя\"!\n\nAttention! Total "
			+ "rating of competences " + _rtg_competens.options[index].text + " and calculated rating " + calculate
			+ " do not coincide! Explain your choice in the field \"Manager’s comment\"! ";

			alert(msg);
			return true;
		}
		return true;
	}

	function checkAllWeights() {
		if (!(this.checkWeights(_tasks)))
			return false;

		if (!(this.checkWeights(_func_tasks)))
			return false;

		return true;
	}

	function _cloneLastRow(table)
	{
		var pattern = table.rows[table.rows.length - 1];
		var numCells = pattern.cells.length;
		var row = table.insertRow(-1);
		var cell = null;

		row.className = pattern.className;
		for(var i = 0; i < numCells; i++)
		{
			cell = row.insertCell(-1);
			cell.className = pattern.cells[i].className;
			cell.innerHTML = pattern.cells[i].innerHTML;
		}

		return pattern;
	}

	function _removeLastRow(table)
	{
		var row = table.rows[table.rows.length - 1];
		row.parentNode.removeChild(row);
	}

	function _ratingTasksOnchange()
	{
		_ratings.rows[0].cells[1].innerHTML = this.options[this.selectedIndex].text;
	}

	function _ratingCompetsOnchange()
	{
		_ratings.rows[0].cells[3].innerHTML = this.options[this.selectedIndex].text;
	}

	function _getControl(node)
	{
		var obj = node.firstChild;
		while(obj && !obj.name)
		{
			obj = obj.nextSibling;
		}
		return obj;
	}

	this._getInputs = function(table)
	{
		var rows = table.rows;
		var inputs = [];

		for (var i = 0; i < rows.length; i++)
		{
			var cells = rows[i].cells;
			for (var j = 0; j < cells.length; j++)
			{
				var input = cells[j].firstChild;

				while (input && input.name)
				{
					input = input.nextSibling;
				}

				if(input)
					inputs.push(input);
			}
		}
		return inputs;
	}

	//------------------------------------------------------
	var _input = null;

//	this.calendar = function()
//	{
//		_input = this;
//		JsCalendar.open();
//	}
//
//	this.calendarHandler = function(time, year, month, day)
//	{
//		_input.value = day + '.' + (1 + month) + '.' + ('' + year).substr(2, 2);
//		var input = _input.nextSibling;
//
//		if(!input.name)
//		{
//			input = input.nextSibling;
//		}
//
//		input.value = year + '-' + (1 + month) + '-' + day;
//	}
}