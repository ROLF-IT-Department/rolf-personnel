<?php

class Card_AchievsTrainingNotesController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$request = $this->getRequest();
		
		$trainId = $request->getParam('trainId');
		$trainings = new Rp_Db_Table_Ach_Trainings();
		$training = $trainings->find($trainId)->current();
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Заметки к цели проф. развития #' . $training->id);
		$view->training = $training;
		$view->notes = $training->fetchNotes();
	}
	
	public function saveAction()
	{
		$request = $this->getRequest();
		
		$training = $request->getPost('training');
		$notes = $request->getPost('notes', array());
		$newNote = $request->getPost('newNote');
		
		$tableNotes = new Rp_Db_Table_Ach_Trainings_Notes();
		
		foreach ($notes as $id => $note) {
			$tableNotes->update($note, $id);
		}
		if (trim($newNote['text']) != '') {
			$newNote['training_id'] = $training['id'];
			$newNote['author_id'] = Rp_User::getInstance()->person_id;
			
			$tableNotes->insert($newNote);
		}
		
		$this->_redirect('/card/achievs-training-notes/index/trainId/' . $training['id']);
	}
}