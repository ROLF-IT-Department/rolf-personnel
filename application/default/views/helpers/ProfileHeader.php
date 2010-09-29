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
					<img src="' . $imgSrc . '" class="icon16" alt="�������� ������" />
				</a>
			';
		}
		
		$is_integrate = "";
		if ($person->id >= 90000000) 
			$is_integrate = "<span style='color: blue; font-size: 12px;'>(������������)</span>";

		$is_testperiod = '';
		if( $person->endtest_date >= date('Y-m-d'))
			$is_testperiod = '<span style="color: green; font-size: 12px;">&nbsp;(������������� ����)</span>';

		$xhtml .= $is_integrate;
		$xhtml .= $is_testperiod;
			
		$xhtml .= '
			</div>
		';
		return $xhtml;
	}
}