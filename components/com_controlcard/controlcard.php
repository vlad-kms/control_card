<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

/**
 * TODO
 * 1. Сделать регистрацию пользователя из пользовательской части
 * 2. Создание карточки копированием из существующей
 */

defined('_JEXEC') or die;

//define('AVV_DEBUG');
define('AVV_DEBUG',1);

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\HTML\HTMLHelper;

//HTMLHelper::_('stylesheet', 'jui/bootstrap.min.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));

JHTML::_('behavior.tooltip');

JLoader::register('ControlcardHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controlcard.php');
JLoader::register('PersonHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/person.php');

$app = JFactory::getApplication();
$user = JFactory::getUser();
if ($user->guest) {
    $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
    return;
}
if (!ControlcardHelper::getPersonForUser($user->id) && !$user->authorise('core.admin'))
{
	$app->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_USER_NOTLINK_PERSON', $user->username), 'warning');
	return;
}

define('FPDF_FONTPATH', JPATH_LIBRARIES . '/fpdf/font/');
//require JPATH_LIBRARIES.'/fpdf/fpdf.php';
JLoader::register('FPDF', JPATH_LIBRARIES.'/fpdf/fpdf.php');

JLoader::registerPrefix('Avv', JPATH_LIBRARIES.'/avv');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

$controller = JControllerLegacy::getInstance('controlcard'); //ControlcardController

/*
if (defined('AVV_DEBUG')) {
    #$gv = $controller->getView();
    //echo '<pre>';
    print_r('controlcard.php ::: get_class($controller) = '.get_class($controller));
    //print_r($controller->input->data=>'view');
    //print_r($controller->input->data=>'task');
    print_r('<br/>');
    //print_r('111-controlcard.php ::: get_class($controller->getModel()) = '.get_class($controller->getModel()));
    print_r('<br/>');
    print_r($controller);
    print_r('<br/>');
    print_r($controller->getModel());
    print_r('<br/>');
    echo '</pre>';
}
*/

$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();


/*
if (defined('AVV_DEBUG'))
{
	AvvLog::logMsg(['msg' =>
                    [
                        'point enter controlcard.php ===============================================================================',
                        'test',
                        'JPATH_ADMINISTRATOR ___________: ' . JPATH_ADMINISTRATOR,
	                    'JPATH_BASE ____________________: ' . JPATH_BASE,
                        'JPATH_CACHE ___________________: ' . JPATH_CACHE,
                        'JPATH_COMPONENT _______________: ' . JPATH_COMPONENT,
                        'JPATH_COMPONENT_ADMINISTRATOR _: ' . JPATH_COMPONENT_ADMINISTRATOR,
                        'JPATH_COMPONENT_SITE __________: ' . JPATH_COMPONENT_SITE,
                        'JPATH_CONFIGURATION ___________: ' . JPATH_CONFIGURATION,
						'JPATH_INSTALLATION ____________: ' . JPATH_INSTALLATION,
						'JPATH_LIBRARIES _______________: ' . JPATH_LIBRARIES,
						'JPATH_PLUGINS _________________: ' . JPATH_PLUGINS,
						'JPATH_ROOT ____________________: ' . JPATH_ROOT,
						'JPATH_SITE ____________________: ' . JPATH_SITE,
						'JPATH_THEMES __________________: ' . JPATH_THEMES,
						'JPATH_XMLRPC __________________: ' . JPATH_XMLRPC
                    ],
    'category'], defined('AVV_DEBUG'), null, 'controlcard.log');
}
*/
