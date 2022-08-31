<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

/*
if (defined('AVV_DEBUG')) {
    echo '/views/controlcard/tmpl/default.php';
    echo '<br/>';
}
*/

HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));

$data = array('title'=>'title', 'ddd'=>'12312312');
$layout = new FileLayout('controlcard.page');
$hhh=$layout->render($data);

$app = JFactory::getApplication();
/*
echo '<pre>';
//print_r($controller);
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
//print_r($app);
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
//print_r($app);
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
print_r("=======================================================================================================================\n");
//print_r($app->input->getCmd('task'));
//var_dump($GLOBALS);
echo '</pre>';


$data['text'] = 'Hello Joomla! ПРИВЕТ Алиса!';


echo $layout->render($data);
*/
echo 'HELLO controlcard VIEW';
echo $hhh;