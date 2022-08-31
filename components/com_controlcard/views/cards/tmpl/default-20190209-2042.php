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
/*
if (defined('AVV_DEBUG')) {
    echo '/views/controlcards/tmpl/defaul.php';
    echo '<br/>';
}
*/

HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));

/*
$layout = new FileLayout('controlcard.page');

$data = array();
$data['text'] = 'LISTs LISTs LISTs';
echo $layout->render($data);

echo '<pre>';
//print_r($this);
echo '</pre>';
*/
/*
$doc = JFactory::getDocument();
$baseUrl = JUri::base();
$doc->addStyleSheet($baseUrl . '/media/jui/css/jquery.searchtools.css');
$doc->addScript($baseUrl . '/media/jui/is/jquery.searchtools.min.js');
*/
$isRoot = JFactory::getUser()->authorise('core.admin', $component);
$isDelete= $this->canDelete(2);
$model = $this->getModel();

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<div class="controlcard-cards">
	<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	    <?php if (!empty($this->toolbar)) : ?>
		    <div id="j-sidebar-container" class="span2">
			    <?php echo $this->toolbar; ?>
		    </div>
        <?php endif; ?>

		<?php if ($this->params->get('filter_field') !== 'hide') : ?>
            <fieldset class="filters btn-toolbar clearfix">
                <legend class="hide-cc"><?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_LEGEND'); ?></legend>
                <div class="btn-group">
                    <div class="btn-wrapper">
                        <label class="filter-search-lbl element-invisible" for="filter_search">
							<?php echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?>
                        </label>
                        <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTROLCARD_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_CONTROLCARD_FILTER_LABEL'); ?>" />
                        <button type="submit" class="btn hasTooltip" title="" aria-label="<?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?>" data-original-title="<?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?>">
                            <span class="icon-search" aria-hidden="true"></span>
                        </button>

                    </div>
                    <div class="btn-wrapper">
                        <button type="button" class="btn hasTooltip js-stools-btn-clear bbtn-primary-avv" title=""
                                data-original-title="<?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_CLEAR'); ?>"
                                onclick="avvFilterClear()"
                        ><?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_CLEAR'); ?>
                        </button>
                    </div>
	                <?php
                        $showFP = $this->state->get('filter.show_panel');
                        if ($showFP) {
	                        $classBtn      = 'icon-arrow-up';
	                        $classButton   = ' shown';
	                        $classBtnGroup = '';
                        } else {
	                        $classBtn      = 'icon-arrow-down';
	                        $classButton   = '';
	                        $classBtnGroup = ' hide-cc';
                        }
                    ?>
                    <div class="btn-wrapper">
                        <!--<button type="button" class="btn btn-small hasTip"-->
                        <button type="button" class="btn btn-small hasTooltip<?php echo $classButton; ?>" id="button-showhide-filterpanel"
                                data-original-title="<?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_EXT_DESC'); ?>"
                                onclick="showHideFilterPanel()"
                        >
                            <span id ="btn-showhide-panel" class="<?php echo $classBtn?>" aria-hidden="true"></span>
                            <?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_EXT'); ?>
                        </button>
                    </div>
                    <!-- <button type="submit" class="btn btn-primary-avv" name="filter_submit"><?php //echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?></button> -->
                    <!-- <button type="submit" class="btn btn-primary-avv" name="filter_submit"><?php //echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?></button> -->
                </div>
                <div class="btn-group<?php echo $classBtnGroup; ?>" id="filter-buttons">
                    <div class="rem-btn-wrapper">
	                    <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
                        <input type="hidden" id ="show_filter_panel" name="show_filter_panel" value="<?php echo $this->state->get('filter.show_panel'); ?>" />
                    </div>
                </div>
                <div class="clearfix"></div>
                <!--
				<input type="hidden" name="filter_order" value="" />
				<input type="hidden" name="filter_order_Dir" value="" />
				-->
                <input type="hidden" name="limitstart" value="" />
                <input type="hidden" name="task" value="" />
            </fieldset> <!-- <fieldset class="filters btn-toolbar clearfix"> -->
		<?php endif; ?>
        <!-- вывод pagination, -->
		<fieldset class="list btn-toolbar clearfix">
		    <?php if ($this->params->get('show_pagination_limit')) : ?>
			    <div class="btn-group pull-right">
				    <!-- <label for="list_limit" class="element-invisible"> -->
				    <label for="list_limit" class="element-visible">
					    <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				    </label>
				    <?php
				    echo $this->pagination->getLimitBox();
    				//echo $this->pagination->getListFooter();
	    			?>
		    	</div>
		    <?php endif; ?>
			<?php if ($this->params->get('show_pagination_top', 0)) : ?>
				<?php include('cards_pagination.php'); ?>
			<?php endif; ?>
		</fieldset>
        <!-- Вывод таблицы с данными -->
		<?php $visibleCheck = ($isDelete || $isRoot) ? "" : "hide-cc "; ?>
        <!-- <table class="category persons adminlist"> -->
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
                <th width="30%" class="border-right"><?php //echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_NOTE'); ?>
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
                <th  width="5%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DATE_NEXT'); ?>
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

	                $strTmp = $item->login;
	                if (!empty($strTmp)) {
		                $strTmp .= '</br>' . $item->email;
	                } else {
		                $strTmp = $item->email;
	                }
	                if ($item->dismiss) {
		                if (!empty($strTmp))
		                {
			                $strTmp .= '</br>' . JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION');
		                } else {
			                $strTmp = JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION');
		                }

	                }
	                if (!empty($strTmp)) {
		                $strTmp = JText::_('COM_CONTROLCARD_CARDS_COLTIP_ABOUT_USER') . '::' . $strTmp;
                    }
	                $strTmp = empty(JText::_($strTmp)) ? '': JText::_('title="' . $strTmp . '"');
	                $classTipPerson = empty($strTmp) ? '' : " hasTip ";

	                $tipDismiss= ($item->dismiss) ? JText::_('COM_CONTROLCARD_PERSON_FIELD_DISMISS_TITLE') : '';
                    $classTipDismiss= empty($tipDismiss) ? '' : " hasTip ";

                    $it = $item->performed_type;
                    $strTipNT = ( ($it >= 2) && ($it <= 5)) ? ' title="' . JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED_DATE_NEXT_DESC') . '"': '';
                    $classTipNT = empty($strTipNT) ? '': ' hasTip ';
                    //$item->performed_date_next = ControlcardHelper::getDateNextPerformed($item);
	                ControlcardHelper::fillDateNextPerformed($item);
	                $dayToNextPerform = ($item->day_to_performed == 0) ? '0': (string)$item->day_to_performed;
            ?>
                <tr class="<?php echo $item->performed ? perform: ''; ?> <?php echo 'days-perform-' . $dayToNextPerform; ?>">
                    <!-- check field -->
                    <td class="<?php echo $visibleCheck; ?>nowrap center border-right">
                        <input type="checkbox" id="cb<?php echo $ic; ?>" name="eid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);"/>
                    </td>
                    <!-- ID -->
                    <td class="hide-cc txt-right border-right"><?php echo $item->id; ?></td>
                    <!-- порядковый номер  -->
                    <td class="hide-cc txt-right border-right"><?php echo $ic; ?></td>
                    <!-- номер карточки -->
                    <td class="txt-right border-right"><?php echo $item->num_controlcard; ?></td>
                    <!-- краткое содержание -->
                    <?php
                        $item->link =
	                        '<a class="full-td-a" href="' . JRoute::_('index.php?option=com_controlcard&view=card&id=' . (int)$item->id) . '">'. $this->escape($item->note) .'</a>';
                        if ($itemEdit) :
	                        $link = $item->link;
	                        $td_link=' link';
                        else :
	                        $link = $this->escape($item->note);
	                        $td_link='';
                        endif;
                    ?>
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
                        <td class="border-right"><?php echo $item->email; ?></td>
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
                    <td class="border-right"><?php echo JHtml::_('date', $item->performed_date, 'd.m.Y'); ?>
                    </td>
                    <!-- цикл ИСПОЛНЕНИЯ -->
                    <td class="border-right">
                        <?php
                            switch ($item->performed_type){
                                case 2:
	                                $strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_2');
                                    break;
	                            case 3:
		                            $strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_3');
		                            break;
	                            case 4:
		                            $strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_4');
		                            break;
	                            case 5:
		                            $strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_5');
		                            break;
                                default:
                                    $strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_1');
                            }
                        ?>
                        <?php echo $strType; ?>
                    </td>
                    <!-- доп. параметр Integer ИСПОЛНЕНИЯ -->
                    <td class="txt-right border-right"><?php echo $item->performed_ext_int; ?></td>
                    <!-- следующая дата в цикле ИСПОЛНЕНИЯ -->
                    <td class="border-right<?php echo $classTipNT; ?>"<?php echo $strTipNT; ?>>
                        <?php echo JHtml::_('date', $item->performed_date_next, 'd.m.Y'); ?>
	                    <?php //echo $item->performed_date_next; ?>
                    </td>
                    <!-- осталось дней до ИСПОЛНЕНИЯ -->
                    <td class="txt-right">
		                <?php echo $dayToNextPerform; ?>
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

<?php
