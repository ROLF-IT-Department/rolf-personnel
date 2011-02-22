<?php

class Zend_View_Helper_ProfileHeader
{
	public function profileHeader(Rp_Db_View_Row_Person $person, $baseUrl)
	{
		$xhtml = '
			<div class="header">
				<span onclick="openCard()">' . $person->fullname . '</span>
		';
		if (trim($person->email)) {
			$imgSrc = $baseUrl . '/img/default/profile/mail.gif';
			$xhtml .= '
				<a href="mailto: ' . $person->email . '">
					<img src="' . $imgSrc . '" class="icon16" alt="Ќаписать письмо" />
				</a>
			';
		}

		$is_integrate = "";
//		if ($person->id >= 90000000)
		switch($person->persg)
		{
			case 2:
			case 3:
			case 7:
			case 8:
			case 'S':
				$is_integrate = "<span style='color: blue; font-size: 12px;'>(" . $person->pgtxt . ")</span>";
				break;
		}

		$is_testperiod = '';
		if( $person->endtest_date >= date('Y-m-d'))
			$is_testperiod = '<span style="color: green; font-size: 12px;">&nbsp;(испытательный срок)</span>';

		$xhtml .= $is_integrate;
		$xhtml .= $is_testperiod;

		$xhtml .= '
			</div>
		';
		return $xhtml;
	}
}