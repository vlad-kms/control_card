<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Controlcard table.
 *
 * @since       1.0
 */
class TableControlcard extends JTable
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
    parent::__construct('#__controlcard_cards', 'id', $db);
  }
}