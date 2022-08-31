<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

#use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Controlcard view.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardViewReasons extends JViewLegacy
{
/*
core.reasons.access
core.reasons..display
core.reasons.edit
core.reasons.delete
*/
	protected   $access     =null;
    protected   $state      =null;
	protected   $items      =null;
	protected   $toolbar    =null;

	public      $activeFilters = null;
	public      $filterForm =null;

	public      $params     =null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		//$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->access = ControlcardHelper::getActions();
	}

	public function canDelete($item=null)
	{
		return $this->getModel()->canDelete($item);
	}

	public function canDisplay($item=null)
	{
		return $this->getModel()->canDisplay($item);
	}

	public function canEdit($item=null)
	{
		return $this->getModel()->canEdit($item);
	}

	public function display($tpl=null)
	{
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		// проверить есть ли доступ И (просмотр карточек ИЛИ просмотр своих карточек)
		if ($this->canDisplay())
		{
			$model = $this->getModel();
			// вернуть параметры компонента и меню
			$paramsModel = $model->params;
			if ( isset($paramsModel) ) {
				$this->params = $paramsModel;
			} else {
				//$this->params = ControlcardHelper::getParams($this->getModel()->get('option'));
				$this->params = ControlcardHelper::getParams();
			}
			$this->state        = $model->getState();
			$this->items        = $model->getItems(true);
			$this->pagination   = $this->get('Pagination');
			$this->filterForm   = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

			$this->toolbar      = $this->addToolBar();

			// Check for errors.
			if (count($errors = $this->get('Errors'))) {
				JError::raiseError(500, implode("\n", $errors));
				return false;
			}
			parent::display($tpl);
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_REASON_PERMISSION_NOT_DISPLAY'), 'warning');
		}
	}

	public function addToolBar ()
	{
		//$this->canDo = ControlcardHelper::getActions();
		//JToolbarHelper::title(JText::_('ToolBar PERSONS'));
		if ( $this->canEdit() )
		{
			JToolBarHelper::addNew('reason.add', JText::_('COM_CONTROLCARD_PERSONS_ADD'));
		}
		//if (JFactory::getUser()->authorise('core.admin')) {
		if ($this->canDelete())
		{
			JToolBarHelper::deleteList('','reasons.deleteList', JText::_('COM_CONTROLCARD_PERSONS_DELETE'));
		}

		return JToolbar::getInstance('toolbar')->render();
	}

}
