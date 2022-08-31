<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

class ControlcardViewCard extends JViewLegacy
{
	protected   $access=null;
	protected   $form = null;
	protected   $item = null;
	protected   $state = null;
	protected   $personForCurrentUser = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		//$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->access = ControlcardHelper::getActions();
		/*
		Av8vLog::logMsg(
			['msg'=>
				 [
					 'ControlcardViewCard->__construct ===============================================================================',
					 'config: ', $config,
					 'this->_viewOnly: ' . $this->_viewOnly
				 ],'category'],
			defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
		*/
	}

	public function display($tpl = null)
	{
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}

		$app        = JFactory::getApplication();

		$inp        = $app->input;
		$this->id   = (int) $inp->get('id', 0);

		$model      = $this->getModel();
		$this->item = $model->getItem($this->id, false); //TODO может 2-й параметр нужен true

		$this->personForCurrentUser = ControlcardHelper::getPersonForUser();

			// разрешено редактировать эту запись (карточку)
		if ($model->canEdit(0, $this->item))
		{
			$copy_id = $app->getUserState('com_controlcard.card.copy_id');
			$app->setUserState('com_controlcard.card.copy_id', 0);
				// Get the view data.
			$this->form  = $this->get('Form');
			$this->state = $this->get('State');
			if ($copy_id) {
				$dataCopy = ControlcardHelper::getDataCard($copy_id);
				$dataCopy->id = null;
				$dataCopy->num_controlcard = null;
				$this->form->bind($dataCopy);
			}
				// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				JError::raiseError(500, implode('<br />', $errors));
				return false;
			}
				// непосредственно вывод
			parent::display($tpl);

		} else { // запрещено редактировать эту запись (карточку)
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_CARD'), 'warning');
		} // if ($model->canEdit(0, $this->item))
	}

}