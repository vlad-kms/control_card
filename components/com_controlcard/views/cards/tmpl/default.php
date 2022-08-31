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

$app = JFactory::getApplication();
//$isRoot = JFactory::getUser()->authorise('core.admin', $component);
$isRoot = JFactory::getUser()->authorise('core.admin', $app->input->getString('option', 'com_controlcard'));
$isDelete= $this->canDelete(0);
$model = $this->getModel();

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$filePDF = $app->getUserState('global.cardFilePDF', '');
$app->setUserState('global.cardFilePDF', '');
?>

<div class="controlcard-cards">
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
				<?php include('cards_pagination.php'); ?>
			<?php endif; ?>
		</fieldset>
        <!-- Вывод таблицы с данными -->
		<?php $visibleCheck = ($isDelete || $isRoot || true) ? "" : "hide-cc "; ?>
        <!-- <table class="category persons adminlist"> -->
        <div class="clearfix"></div>
        <table class="persons adminlist">
            <thead> <!-- Заголовок таблицы-->
            <tr>
                <!-- check field -->
                <th width="1%" class="<?php echo $visibleCheck; ?>nowrap center checkmark-col border-right">
                    <input type="checkbox" name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="Выбрать все">
                </th>
                <!-- ID -->
                <th  width="1%" class="hide-cc border-right">ID</th>
                <!-- порядковый номер  -->
                <th  width="2%" class="hide-cc border-right"><?php echo JText::_('COM_CONTROLCARD_PERSONS_COLTITLE_NPP'); ?>
                </th>
                <!-- номер карточки -->
                <th width="10%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_NUMCARD'); ?>
	                <!-- <?php //echo JHtml::_('searchtools.sort', 'COM_CONTROLCARD_CARDS_COLTITLE_NUMCARD', 'a.num_controlcard', $listDirn, $listOrder); ?> -->
                </th>
                <!-- краткое содержание -->
                <th width="30%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_NOTE'); ?>
                </th>
                <!-- исполнитель -->
                <th width="15%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERSON'); ?>
                </th>
                <!-- логин -->
                <?php if ($this->params->get('show_login_list')) : ?>
                    <th width="10%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_LOGIN'); ?></th>
                <?php endif; ?>
                <!-- емайл -->
                <?php if ($this->params->get('show_email_list')) : ?>
                    <th width="10%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_EMAIL'); ?></th>
                <?php endif; ?>
                <!-- флаг уволен -->
                <?php if ($this->params->get('show_dismiss_list')) : ?>
                    <th width="1%" class="nowrap center checkmark-col"><?php echo JText::_('COM_CONTROLCARD_PERSON_FIELD_DISMISS_TITLE'); ?></th>
                <?php endif; ?>
                <!-- флаг ИСПОЛНЕНА карточка -->
                <?php if ($this->params->get('performed_list.show')) : ?>
                    <th  width="1%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED'); ?>
                    </th>
                <?php endif; ?>
                <!-- дата ИСПОЛНЕНИЯ -->
                <th  width="5%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DATE'); ?>
                </th>
                <!-- цикл ИСПОЛНЕНИЯ -->
                <th  width="5%" class="hasTip border-right" title ="<?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_TYPE_DESC'); ?>"> <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_TYPE'); ?>
                </th>
                <!-- доп. параметр Integer ИСПОЛНЕНИЯ -->
                <th  width="5%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_EXT_INT'); ?>
                </th>
                <!-- следующая дата в цикле ИСПОЛНЕНИЯ -->
                <th  width="10%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DATE_NEXT'); ?>
                </th>
                <!-- осталось дней до ИСПОЛНЕНИЯ -->
                <th  width="5%" class=""><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DAYS_LEFT'); ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php
                $ic=0;
                foreach ($this->items as $item) :
                    $itemEdit = $this->canEdit(0, $item);

                    $tipNote = empty(JText::_($item->note_big)) ? '': JText::_('title="' . $item->note_big . '"');
                    $classTipNote = empty($tipNote) ? '' : " hasTip ";

                    // Сформировать tooltip подсказку для персоны
                    // должность
                    $sb = '<strong>';
                    $se = '</strong>';
	                $strTmp = $item->person_post;
	                if (!empty($strTmp)) {
		                $strTmp = $sb . JText::_('COM_CONTROLCARD_PERSON_FIELD_PERSON_POST_TITLE') . $se . ': ' . $strTmp;
                    }
	                // системный логин
                    $stL = $item->login;
	                if (!empty($stL)) {
		                if (!empty($strTmp)) {
			                $strTmp .= '</br>';
		                }
			            $strTmp .= $sb . JText::_('COM_CONTROLCARD_CARDS_COLTITLE_LOGIN') . $se . ': ' . $stL;
                    }
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
	                $dayToNextPerform = ($item->day_to_performed == 0) || empty($item->day_to_performed) ? '0': (string)$item->day_to_performed;
            ?>
                <tr class="<?php echo $item->performed ? 'perform ' : ''; ?> <?php echo 'days-perform-' . $dayToNextPerform; ?>">
                    <!-- check field -->
                    <td class="<?php echo $visibleCheck; ?>nowrap center border-right">
                        <input type="checkbox" id="cb<?php echo $ic; ?>" name="eid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);"/>
                    </td>
                    <!-- ID -->
                    <td class="hide-cc txt-right border-right"><?php echo $item->id; ?></td>
                    <!-- порядковый номер  -->
                    <td class="hide-cc txt-right border-right"><?php echo $ic; ?></td>
	                <?php
	                    $item->link =
		                    '<a class="full-td-a" href="' . JRoute::_('index.php?option=com_controlcard&view=card&id=' . (int)$item->id) . '">'. $this->escape($item->note) .'</a>';
	                    if ($itemEdit) :
		                    $link = $item->link;
		                    $linkNum = '<a class="full-td-a" href="' . JRoute::_('index.php?option=com_controlcard&view=card&id=' . (int)$item->id) . '">'. $this->escape($item->num_controlcard) .'</a>';
    		                $td_link=' link';
	                    else :
		                    $link = $this->escape($item->note);
		                    $linkNum = $this->escape($item->num_controlcard);
		                    $td_link='';
	                    endif;
	                ?>
                    <!-- номер карточки -->
                    <td class="txt-right border-right full-td <?php echo $td_link; ?>"><?php echo $linkNum; ?></td>
                    <!-- краткое содержание -->
                    <!-- <td class="border-right <?php //echo $classTipNote; ?>" <?php //echo $tipNote; ?>><?php //echo $item->note; ?></td> -->
                    <td class="border-right full-td <?php echo $classTipNote; ?><?php echo $td_link; ?>" <?php echo $tipNote; ?>><?php echo $link; ?></td>
                    <!-- исполнитель -->
                    <td class="border-right <?php echo $classTipPerson?>" <?php echo $strTmp; ?>><?php echo $item->fio; ?></td>
                    <!-- логин -->
	                <?php if ($this->params->get('show_login_list')) : ?>
                        <td class="border-right"><?php echo $item->login; ?></td>
	                <?php endif; ?>
                    <!-- емайл -->
	                <?php if ($this->params->get('show_email_list')) : ?>
                        <td class="border-right"><?php echo empty($item->email)? $item->emailuser: $item->email; ?></td>
	                <?php endif; ?>
                    <!-- флаг уволен -->
	                <?php if ($this->params->get('show_dismiss_list')) : ?>
                        <td class="nowrap center border-right">
			                <?php //echo $item->dismiss; ?>
                            <input type="checkbox" id="dism<?php echo $ic; ?>" name="dism[]" value="<?php echo $item->dismiss; ?>"
				                <?php echo $item->dismiss ? ' checked ' : ' ';?> disabled
                            />
                        </td>
	                <?php endif; ?>
                    <!-- флаг ИСПОЛНЕНА карточка -->
                    <?php if ($this->params->get('performed_list.show')) : ?>
                        <td class="nowrap center border-right">
	                        <?php if ($itemEdit && $this->params->get('performed_list.allow_change')) :
		                        $cbDisabled = '';
	                        else :
		                        $cbDisabled = ' disabled ';
	                        endif;
                            ?>
                            <input type="checkbox" id="ccb<?php echo $ic; ?>" name="e-eid[]" value="<?php echo $item->id; ?>"
	    	                    <?php echo $item->performed ? ' checked ' : ' ';?>
		                        <?php echo $cbDisabled; ?>
                                onclick="Joomla.listItemTask('<?php echo "cb$ic"; ?>', 'cards.perform');"
                            />
                        </td>
                    <?php endif; ?>
                    <!-- дата ИСПОЛНЕНИЯ -->
                    <td class="border-right"><?php echo JHtml::_('date', $item->performed_date, 'd-m-Y'); ?>
                    </td>
                    <!-- цикл ИСПОЛНЕНИЯ -->
                    <td class="border-right">
                        <?php echo ControlcardHelper::getStringPerformedType($item->performed_type); ?>
                    </td>
                    <!-- доп. параметр Integer ИСПОЛНЕНИЯ -->
                    <td class="txt-right border-right"><?php echo $item->performed_ext_int; ?></td>
                    <!-- следующая дата в цикле ИСПОЛНЕНИЯ -->
                    <td class="border-right<?php echo $classTipNT; ?>"<?php echo $strTipNT; ?>>
                        <?php
                            if ( empty($item->performed_date_next) ) :
	                            echo '';
                            else :
	                            echo JHtml::_('date', $item->performed_date_next, 'd-m-Y');
                            endif;
                        ?>
	                    <?php //echo $item->performed_date_next; ?>
                    </td>
                    <!-- осталось дней до ИСПОЛНЕНИЯ -->
                    <td class="txt-right">
		                <?php echo $dayToNextPerform == 0 ? '' : $dayToNextPerform; ?>
                    </td>

                </tr>
            <?php
                $ic++;
                endforeach;
            ?>
            </tbody>

        </table>
        <fieldset class="list btn-toolbar clearfix">
			<?php include('cards_pagination.php'); ?>
        </fieldset>

        <input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />
	</form>
</div>

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
        //console.log(ri);
    </script>

<?php
