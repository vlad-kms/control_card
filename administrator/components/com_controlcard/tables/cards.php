<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;


/**
 * Controlcard table.
 *
 * @since       1.0
 */
class TableCards extends JTable
{
	/**
	* Constructor
	*
	* @param   JDatabaseDriver  $db  Database driver object.
	*
	* @since   1.0
	*/

	protected   $tableType = 'Cards';

	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__controlcard_cards', 'id', $db);
	}

	public function perform ($pks=null, $value=null) {

		$k = $this->_tbl_key;
		// Sanitize input.
		$pks    = ArrayHelper::toInteger($pks);
		if (!is_null($value)) {
			$value  = (int) $value;
		}

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Get an instance of the table
		/** @var BannersTableBanner $table */
		$table = JTable::getInstance($this->tableType, 'Table');
		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if (!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Change the state
			if (is_null($value)) {
				if ($table->performed == 0)
				{
					$table->performed = 1;
				}
				else
				{
					$table->performed = 0;
				}
			}
			else {
				$table->performed = $value;
			}
			// Check the row
			$table->check();
			// Store the row
			if (!$table->store())
			{
				$this->setError($table->getError());
			}
		}
		return count($this->getErrors()) == 0;
	}

}