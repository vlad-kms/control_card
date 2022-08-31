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
$isRoot = JFactory::getUser()->authorise('core.admin', $component);
$isDelete= $this->canDelete(2);
$model = $this->getModel();
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
                    <!-- <button type="submit" class="btn btn-primary-avv" name="filter_submit"><?php //echo JText::_('COM_CONTROLCARD_FORM_FILTER_SUBMIT'); ?></button> -->
                </div>
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




        <div class="table-div">
            <div class="thead">
                <div class="border-right width-1">
                    s fs df
                </div>
                <div class="border-right width-30">
                    111sdfsdf
                </div>
                <div class="border-right width-20">
                    111sdfsdf
                </div>
            </div> <!-- <div class="thead"> -->
            <div class="tbody">

            </div> <!-- <div class="tbody"> -->
        </div> <!-- <div class="table-div"> -->
        <div class="clear"></div>


        <table class="persons adminlist">
            <thead> <!-- Заголовок таблицы-->
            <tr>
                <th width="1%" class="<?php echo $visibleCheck; ?>nowrap center checkmark-col border-right">
                    <input type="checkbox" name="checkall-toggle" value="" class="hasTooltip" title="" onclick="Joomla.checkAll(this)" data-original-title="Выбрать все">
                </th>
                <th width="1%" class="hide-cc border-right">ID</th>
                <th width="2%" class="hide-cc border-right"><?php echo JText::_('COM_CONTROLCARD_PERSONS_COLTITLE_NPP'); ?></th>
                <th width="10%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_NUMCARD'); ?></th>
                <th width="30%" class="hide-cc1 border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_NOTE'); ?></th>
                <th width="15%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERSON'); ?></th>
                <th width="1%" class="hide-cc1 border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PERFORMED'); ?></th>
                <?php if ($this->params->get('show_login_list')) : ?>
                    <th width="15%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_LOGIN'); ?></th>
                <?php endif; ?>
                <?php if ($this->params->get('show_email_list')) : ?>
                    <th width="20%" class="border-right"><?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_EMAIL'); ?></th>
                <?php endif; ?>
                <th width="15%" class="nowrap center checkmark-col"><?php echo JText::_('COM_CONTROLCARD_PERSONS_DISMISS_CAPTION'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php
                $ic=0;
                foreach ($this->items as $item) :
                    $itemEdit = $this->canEdit(0, $item);
            ?>
                <tr>
                    <td class="<?php echo $visibleCheck; ?>nowrap center border-right">
                        <input type="checkbox" id="cb<?php echo $ic; ?>" name="eid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);"/>
                    </td>
                    <td class="hide-cc txt-right border-right"><?php echo $item->id; ?></td>
                    <td class="hide-cc txt-right border-right"><?php echo $ic; ?></td>
                    <td class="txt-right border-right"><?php echo $item->num_controlcard; ?></td>
                    <td class="border-right"><?php echo $item->note; ?></td>
                    <?php
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
                        $strTmp = JText::_('COM_CONTROLCARD_CARDS_COLTIP_ABOUT_USER') . '::' . $strTmp;
                    ?>
                    <td class="border-right hasTip" title="<?php echo $strTmp; ?>"><?php echo $item->fio; ?></td>
                    <td class="nowrap center border-right">
	                    <?php if ($itemEdit) :
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
                    <?php if ($this->params->get('show_login_list')) : ?>
                        <td class="border-right"><?php echo $item->login; ?></td>
                    <?php endif; ?>
	                <?php if ($this->params->get('show_email_list')) : ?>
                        <td class="border-right"><?php echo $item->email; ?></td>
	                <?php endif; ?>

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
