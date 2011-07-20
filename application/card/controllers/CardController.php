<?php

class Card_CardController extends Zend_Controller_Action
{
	/**
	 * @throws ErrorException
	 * @return void
	 */
	public function createAction()
	{
		$request = $this->getRequest();

		$person_id       = $request->getPost('person_id', NULL);
		$card_creator_id = $request->getPost('card_creator_id', NULL);
		$period_start    = $request->getPost('period_start', NULL);

		$_period_start = ($period_start) ? strtotime($period_start) : NULL;
		$period        = date('Y', $_period_start);

		$period_start = date('Y-m-d 00:00', $_period_start);
		$period_end   = date('Y-12-31 00:00', $_period_start);

		if( ! $person_id OR ! $card_creator_id OR ! $period_start)
		{
			throw new ErrorException('Не определены основные параметры.');
		}

		$_cards = new Rp_Db_Table_Ach_Cards();

		$where ='person_id = ' . (int) $person_id . ' AND period = ' . (int) $period;

		$old_cards = $_cards->fetchAll($where);

		$old_cards_count = count($old_cards);

		if($old_cards_count == 1)
		{
			$card_id = NULL;
			foreach($old_cards as $old_card)
			{
				if( ! $card_id)
				{
					$card_id = $old_card->id;
				}
			}

			$new_card = $_cards->cut_the_card($person_id, $card_id, $period, $period_start, $period_end, $card_creator_id);
		}
		elseif($old_cards_count > 1)
		{
			$old_cards_arr = array();
			foreach($old_cards as $old_card)
			{
				$old_cards_arr[] = array(
					'period_start' => strtotime($old_card->period_start),
					'period_end' => strtotime($old_card->period_end),
					'card' => $old_card
				);
			}

			$cards = array();
			foreach($old_cards_arr as $i => $old_card)
			{
				if(
					    $_period_start > $old_card['period_start']
					AND $_period_start < $old_card['period_end']
				)
				{
					$cards[1] = isset($old_cards_arr[$i - 1]) ? $old_cards_arr[$i - 1]['card'] : NULL;
					$cards[2] = isset($old_cards_arr[$i + 1]) ? $old_cards_arr[$i + 1]['card'] : NULL;
					$cards['current'] = $old_card['card'];
					break;
				}
			}

			if($cards[2] == NULL)
			{
				$new_card = $_cards->cut_the_card($person_id, $cards['current']->id, $period, $period_start, $period_end, $card_creator_id);
			}
			else
			{
				$new_card = $_cards->move_time_border($cards, $_period_start);
			}
		}
		elseif($old_cards_count == 0)
		{
			$new_card = $_cards->createCard($person_id, $period, $period_start, $period_end, $card_creator_id);
		}

		$view = $this->initView();
		$view->person_id = $person_id;
		$view->card_id = $new_card->id;
		$view->period = $period;
	}

	/**
	 * @return bool
	 */
	public function blockAction()
	{
		$request = $this->getRequest();

		$user    = Rp_User::getInstance();
		$card_id = $request->getParam('card_id', NULL);

		$cards = new Rp_Db_Table_Ach_Cards();

		return $cards->blockCard($card_id, $user->id);
	}

	/**
	 * @return Zend_Db_Table_Row_Abstract
	 */
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