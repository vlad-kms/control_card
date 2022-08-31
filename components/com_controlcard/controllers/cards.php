<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

#use Joomla\CMS\MVC\Controller\BaseController;
#use ControlCardModelCard;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerCards extends JControllerForm
{
	protected $default_view = "cards";

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('deleteList', 'delete');
	}

	protected function allowAdd($data = array())
	{
		return $this->allowEdit($data);
	}

	protected function allowDelete($data = array(), $key = 'id')
	{
		if ( count($data) == 0)
		{
			$item=null;
		}
		else
		{
			$item = ArrayHelper::toObject($data, '\JObject');
		}
		$model = $this->getModel();
		if ($model)
		{
			return $model->canDelete(0, $item);
		}
		else
		{
			return (\JFactory::getUser()->authorise('core.admin'))
			|| \JFactory::getUser()->authorise('core.card.delete', $this->option);
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		if ( count($data) == 0) {
			$item=null;
		} else {
			$item = ArrayHelper::toObject($data, '\JObject');
		}
		$model = $this->getModel();
		if ($model) {
			return $model->canEdit(0, $item);
		}
		else {
			return (\JFactory::getUser()->authorise('core.admin'))
				|| (\JFactory::getUser()->authorise('core.card.edit', $this->option));
		}
	}

	public function perform() {
		AvvLog::logMsg(['msg' =>
			                [
				                'ControlcardControllerCards->perform ===============================================================================',
				                '$_REQUEST', $_REQUEST
			                ],
			'category'], defined('AVV_DEBUG'), null, 'controlcard.log'
		);
		$this->checkToken();

		if ( $this->allowEdit() ) {
			// установить(снять) флаг Исполнен
			$model = $this->getModel('Cards');
			$ids = $this->input->get('eid', array(), 'array');
			if ($model) {
				$result = $model->perform($ids);
			}
			/*
			$id = (int)$_REQUEST['eid'][0];
			// получить флаг Уволен данной записи работы или принять на работу: =0 принять на работу; =1 уволить с работы
			$table = $model->getTable('Persons');
			if ($table->load($id)) {
				$value = $table->dismiss ? 0: 1;
			}
			$d = $model->dismiss($id, $value);
			*/
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_CARD'), 'warning');
		}
		$this->setRedirect(\JRoute::_('index.php?option='. $this->option .'&view=cards', false));
	}

	/*****************************************************
	 *
	 *
	 * @since version
	 * @throws Exception
	 *****************************************************/
	public function printList() {
		$this->checkToken();

		if (ControlcardHelper::getActions()->get('core.card.access') ) {

			$app = JFactory::getApplication();
			$params = ControlcardHelper::getParams();
			$ret = ControlcardHelper::pdfPrintListCards($app->input->get('eid', array(), 'array'), $params);

			$fn = '/tmp/' . 'cards-' . (string) JFactory::getUser()->id . '.pdf';
			$app->setUserState('global.cardFilePDF', $fn);
			$result = $ret->Output('F', JPATH_SITE . $fn);
			/*
			$model = $this->getModel();
					$data  = $this->input->post->get('jform', array(), 'array');
					$checkin = property_exists($table, $table->getColumnAlias('checked_out'));
					$task = $this->getTask();
			*/

		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_CARD'), 'warning');
		}
		// Redirect
		$url = 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend();
		// Check if there is a return value
		$return = $this->input->get('return', null, 'base64');
		if (!is_null($return) && \JUri::isInternal(base64_decode($return)))
		{
			$url = base64_decode($return);
		}
		// Redirect to the list screen.
		$this->setRedirect(\JRoute::_($url, false));
	}

	public function copyList() {
		$this->checkToken();
		if ( $this->allowAdd() ) {
			// перейти к редактированию карточки на основании
			$app = JFactory::getApplication();
			$params = ControlcardHelper::getParams();
			$array_id = $app->input->get('eid', array(), 'array');
			if ( count($array_id) && ( ($id = $array_id[0]) > 0) ) {
				// есть отмеченные на форме карточки и у 1-й из них id > 0
				$modelCard = $this->getmodel('Card');
				$item = $modelCard->getItem($id);
				$url = $this->getRedirectToItemAppend();
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_CARD_BAD_ID'), 'warning');
				$url = $this->getRedirectToListAppend();
			}
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_CARD'), 'warning');
			$url = $this->getRedirectToListAppend();
		}

		if (empty($url)) {
			$url = 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend();
			// Check if there is a return value
			$return = $this->input->get('return', null, 'base64');
			if (!is_null($return) && \JUri::isInternal(base64_decode($return)))
			{
				$url = base64_decode($return);
			}
		}
		$this->setRedirect(\JRoute::_($url, false));
	}

	/*********************************************************
	 *  Удалить карточку из БД
	 *
	 *********************************************************/
	public function delete()
	{
		AvvLog::logMsg(['msg' =>
			                [
				                'ControlcardControllerCards->delete ===============================================================================',
				                '$_REQUEST', $_REQUEST
			                ],
			'category'], defined('AVV_DEBUG'), null, 'controlcard.log'
		);
		$str_route = 'index.php?option=' . $this->option . '&view=' . $this->view_list;
		// Check for request forgeries
		$this->checkToken();
		// Get items to remove from the request.
		$cid = $this->input->get('eid', array(), 'array');
		$not_err=true;
		if (!is_array($cid) || count($cid) < 1)
		{
			\JLog::add(\JText::_($this->text_prefix . '_NO_CARD_SELECTED'), \JLog::WARNING, 'jerror');
			JFactory::getApplication()->enqueueMessage(JText::_($this->text_prefix . '_NO_CARD_SELECTED'), 'warning');
			$not_err = !$not_err;
			$this->setRedirect(\JRoute::_($str_route, false));
			return $not_err;
		}

		if ( $not_err && $this->allowDelete($cid)){
			// Get the model.
			$model = $this->getModel();
			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(\JText::sprintf($this->text_prefix . '_ITEMS_DELETED', 'карточек', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
			// Invoke the postDelete method to allow for the child class to access the model.
			//$this->postDeleteHook($model, $cid);
		}
		else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_DELETE_CARD'), 'warning');
		}

		$this->setRedirect(\JRoute::_($str_route, false));
	}

}
