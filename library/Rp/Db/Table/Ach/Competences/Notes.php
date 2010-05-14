<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Îáúåêò òàáëèöû çàìåòîê ê êîìïåòåíöèÿì.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Competences_Notes extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_cards_competences_notes';
	
	protected $_referenceMap = array(
		'Competence' => array(
			'columns'       => 'competence_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Cards_Competences',
			'refColumns'    => 'id'
		)
	);
}