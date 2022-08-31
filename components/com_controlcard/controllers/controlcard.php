<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

#use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerControlcard extends JControllerLegacy
{
    //protected $default_view="controlCard";

	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}
