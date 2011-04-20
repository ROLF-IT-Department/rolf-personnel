<?php

class Card_CardController extends Zend_Controller_Action
{
	public function createAction()
	{
		$request = $this->getRequest();

		$person_id       = $request->getPost('person_id', NULL);
		$card_creator_id = $request->getPost('card_creator_id', NULL);
		$period_start    = $request->getPost('period_start', NULL);
//		$period_end      = $request->getPost('period_end', date('31.12.Y'));

		$_period_start = ($period_start) ? strtotime($period_start) : NULL;
//		$period_end =   ($period_start)   ? date('31.12.Y', $period_start) : NULL;

		$period = date('Y', $_period_start);

		$period_start = date('Y-m-d H:i', $_period_start);
		$period_end = date('Y-12-31 H:i', $_period_start);


		$cards = new Rp_Db_Table_Ach_Cards();

		$where ='person_id = ' . $person_id . ' AND period = ' . $period;

		$old_cards = $cards->fetchAll($where);

		if(count($old_cards) >= 1)
		{
			$card_id = NULL;
			foreach($old_cards as $old_card)
			{
				if( ! $card_id)
				{
					$card_id = $old_card->id;
				}
				$card_period_start = strtotime($old_card->period_start);
				$card_period_end   = strtotime($old_card->period_end);

				if($_period_start < $card_period_start)
				{
					$period_end = date('Y-m-d H:i', $card_period_start - 86400);
				}
			}

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

	public function agreementAction()
	{
		$request      = $this->getRequest();
		$agreement_id = $request->getParam('id');
		$agreement_id = ($agreement_id == 'null') ? NULL : $agreement_id;
		$person_id    = $request->getParam('person_id');
		$period_year  = $request->getParam('period_year');
		$rating_id    = $request->getParam('rating_id', 0);
		$confirmed    = $request->getParam('confirmed', 0);

		$card_agreements = new Rp_Db_Table_Ach_Cards_Agreements();
		$card_agreement = $card_agreements->card_agreement($person_id, $period_year, $rating_id, $confirmed, $agreement_id);

		return $card_agreement;
	}
}