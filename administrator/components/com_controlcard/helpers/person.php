<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

class PersonHelper
{
	public static function deleteUserFromPerson($user_id=null) {
		if ($user_id != null) {
			$table = JTable::getInstance('Persons', 'Table');
			$tableName = $table->getTableName();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->update($db->qn($tableName))
				->set($db->qn('user_id') . ' = NULL')
				->where($db->qn('user_id') . ' = ' . $db->q($user_id));
			$db->setQuery($query);
			try
			{
				$db->execute();
				$result = true;
			}
			catch (RuntimeException $e)
			{
				$result = false;
			}
		} else {
			$result = false;
		}
		return $result;
	}
}