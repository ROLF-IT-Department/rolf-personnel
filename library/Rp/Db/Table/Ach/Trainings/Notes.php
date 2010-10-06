<?php
/**
 * ROLF Personnel library
 *
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */

/**
 * Îáúåêò òàáëèöû çàìåòîê ê ïğîôåññèîíàëüíîìó ğàçâèòèş
 * 
 * @category   Rp
 * @package    Rp_Db
 * @subpackage Rp_Db_Table
 */
class Rp_Db_Table_Ach_Trainings_Notes extends Rp_Db_Table_Abstract
{
	protected $_name = 'user_rp_ach_trainings_notes';
	
	protected $_referenceMap = array(
		'Training' => array(
			'columns'       => 'training_id',
			'refTableClass' => 'Rp_Db_Table_Ach_Trainings',
			'refColumns'    => 'id'
		)
	);
}