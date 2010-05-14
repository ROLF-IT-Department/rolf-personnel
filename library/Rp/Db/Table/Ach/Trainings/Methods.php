<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Îáúåêò òàáëèöû ñïğàâî÷íèêà ìåòîäîâ ïğîô. ğàçâèòèÿ.
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Trainings_Methods extends Rp_Db_Table_Abstract 
{
	protected $_name = 'user_rp_ach_trainings_methods';
	
	protected $_referenceMap = array(
		'Group' => array(
			'columns'       => 'group_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Trainings_GroupsMethods',
			'refColumns'    => 'id'
		)
	);
}