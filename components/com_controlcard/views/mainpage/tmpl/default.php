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

$app = JFactory::getApplication();
/*
echo '<pre>';
$data = array('title'=>'title', 'ddd'=>'12312312');
$layout = new FileLayout('controlcard.page');
$hhh=$layout->render($data);
echo '</pre>';
echo 'View' . ucfirst($this->getName());
*/

?>
<div class="mainpage">
    <!--
	<form action="<?php //echo htmlspecialchars(JUri::getInstance()->toString() . '?option=com_controlcard'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php //if (!empty($this->toolbar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php //echo $this->toolbar; ?>
		</div>
	<?php //endif; ?>
		<input type="hidden" name="option" value="com_controlcard" />
	</form>
    -->
	<div class="item-mainpage">
		<div class="btn-1"><a href="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '?option=com_controlcard&view=cards'); ?>"><?php echo JText::_("COM_CONTROLCARD"); ?></a></div>
	</div>
    <div class="item-mainpage">
        <div class="btn-1"><a href="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '?option=com_controlcard&view=persons'); ?>"><?php echo JText::_("COM_CONTROLCARD_PERSONS"); ?></a></div>
    </div>
    <div class="item-mainpage">
        <div class="btn-1"><a href="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '?option=com_controlcard&view=reasons'); ?>"><?php echo JText::_("COM_CONTROLCARD_REASONS"); ?></a></div>
    </div>
</div>
