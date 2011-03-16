<?php

class Card_CardController extends Zend_Controller_Action
{
	public function createAction()
	{
		$request = $this->getRequest();

		$person_id       = $request->getPost('person_id', NULL);
		$card_creator_id = $request->getPost('card_creator_id', NULL);
		$period_start    = $request->getPost('period_start', NULL);
		$period_end      = $request->getPost('period_end', NULL);

		$period_start = ($period_start) ? strtotime($period_start) : NULL;
		$period_end =   ($period_end)   ? strtotime($period_end) : NULL;

		$period = date('Y', $period_start);

		$period_start = date('Y-m-d H:i', $period_start);
		$period_end = date('Y-m-d H:i', $period_end);

		$cards = new Rp_Db_Table_Ach_Cards();

		$where ='person_id = ' . $person_id . ' AND period = ' . $period;

		$old_cards = $cards->fetchAll($where);

		if(count($old_cards) >= 1)
		{
			$card_id = $old_cards->current()->id;
			$new_card = $cards->cut_the_card($person_id, $card_id, $period, $period_start, $period_end, $card_creator_id);
		}
		elseif(count($old_cards) == 0)
		{
			$new_card = $cards->createCard($person_id, $period, $period_start, $period_end, $card_creator_id);
		}

		$view = $this->initView();
		$view->person_id = $person_id;
		$view->card_id = $new_card->id;
		$view->period = $period;

	}

	public function blockAction()
	{
		$request = $this->getRequest();

		$user    = Rp_User::getInstance();
		$card_id = $request->getParam('card_id', NULL);

		$cards = new Rp_Db_Table_Ach_Cards();

		return $cards->blockCard($card_id, $user->id);
	}
}