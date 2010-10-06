<?php

class Zend_View_Helper_AchievsCareerFlags
{	
	public function achievsCareerFlags($value, array $careerFlags)
	{
		$xhtml = array();
		
		$xhtml[] = '<input name="comments[career_flag_id]" type="hidden" value="' . $value . '" />';
		foreach ($careerFlags  as $id => $name) {
			$xhtml[] = '<input name="comments[career_flag_id]" id="career_flag_id_' . $id . '" type="radio"'
					 . ' value="' . $id . '" disabled="disabled"' . ($id == $value ? ' checked="checked"' : '') . ' />'
					 . '<label for="career_flag_id_' . $id . '">' . $name . '</label>';
		}
		return implode('', $xhtml);
	}
}