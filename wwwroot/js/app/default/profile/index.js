
var Tabs = new Js.TabPanel();
var trainingsLoaded = false;
var personalLoaded = false;
	
function init()
{
	Tabs.addTab('tabsItemTasks', 'tabsBodyTasks');
	Tabs.addTab('tabsItemTrainings', 'tabsBodyTrainings');
	Tabs.addTab('tabsItemPersonal', 'tabsBodyPersonal');
	
	document.getElementById('tabsItemTrainings').onclick = onclickTrainings;
	document.getElementById('tabsItemPersonal').onclick = onclickPersonal;
}

function openCard()
{
	var url = BASE_URL + '/card/index/index/personid/' + PERSON_ID;
	Js.open(url, '', 1000, 746, null, null, ['status', 'resizable']);
}


function openNotes(taskId, is_personal)
{
	var url = BASE_URL + '/card/achievs-task-notes/index/taskid/' + taskId + '/is_personal/' + is_personal;
	Js.open(url, '', 400, 510);
}

/*function openNotesCompetence(competId)
{
	var url = BASE_URL + '/card/achievs-competence-notes/index/competid/' + competId;
	
	Js.open(url, '', 400, 510);
}*/

function onclickTrainings()
{
	if (!trainingsLoaded) {
		var url = BASE_URL + '/default/profile/trainings/personid/' + PERSON_ID;
		window.trainings.location = url;
		trainingsLoaded = true;
	}
	Tabs.getTab('tabsItemTrainings').activate();
}

function onclickPersonal()
{
	if (!personalLoaded) {
		var url = BASE_URL + '/default/profile/personal/personid/' + PERSON_ID;
		window.personal.location = url;
		personalLoaded = true;
	}
	Tabs.getTab('tabsItemPersonal').activate();
}