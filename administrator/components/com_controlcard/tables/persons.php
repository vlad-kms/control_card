<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31.12.2018
 * Time: 14:32
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
if (defined('AVV_DEBUG')) {
    echo 'admin/tables/persons.php';
    echo '<br/>';
}
*/

class TablePersons extends JTable
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  $db  Database driver object.
     *
     * @since   1.0
     */
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__controlcard_persons', 'id', $db);
    }

    public function dismiss ($pks=null, $value=1) {
        $k = $this->_tbl_key;

        // Sanitize input.
        $pks    = ArrayHelper::toInteger($pks);
        $value  = (int) $value;

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
        $table = JTable::getInstance('Persons', 'Table');
        // For all keys
        foreach ($pks as $pk)
        {
            // Load the banner
            if (!$table->load($pk))
            {
                $this->setError($table->getError());
            }

            // Change the state
            $table->dismiss = $value;
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