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

JFactory::getDocument()->setTitle(strip_tags(JText::_('COM_CONTROLCARD_TITLEAPP_REASONS')));

//$isRoot = JFactory::getUser()->authorise('core.admin');
//$model = $this->getModel();

$isDelete= $this->canDelete();

//$listOrder = $this->escape($this->state->get('list.ordering'));
//$listDirn  = $this->escape($this->state->get('list.direction'));

//$app = JFactory::getApplication();
$hide_cc = 'hide-cc';
$hide_cc = '';
?>

<div class="controlcard-reasons">
	<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
		<?php if ($this->params->get('filter_field') !== 'hide') : ?>
            <fieldset class="filters btn-toolbar clearfix">
                <input type="hidden" name="limitstart" value="" />
                <input type="hidden" name="task" value="" />
            </fieldset> <!-- <fieldset class="filters btn-toolbar clearfix"> -->
		<?php endif; ?>
        <div class="js-stools clearfix">
            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        </div>
		<?php if (!empty($this->toolbar)) : ?>
            <div id="j-sidebar-container" class="span2">
				<?php echo $this->toolbar; ?>
            </div>
		<?php endif; ?>

        <!--<div class="clearfix"></div>-->

        <!-- вывод pagination, -->
		<fieldset class="list btn-toolbar clearfix">
		    <?php if ($this->params->get('show_pagination_limit')) : ?>
			    <div class="btn-group pull-right">
				    <!-- <label for="list_limit" class="element-invisible"> -->
				    <label for="list_limit" class="element-visible">
					    <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				    </label>
				    <?php echo $this->pagination->getLimitBox(); //echo $this->pagination->getListFooter(); ?>
		    	</div>
		    <?php endif; ?>
			<?php if ($this->params->get('show_pagination_top', 0)) : ?>
				<?php include('reasons_pagination.php'); ?>
			<?php endif; ?>
		</fieldset>

        <div class="clearfix"></div>

        <?php // ************************************************************************************ ?>
        <?php // Вывод таблицы с данными ************************************************************ ?>
		<?php // ************************************************************************************ ?>
		<?php $visibleCheck = ($isDelete || $isRoot || true) ? "" : $hide_cc; ?>
        <!-- <table class="category persons adminlist"> -->

        <table class="persons adminlist reasons">
            <thead> <!-- Заголовок таблицы-->
            <tr>
                <!-- check field -->
                <th width="5%" class="<?php echo $visibleCheck; ?>nowrap center checkmark-col border-right">
                    <input type="checkbox" name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="Выбрать все">
                </th>
                <!-- ID -->
                <th  width="10%" class="<?php echo $hide_cc;?> border-right">ID</th>
                <!-- наименование -->
                <th width="84%" class=""><?php echo JText::_('COM_CONTROLCARD_REASON_COLTITLE_NAME'); ?>
	                <!-- <?php //echo JHtml::_('searchtools.sort', 'COM_CONTROLCARD_CARDS_COLTITLE_NUMCARD', 'a.num_controlcard', $listDirn, $listOrder); ?> -->
                </th>
                <th  width="1%" class=""></th>
            </tr>
            </thead>

            <tbody>
            <?php
                foreach ($this->items as $item) :
                    $itemEdit = $this->canEdit($item);

                    $tipNote = empty(JText::_($item->note_big)) ? '': JText::_('title="' . $item->note_big . '"');
                    $classTipNote = empty($tipNote) ? '' : " hasTip ";

                    // Сформировать tooltip подсказку для персоны
                    // должность
                    $sb = '<strong>';
                    $se = '</strong>';
	                $strTmp = $item->person_post;
	                if (!empty($strTmp)) :
		                $strTmp = $sb . JText::_('COM_CONTROLCARD_PERSON_FIELD_PERSON_POST_TITLE') . $se . ': ' . $strTmp;
                    endif;
	                // системный логин
                    $stL = $item->login;
	                if (!empty($stL)) :
		                if (!empty($strTmp)) :
			                $strTmp .= '</br>';
		                endif;
			            $strTmp .= $sb . JText::_('COM_CONTROLCARD_CARDS_COLTITLE_LOGIN') . $se . ': ' . $stL;
                    endif;
	                // EMail
	                $stL = $item->email;
	                if (!empty($stL)) {
		                if (!empty($strTmp)) {
			                $strTmp .= '</br>';
		                }
		                $strTmp .= $sb . JText::_('COM_CONTROLCARD_CARDS_COLTITLE_EMAIL') . $se . ': ' . $stL;
	                }
	                // состояние персоны (уволен или не уволен)
	                if ($item->dismiss) {
		                if (!empty($strTmp))
		                {
			                $strTmp .= '</br>';
		                }
			            $strTmp .= $sb . JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION') . $se;
	                }
	                if (!empty($strTmp)) {
		                $strTmp = $sb . JText::_('COM_CONTROLCARD_CARDS_COLTIP_ABOUT_USER') . $se . '::' . $strTmp;
                    }
	                $strTmp = empty(JText::_($strTmp)) ? '': JText::_('title="' . $strTmp . '"');
	                $classTipPerson = empty($strTmp) ? '' : " hasTip ";

	                $tipDismiss= ($item->dismiss) ? JText::_('COM_CONTROLCARD_PERSON_FIELD_DISMISS_TITLE') : '';
                    $classTipDismiss= empty($tipDismiss) ? '' : " hasTip ";

                    $it = $item->performed_type;
                    $strTipNT = ( ($it >= 2) && ($it <= 5)) ? ' title="' . JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DATE_NEXT_DESC') . '"': '';
                    $classTipNT = empty($strTipNT) ? '': ' hasTip ';
                    //$item->performed_date_next = ControlcardHelper::getDateNextPerformed($item);
	                //ControlcardHelper::fillDateNextPerformed($item);
	                $dayToNextPerform = ($item->day_to_performed == 0) ? '': (string)$item->day_to_performed;
            ?>
                <tr>
                    <!-- check field -->
                    <td class="<?php echo $visibleCheck; ?>nowrap center border-right">
                        <input type="checkbox" id="cb<?php echo $ic; ?>" name="eid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);"/>
                    </td>
                    <!-- ID -->
                    <td class="<?php echo $hide_cc; ?> txt-right border-right"><?php echo $item->id; ?></td>
                    <!-- наименование -->
	                <?php
	                    $item->link =
		                    '<a class="full-td-a" href="' . JRoute::_('index.php?option=com_controlcard&view=reason&id=' . (int)$item->id) . '">'. $this->escape($item->name) .'</a>';
	                    if ($itemEdit) :
		                    $link = $item->link;
    		                $td_link=' link';
	                    else :
		                    $link = $this->escape($item->name);
		                    $td_link='';
	                    endif;
	                ?>
                    <td class="full-td<?php echo $td_link; ?>"><?php echo $link; ?></td>
                </tr>
            <?php
                endforeach;
            ?>
            </tbody>

        </table>
        <fieldset class="list btn-toolbar clearfix">
			<?php include('reasons_pagination.php'); ?>
        </fieldset>

        <input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />
	</form>
</div>

<script>
    //console.log(jQuery.fn.jQuery);
    //jQuery('#jform_reason').parent().editableSelect({filter:false});
    //jQuery('#sel_reason').editableSelect({filter:false});
    //console.log(ri);
</script>

<?php
