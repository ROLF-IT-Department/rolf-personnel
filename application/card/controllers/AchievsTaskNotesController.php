<?php

class Card_AchievsTaskNotesController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$request = $this->getRequest();
		
		$taskId = $request->getParam('taskid');
		$is_personal = $request->getParam('is_personal', 0);
		$tasks = new Rp_Db_Table_Ach_Tasks();
		$task = $tasks->find($taskId)->current();
		
		$notes = new Rp_Db_Table_Ach_Tasks_Notes();
		switch ($is_personal)
		{
			case 0:
				$note = $notes->fetchTaskNotes($task->id);
				break;
			case 1: 
				$note = $notes->fetchPersonalManagerNotes($task->id);
				break;
			default:
				$note = $notes->fetchTaskNotes($task->id);
				break;
		}
		//$note = $notes->fetchTaskNotes($task->id);
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Заметки к бизнес-цели #' . $task->id);
		$view->task = $task;
		$view->is_personal = $is_personal; 
		$view->notes = $note; 

	}
	
	public function saveAction()
	{
		$request = $this->getRequest();
		
		$task = $request->getPost('task');
		$is_personal = $request->getPost('is_personal');
		$notes = $request->getPost('notes', array());
		$newNote = $request->getPost('newNote');
		
		$tableNotes = new Rp_Db_Table_Ach_Tasks_Notes();
		
		foreach ($notes as $id => $note) {
			$tableNotes->update($note, $id);
		}
		if (trim($newNote['text']) != '') {
			$newNote['task_id'] = $task['id'];
			$newNote['author_id'] = Rp_User::getInstance()->person_id;
			$newNote['is_personal'] = $is_personal;
			$tableNotes->insert($newNote);
		}
		
		$this->_redirect('/card/achievs-task-notes/index/taskid/' . $task['id'] . '/is_personal/' . $is_personal);
	}
}