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

JHTML::_('behavior.tooltip');
JHTML::_('formbehavior.chosen', 'select');

?>

<div class="controlcard-reason">
    <form action="<?php echo JRoute::_('index.php?option=com_controlcard'); ?>" id="adminForm" method="post" class="form-validate form-horizontal well">
        <div class="control-group">
            <button type="submit" class="btn btn-small button-save" onclick="Joomla.submitbutton('reason.save');">
			    <?php echo JText::_('COM_CONTROLCARD_BUTTON_LABEL_SAVE_CLOSE'); ?>
            </button>
            <button type="submit" class="btn btn-small button-apply" onclick="Joomla.submitbutton('reason.apply');">
			    <?php echo JText::_('JAPPLY'); ?>
            </button>
            <button type="button" class="btn btn-small btn-cancel-del button-cancel" onclick="Joomla.submitbutton('reason.cancel');">
			    <?php echo JText::_('JCANCEL'); ?>
            </button>
        </div>
        <fieldset>
		    <?php echo $this->form->renderFieldset('reason'); ?>
        </fieldset>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-small button-save" onclick="Joomla.submitbutton('reason.save');">
				    <?php echo JText::_('COM_CONTROLCARD_BUTTON_LABEL_SAVE_CLOSE'); ?>
                </button>
                <button type="button" class="btn btn-small btn-cancel-del button-cancel" onclick="Joomla.submitbutton('reason.cancel');">
				    <?php echo JText::_('JCANCEL'); ?>
                </button>

            </div>
        </div>

        <input type="hidden" name="task" value=""/>
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />
	</form>
</div>

<?php
