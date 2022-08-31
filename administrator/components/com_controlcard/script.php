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

/**
 * Controlcard script file.
 *
 * @package     A package name
 * @since       1.0
 */
class Com_ControlcardInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter) {}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter) {
	    /*
	    echo '<div>';
	    echo JText::_('COM_CONTROLCARD_PREFLIGHT_SCRIPT').'. '.$route;
        echo '</div>';
        */
    }

	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter) {
	    /*
        echo '<div>';
        echo JText::_('COM_CONTROLCARD_POSTFLIGHT_SCRIPT').'. '.$route;
        echo '</div>';
	    */
    }

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter) {
	    /*
        echo '<div>';
        echo JText::_('COM_CONTROLCARD_INSTALL_SCRIPT');
        echo '<pre>';
        print_r($adapter);
        echo '</pre>';
        echo '</div>';
	    */
    }

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter) {
	    /*
        echo '<div>';
        echo JText::_('COM_CONTROLCARD_UPDATE_SCRIPT');
        echo '<pre>';
        print_r($adapter);
        echo '</pre>';
        echo '</div>';
	    */
    }

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter) {
	    /*
        echo '<div>';
        echo JText::_('COM_CONTROLCARD_UNINSTALL_SCRIPT');
        echo '</div>';
	    */
    }
}
