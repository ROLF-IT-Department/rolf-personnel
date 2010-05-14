<?php

class Card_View_Helper_AchievsTrainingNotes
{	
	public function achievsTrainingNotes(Zend_Db_Table_Rowset_Abstract $notes)
	{
		$xhtml = array();
		
		$xhtml[] = '
			<table class="notes-table">
				<tbody>
		';
		$counter = 0;
		foreach ($notes as $note) {
			if (empty($note->deleted)) {
				$name = 'notes[' . $note->id . ']';
				$xhtml[] = '
					<tr>
						<td class="notes-field-num">' . ++$counter . '</td>
						<td class="notes-field-date-record">' . $note->date_record . '</td>
					</tr>
					<tr>
						<td class="notes-field-text" colspan="2">
							<div>' . $note->text . '</div>
						</td>
					</tr>
				';
			}
		}
		$xhtml[] = '
				</tbody>
			</table>
		';
		
		return implode('', $xhtml);
	}
}