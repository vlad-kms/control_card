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

HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));
/*
if (defined('AVV_DEBUG')) {
    echo '/views/persons/tmpl/default.php';
    echo '<br/>';
}
*/

JFactory::getDocument()->setTitle(strip_tags(JText::_('COM_CONTROLCARD_TITLEAPP_PERSONS')));

$items = $this->items;
//$this->pagination=$this->get('Pagination');
$pagination = $this->pagination;
$component=$this->getModel()->get('option');
$isRoot = JFactory::getUser()->authorise('core.admin', $component);
$isDelete= JFactory::getUser()->authorise('core.persons.delete', $component);
?>

<div class="controlcard-persons">
    <form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
    <?php if ($this->params->get('filter_field') !== 'hide') : ?>
        <fieldset class="filters btn-toolbar clearfix">
            <legend class="hide-cc"><?php echo JText::_('COM_CONTROLCARD_FORM_FILTER_LEGEND'); ?></legend>
            <div class="btn-group">
                <div class="btn-wrapper">
                    <label class="filter-search-lbl element-invisible" for="filter_search">
		                <?php //echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?>
                    </label>
                    <!--
                    <input type="text" name="filter_search" id="filter_search" value="<?php //echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php //echo JText::_('COM_CONTROLCARD_FILTER_SEARCH_DESC'); ?>" placeholder="<?php //echo JText::_('COM_CONTROLCARD_FILTER_LABEL'); ?>">
                    -->
                    <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTROLCARD_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>">
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
                <!-- <button type="submit" class="btn btn-primary-avv" name="filter_submit"><?php //echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?></button> -->
            </div>
	        <?php if (!empty($this->toolbarPersons)) : ?>
                <div id="j-sidebar-container" class="span2">
			        <?php echo $this->toolbarPersons; ?>
                </div>
	        <?php endif; ?>
            <!--
            <input type="hidden" name="filter_order" value="" />
            <input type="hidden" name="filter_order_Dir" value="" />
            -->
            <input type="hidden" name="limitstart" value="" />
            <input type="hidden" name="task" value="" />
        </fieldset> <!-- <fieldset class="filters btn-toolbar clearfix"> -->
    <?php endif; ?>
    <fieldset class="list btn-toolbar clearfix">
	    <?php if ($this->params->get('show_pagination_limit')) : ?>
            <div class="btn-group pull-right">
                <!-- <label for="list_limit" class="element-invisible"> -->
                <label for="list_limit" class="element-visible">
			        <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
                </label>
			    <?php
			        echo $pagination->getLimitBox();
			        //echo $this->pagination->getListFooter();
			    ?>
            </div>
		    <?php if ($this->params->get('show_pagination_top', 0)) : ?>
			    <?php include('persons_pagination.php'); ?>
		    <?php endif; ?>
	    <?php endif; ?>
    </fieldset>
    <!-- <?php //$visibleCheck = ($this->canDo->get('core.persons.edit') && $isRoot) ? "" : "hide-cc "; ?> -->
    <?php $visibleCheck = ($isDelete || $isRoot) ? "" : "hide-cc "; ?>
        <table class="category persons adminlist even">
            <thead> <!-- Заголовок таблицы-->
                <tr>
                    <th width="3%" class="nowrap <?php echo $visibleCheck; ?>center checkmark-col">
                        <input type="checkbox" name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="Выбрать все"/>
                    </th>
                    <th width="1%" class="hide-cc">ID</th>
                    <th width="2%" class="nowrap center"><?php echo JText::_('COM_CONTROLCARD_PERSONS_COLTITLE_NPP'); ?></th>
                    <th width="24%"><?php echo JText::_('COM_CONTROLCARD_PERSONS_COLTITLE_FIO'); ?></th>
                    <th width="24%"><?php echo JText::_('COM_CONTROLCARD_PERSON_FIELD_PERSON_POST_TITLE'); ?></th>
                    <th width="1%" class="hide-cc">USER ID</th>
                    <th width="1%" class="hide-cc">USER NAME</th>
                    <th width="10%">Login</th>
                    <th width="20%">E-Mail</th>
                    <th width="15%" class="nowrap center checkmark-col"><?php echo JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php $ic=0; foreach ($items as $item) : ?>
                <?php
	                $item->link =
		                '<a class="full-td-a" href="' . JRoute::_('index.php?option=com_controlcard&view=person&id=' . (int) $item->id) . '">'. $this->escape($item->fio) .'</a>';
                    if ($this->canDo->get('core.persons.edit')) :
                        $link = $item->link;
                        $td_link=' link';
                    else :
	                    $link = $this->escape($item->fio);
	                    $td_link='';
                    endif;
                ?>
                <tr>
                    <td class="<?php echo $visibleCheck; ?>nowrap center">
                        <input type="checkbox" id="cb<?php echo $ic; ?>" name="eid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);"/>
                    </td>
                    <td class="hide-cc"><?php echo $item->link; ?></td>
                    <td class="txt-right"><?php echo $this->pagination->limitstart + 1 + $ic; ?></td>
                    <td class="full-td<?php echo $td_link; ?>"><?php echo $link; ?></td>
                    <td class=""><?php echo $item->person_post; ?></td>
                    <td class="hide-cc"><?php echo $item->user_id;?></td>
                    <td class="hide-cc"><?php echo $item->name; ?></td>
                    <td><?php echo $this->escape($item->username) ;?></td>
                    <td><?php echo empty($item->email)? $item->emailuser : $item->email; ?></td>
                    <td class="nowrap">
	                    <?php if ($this->canDo->get('core.persons.edit')) :
                            $cbDisabled = '';
	                    else :
		                    $cbDisabled = ' disabled ';
	                    endif;
	                    ?>

                        <input type="checkbox" id="ccb<?php echo $ic; ?>" name="e-eid[]" value="<?php echo $item->id; ?>"
                               <?php echo $item->dismiss ? ' checked ' : ' ';?>
	                           <?php echo $this->canDo->get('core.persons.edit') ? ' ' : ' disabled ';?>
                               onclick="Joomla.listItemTask('<?php echo "cb$ic"; ?>', 'persons.dismiss');"
                        />
	                    <?php if ($item->dismiss) : echo JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION_FIELD'); endif; ?>
                    </td>
                </tr>
            <?php
                $ic++;
                endforeach;
            ?>
            </tbody>
        </table>
        <!-- <input type="hidden" name="task" value="" /> -->
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />

        <fieldset class="list btn-toolbar clearfix">
	        <?php include('persons_pagination.php'); ?>
        </fieldset>
    </form>

</div> <!-- class="controlcard-persons"-->

<?php
