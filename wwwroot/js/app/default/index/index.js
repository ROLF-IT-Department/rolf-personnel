
var main = null;
var menu = null;

var Toolbar = null;

function init()
{
	document.body.onselectstart = function() {return false};
	
	main = new Main();
	main.init();
	
	Toolbar = new Js.Toolbar('toolbar');
	Toolbar.addItem({text: 'Персональная карточка<br/><span class="translate_menu">Personal Card</span>', onclick: toolbarItemCard}).addClassName('toolbar-item-card');
	Toolbar.addItem({text: 'Управление достижениями<br/><span class="translate_menu">Achievement management</span>', onclick: toolbarItemAchievs}).addClassName('toolbar-item-achievs');
	Toolbar.addItem({text: 'Обновить<br/><span class="translate_menu">Refresh</span>', onclick: toolbarItemRefresh}).addClassName('toolbar-item-refresh');
	Toolbar.addItem({text: 'Справка<br/><span class="translate_menu">Info</span>', onclick: toolbarItemHelp}).addClassName('toolbar-item-help');
	
	if (have_integrate == 1)
		Toolbar.addItem({text: 'Совместители<br/><span class="translate_menu">Integrate</span>', onclick: toolbarItemIntegrate}).addClassName('toolbar-item-integrate');
	
	menu = new Js.Tree('menu', true);
	menu.onselect = menuOnselect;
	menu.onexpand = menuOnexpand;
	// id - id ветки, pid - id родительской ветки, text - название ветки
	menu.addNode({id : 'card', text : User.fullname}).addClassNames('tree-node-card');
	menu.addNode({id : 'achievs', pid : 'card', text : 'Управление достижениями - Achievement management'}).addClassNames('tree-node-achievs');
	menu.addNode({id : 'personal', pid : 'card', text : 'Персональная информация - Personal data'}).addClassNames('tree-node-personal');
	menu.addNode({id : 'emps', text : 'Подчиненные сотрудники - Subordinate employees'}).addClassNames({branch : 'tree-branch-emps'});
	menu.addNode({id : 'people', pid : 'emps', text : 'Непосредственные подчиненные - Direct subordinates'});
	menu.addNode({id : 'subpeople', pid : 'emps', text : 'Подчиненные подчиненных - Subordinates of subordinates'});
	
	menu.addNode({id : 'func', text : 'Функциональные подчиненные - Functional subordinates'}).addClassNames({branch : 'tree-branch-func'});
	menu.addNode({id : 'funcpeople', pid : 'func', text : 'Функциональные подчиненные - Functional subordinates'});
	
	menu.addNode({id : 'subposts', text : 'Подчиненные должности - Subordinate appointments'}).addClassNames({branch : 'tree-branch-posts'});
	menu.addNode({id : 'viewposts', text : 'Просматриваемые должности - Appointments displayed'}).addClassNames({branch : 'tree-branch-posts'});
	menu.expandAllNodes();
	
	loadBranchPosts('subposts', User.subposts);		// загружаем список должностей подчиненных должностей в дерево
	var posts = User.viewposts;
	for (var key in posts) {
		posts[key].id = -posts[key].id;
	}
	loadBranchPosts('viewposts', posts);
	menu.getNode('card').select();
}

function menuOnselect(node)
{
	var id = node.id;
	var text = node.text;
	var className = '';
	var url = BASE_URL;
	
	if (id > 0) {
		text = 'Подчиненные должности : список сотрудников - Subordinate appointments : list of employees';
		className = 'caption-people';
		url += '/default/employees/index/empsShow/1/subEmpsShow/1/postIds/' + id;
	} else if (id < 0) {
		id = -id;
		text = 'Просматриваемые должности : список сотрудников - Appointments displayed : list of employees';
		className = 'caption-people';
		url += '/default/employees/index/empsShow/1/subEmpsShow/1/postIds/' + id;
	} else {
		switch (id) {
			case 'card':
				className = 'caption-card';				// ветка карточки сотрудника с полным именем
				url += '/card/index/index/personid/' + User.personId;
				break;
				
			case 'achievs':								// ветка Управление достижениями
				className = 'caption-achievs';
				url += '/card/achievs/index/personid/' + User.personId;
				break;
				
			case 'personal':
				className = 'caption-personal';			// ветка Персональная информация
				url += '/card/personal/index/personid/' + User.personId;
				break;
			
			case 'people':								// Ветки подчиненных и подчиненных подчиненных
			case 'subpeople':
			case 'funcpeople':
				className = 'caption-people';
				url += '/default/index/menu/id/' + id;	// передаем id ветки, которую выбрали в контроллер default/IndexController действие menuAction
				break;									// id равен people или subpeople
				
			default:
				url += '/default/empty';
				break;
		}
	}
	
	if (url == BASE_URL) {
		return false;
	}
	
	main.container.setCaption(text);
	main.container.setCaptionClass(className);
	main.container.replace(url);
}

function menuOnexpand(node)
{
	if (node.hasChildNodes()) {
		return false;
	}
	var id = parseInt(node.id);
	if (!id) {
		return false;
	}
	var handler = loadBranchSubPosts;
	if (id < 0) {
		handler = loadBranchViewPosts;
		id = -id;
	}
	var url = BASE_URL + '/json/sub-posts/id/' + id;
	JsDataLoader.create(url, handler, abort).send();
}

function loadBranchSubPosts(result, text)
{
	if (text) {
		return false;
	}
	loadBranchPosts(result.pid, result.posts);
}

function loadBranchViewPosts(result, text)
{
	if (text) {
		return false;
	}
	var posts = result.posts;
	for (var key in posts) {
		posts[key].id = -posts[key].id;
	}
	loadBranchPosts(-result.pid, posts);
}

function loadBranchPosts(pid, posts)
{
	for (key in posts) {
		menu.addNode({id : posts[key].id, pid : pid, text : posts[key].name, leaf : posts[key].last});
	}
}

function abort()
{
}

function toolbarItemCard()
{
	var url = BASE_URL + '/card/index/index/personid/' + User.personId;
	//Js.open(url, '', 1000, 746, null, null, ['status', 'resizable']);

	var hei = screen.height-150;
	Js.open(url, '',1000, hei, null, null, ['status', 'resizable']);
}

function toolbarItemAchievs()
{
	var url = BASE_URL + '/card/achievs/index/personid/' + User.personId;
	var hei = screen.height-150;
	Js.open(url, '',1000, hei, null, null, ['status', 'resizable']);
}

function toolbarItemRefresh()
{
	location.reload();
}

function toolbarItemHelp()
{
	var url = BASE_URL + '/help';
	Js.open(url, '', 480, 350, null, null, ['status', 'resizable']);
}

function toolbarItemIntegrate()
{
	var div = document.getElementById('integrated_block');
	div.style.display = "block";
	var div1 = document.getElementById('toolbar');				// прячем все элементы на странице, чтобы вывести окно с совместителями
	div1.style.display = "none";
	var div2 = document.getElementById('sidebar');
	div2.style.display = "none";
	var div3 = document.getElementById('container');
	div3.style.display = "none";
	var div4 = document.getElementById('delimiter');
	div4.style.display = "none";
}

function forwardIntegratePerson(person_id)
{
	var url = BASE_URL + '/integrate/index/id/' + person_id ;		// перенаправляем на метод авторизации
	window.location = url;

}