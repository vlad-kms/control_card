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
class ControlcardViewCards extends JViewLegacy
{
/*
core.card.access
core.card.display
core.card.display.own
core.card.edit
core.card.edit.own
core.card.delete
core.card.delete.own
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

	public function canDelete($onlyOwner=0, $item=null)
	{
		$model = $this->getModel();
		return $model->canDelete($onlyOwner, $item);
	}

	public function canDisplay($onlyOwner=0, $item=null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}
		if ($onlyOwner == 1) { // только просмотр своих карточек
			$res = $this->access->get('core.card.access') && $this->access->get('core.card.display.own');
		} elseif ($onlyOwner == 2) { // просмотр всех карточек {
			$res = $this->access->get('core.card.access') && $this->access->get('core.card.display');
		} else { // просмотр любых карточек
			$res = $this->access->get('core.card.access') &&
				($this->access->get('core.card.display') || $this->access->get('core.card.display.own'));
		}
		return $res;
	}

	public function canEdit($onlyOwner=0, $item=null)
	{
		$model = $this->getModel();
		return $model->canEdit($onlyOwner, $item);
	}

	public function display($tpl=null)
	{
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if (!ControlcardHelper::getPersonForUser($user->id)
				&& !$user->authorise('core.admin')
			)
		{
			$app->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_USER_NOTLINK_PERSON', $user->username), 'warning');
			return;
		}


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
			//$this->items        = $model->getItems(true);
			//$this->items        = $model->getItems(false);
			$this->items        = $model->getItems($this->params->get('extfields_use', true));
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
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_CARD_PERMISSION_NOT_DISPLAY'), 'warning');
		}
	}

	public function addToolBar ()
	{
		//$this->canDo = ControlcardHelper::getActions();
		//JToolbarHelper::title(JText::_('ToolBar PERSONS'));
		$person2user = ControlcardHelper::getPersonForUser();
		if ( $person2user && $this->canEdit(0) )
		{
			JToolBarHelper::addNew('card.add', JText::_('COM_CONTROLCARD_PERSONS_ADD'));
			JToolBarHelper::custom('card.copyAdd','', '', JText::_('COM_CONTROLCARD_CARDS_ACTION_LABEL'), true);
		}
		//if (JFactory::getUser()->authorise('core.admin')) {
		if ($this->canDelete())
		{
			JToolBarHelper::deleteList('','cards.deleteList', JText::_('COM_CONTROLCARD_PERSONS_DELETE'));
		}
		JToolBarHelper::custom('cards.printList','', '', JText::_('JGLOBAL_PRINT'), true);

		return JToolbar::getInstance('toolbar')->render();
	}

}
