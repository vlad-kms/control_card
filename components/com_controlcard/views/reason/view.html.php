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
class ControlcardViewReason extends JViewLegacy
{
/*
core.reasons.access
core.reasons..display
core.reasons.edit
core.reasons.delete
*/
	protected   $access=null;
	protected   $form = null;
	protected   $item = null;
	protected   $state = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
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
		// проверить есть ли доступ И (просмотр карточек ИЛИ просмотр своих карточек)
		if ( $this->canEdit() )
		{
			$app        = JFactory::getApplication();
			$inp        = $app->input;
			$this->id   = (int) $inp->get('id', 0);

			$model      = $this->getModel();
			$this->item = $model->getItem($this->id);

			$this->form  = $this->get('Form');
			$this->state = $this->get('State');
			//$this->form        = $model->getForm();
			//$this->state        = $model->getState();

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
/*
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
*/
}
