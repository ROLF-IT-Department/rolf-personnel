<?php

class Card_AchievsCompetenceNotesController extends Zend_Controller_Action
{
	public function indexAction()
	{	
		$request = $this->getRequest();
		
		$competId = $request->getParam('competid');
		$competence = new Rp_Db_Table_Ach_Cards_Competences();
		$competence = $competence->find($competId)->current();			// выбираем таблицу user_rp_cards_competences 
		$comp = new Rp_Db_Table_Ach_Competences();
		$comp = $comp->find($competence->competence_id)->current();		// выбираем компетенцию из справочника компетенций
		
		$view = $this->initView();
		$view->title = Rp::getTitle('Заметки к компетенции #' . $competence->id);
		$view->comp = $comp;
		$view->competence = $competence;
		$view->notes = $competence->fetchNotes();
	}
	
	public function saveAction()
	{
		$request = $this->getRequest();
		
		$competence = $request->getPost('competence');
		$notes = $request->getPost('notes', array());
		$newNote = $request->getPost('newNote');
		
		$tableNotes = new Rp_Db_Table_Ach_Competences_Notes();
		
		foreach ($notes as $id => $note) {
			$tableNotes->update($note, $id);
		}
		if (trim($newNote['text']) != '') {
			$newNote['competence_id'] = $competence['id'];
			$newNote['author_id'] = Rp_User::getInstance()->person_id;
			
			$tableNotes->insert($newNote);
		}
		
		$this->_redirect('/card/achievs-competence-notes/index/competid/' . $competence['id']);
		
	}

}