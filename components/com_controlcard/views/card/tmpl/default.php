<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');

HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));

//HTMLHelper::_('stylesheet', 'com_controlcard/jquery-editable-select.min.css', array('version' => 'auto', 'relative' => true));
//HTMLHelper::_('script', 'com_controlcard/jquery-editable-select.js', array('version' => 'auto', 'relative' => true));


$app = JFactory::getApplication();
$app->getUserState('global.isPDF', 0);

$filePDF = $app->getUserState('global.cardFilePDF', '');
$app->setUserState('global.cardFilePDF', '');
if (!empty($filePDF))
{
	////$host=JFactory::getUri()->getHost();
?>
    <!-- //// для всех php-блоков убрать, если убираются комментарии
    <object data="<?php ////echo $filePDF; ?>" type="application/pdf" width="750px" height="750px">
        <embed type="application/pdf" src="<?php ////echo $filePDF; ?>" width="600" height="400" />
            <p>This browser does not support PDFs. Please download the PDF to view it: <a href="<?php ////echo $filePDF; ?>">Download PDF</a>.</p>
        </embed>
    </object>
    -->
<?php
}
$item = $this->item;
if ($item) :
	JFactory::getDocument()->setTitle(strip_tags(JText::_('COM_CONTROLCARD_CARDS_COLTITLE_CARD') . ' ' .
			$item->num_controlcard));
?>
	<form action="<?php echo JRoute::_('index.php?option=com_controlcard'); ?>" id="adminForm" method="post" class="form-validate form-horizontal well">
        <div class="control-group">
            <button type="submit" class="btn btn-small button-save" onclick="Joomla.submitbutton('card.save');">
	            <?php echo JText::_('COM_CONTROLCARD_BUTTON_LABEL_SAVE_CLOSE'); ?>
            </button>
            <button type="submit" class="btn btn-small button-apply" onclick="Joomla.submitbutton('card.apply');">
		        <?php echo JText::_('JAPPLY'); ?>
            </button>
            <button type="button" class="btn btn-small btn-cancel-del button-cancel" onclick="Joomla.submitbutton('card.cancel');">
		        <?php echo JText::_('JCANCEL'); ?>
            </button>
            <button type="submit" class="btn btn-small button-print" onclick="Joomla.submitbutton('card.print');"><?php echo JText::_('JGLOBAL_PRINT'); ?></button>
            <button type="submit" class="btn btn-small button-send2email" onclick="Joomla.submitbutton('card.send2email');"><?php echo JText::_('JGLOBAL_EMAIL'); ?></button>
        </div>
		<fieldset>
			<?php echo $this->form->renderFieldset('card'); ?>
		</fieldset>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-small button-save" onclick="Joomla.submitbutton('card.save');">
					<?php echo JText::_('COM_CONTROLCARD_BUTTON_LABEL_SAVE_CLOSE'); ?>
				</button>
				<button type="button" class="btn btn-small btn-cancel-del button-cancel" onclick="Joomla.submitbutton('card.cancel');">
					<?php echo JText::_('JCANCEL'); ?>
				</button>

			</div>
		</div>

        <input type="hidden" name="returnprint" value="<?php echo base64_encode(JFactory::getUri()->toString()); ?>" />
        <input type="hidden" name="retrun1" value="<?php echo JFactory::getUri()->toString(); ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>

    <script>
        //console.log(jQuery.fn.jQuery);
        //jQuery('#jform_reason').parent().editableSelect({filter:false});
        //jQuery('#sel_reason').editableSelect({filter:false});
        <?php
            if (!empty($filePDF)) :
        ?>
            window.open(<?php echo '"'. $filePDF . '"'; ?>, 'newWindow');
        <?php
            endif;
        ?>
        rs = jQuery('#jform_reason_sel');
        ri = jQuery('#jform_reason');
        ri.parent().prev().hide();
        ri.offset({left:rs.offset().left, top:rs.offset().top});
        //ri.height(rs.height());
        ri.width(rs.width()-20);
        rs.on('change', function(){
                ri.attr('value', jQuery('#jform_reason_sel :selected').html())
            }
        );
        //console.log(ri);
    </script>

<?php
    echo '';
else :
	$app->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_NOT_RIGHT'), 'warning');
	$app->enqueueMessage(JText::_(JText::sprintf('COM_CONTROLCARD_ERROR_NOT_CARD',
			$this->escape($this->id))), 'warning');
	//return;
	//JRouter
endif;
