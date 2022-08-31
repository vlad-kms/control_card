<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

class TableReasons extends JTable
{
	//protected $tableType = 'Reasons';

	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__controlcard_reasons', 'id', $db);
	}

}