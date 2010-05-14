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
					<img src="' . $imgSrc . '" class="icon16" alt="Написать письмо" />
				</a>
			';
		}
		
		$is_integrate = "";
		if ($person->id >= 90000000) 
			$is_integrate = "<span style='color: blue; font-size: 12px;'>(совместитель)</span>";
		
		$xhtml .= $is_integrate;
			
		$xhtml .= '
			</div>
		';
		return $xhtml;
	}
}