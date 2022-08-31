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
 * Controlcard Controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardController extends JControllerLegacy {
    protected $default_view="controlcards";

	public function __construct($config = array()) {
		parent::__construct($config);
	}

    public function getModel($name='', $prefix='', $config=array()){
        if (empty($name)) {
            $name = $this->default_view;
        }
        return parent::getModel($name, $prefix, $config);
    }
/*
    public function getControlcardController() {
        return 'ControlcardController';
    }
*/
}
