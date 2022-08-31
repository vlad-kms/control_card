<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

# Import JMailHelper
jimport('joomla.mail.helper');

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerCard extends JControllerForm
{
	protected $default_view = 'card';
	//protected $_viewOnly = false;

	public function __construct(array $config = array())
	{
		parent::__construct($config);
		//$this->_viewOnly = $config['viewOnly'] ? true : false;
		AvvLog::logMsg(
			['msg'=>
				 [
					 'ControlcardControllerCard->__construct ==============================================================================='
					 , '(---------------------------------------------------------- parameters'
					 , 'config:', $config
					 , '-----------------------------------------------------------)'
					 //, 'this:', $this
				 ],'category'],
			defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
		$this->registerTask('send2email', 'print');
		$this->registerTask('copyAdd', 'add');
	}

	/*******************************************
	 *
	 *******************************************/
	public function add()
	{
		AvvLog::logMsg(['msg' =>
			                [
				                'ControlcardControllerCard->add ===============================================================================',
				                '$_REQUEST', $_REQUEST
			                ],
			'category'], defined('AVV_DEBUG'), null, 'controlcard.log'
		);
		$res = parent::add();
		$app = JFactory::getApplication();
		if ($this->task == 'copyAdd')
		{
			$array_id = $app->input->get('eid', array(), 'array');
			if ( count($array_id) && ( ($id = $array_id[0]) > 0) ) {
				// есть отмеченные на форме карточки и у 1-й из них id > 0
				$app->setUserState("$this->option.$this->context.copy_id", $id);
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_CARD_BAD_ID'), 'warning');
				$url = $this->getRedirectToListAppend();
			}
		} else {
			$app->setUserState("$this->option.$this->context.copy_id", 0);
		}
		$this->redirect();
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		if ( count($data) == 0) {
			$item=null;
		} else {
			$item = ArrayHelper::toObject($data, '\JObject');
		}
		return $this->getModel()->canEdit(0, $item);
	}

	protected function allowDisplay($data = array(), $key = 'id')
	{
		if ( count($data) == 0) {
			$item=null;
		} else {
			$item = ArrayHelper::toObject($data, '\JObject');
		}
		return $this->getModel()->canEdit(0, $item);
	}

	/*********************************************************
	 *  Выйти без сохранения даннх в БД и закрыть форму
	 *********************************************************/
	public function cancel($key = null)
	{
		/*
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlcardControllerCard->cancel (?task="person.cancel") =========================================================================',
					, '(---------------------------------------------------------- parameters'
					, 'key: '.$key
					, '-----------------------------------------------------------)'
				],
			'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
		*/
		parent::cancel($key);
	}

	/*********************************************************
	 * Сохранение измененой (новой) записи в БД
	 *********************************************************/
	public function save($key = null, $urlVar = null)
	{
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlcardControllerCard->save (?task="person.save") ========================================================================='
					, '(---------------------------------------------------------- parameters'
					, 'key: ' . $key
					, 'urlVar: ' . $urlVar
					, '-----------------------------------------------------------)'
					, 'this->input: ==============='
					, $this->input
				],
			'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
			// проверить права
		$this->checkToken();

		$dataForm = $this->input->get('jform', array(), 'array');

		if (!$this->allowEdit($dataForm) )
		{
			\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
			return false;
		}


		$this->input->set('id', $dataForm['id']);

			// запись
		parent::save($key, 'id');
	}

	public function print($id=null) {
		// Check for request forgeries
		$this->checkToken();

		if (ControlcardHelper::getActions()->get('core.card.access') ) {
			if (empty($id)) {
				// взять id карточки из $_INPUT
				$id = $this->input->get('id', null);
			}
			if (empty($id)) {
				$dataForm = $this->input->get('jform', null, 'array');
				if (!empty($dataForm) && is_array($dataForm))
				{
					$id = $dataForm['id'];
					//$idq = $dataForm['idq'];
				}
			}
			if (empty($id)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_NOT_CARD'), 'warning');
				return false;
			}
			$item = $this->getModel()->getItem($id);
			if (empty($item)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_NOT_CARD'), 'warning');
				return false;
			}
			if ( $this->allowDisplay(ArrayHelper::fromObject($item)) ){
				$params = ControlcardHelper::getParams();
				$ret = ControlcardHelper::getPrintPDF(null, $params);
				if ($ret) {
					// собственно сформировать PDF файл отправить по E-Mail или ывовести на печать
					$task = $this->input->get('task');
					if ($task == 'print')
					{
						$dst = 'I';
						//$dst = 'D';
						$dst = 'F';
						$nm = 'card-' . (string)$item->id . '-I.pdf';
						//$ret->Output('I', );
						$result = $ret->Output($dst, JPATH_SITE . '/tmp/' . $nm);
						JFactory::getApplication()->setUserState('global.cardFilePDF', '/tmp/' . $nm);
						$res = true;
					} elseif ($task == 'send2email') {
						$dst = 'F';
						//$dst = 'D';
						//$dst = 'I';
						//$ret->Output('I', JPATH_SITE . '/tmp/card' . (string)$item->id . '.pdf', true);
						//$ret->Output('F', JPATH_SITE . '/tmp/card' . (string)$item->id . '.pdf', true);
						$nm = JPATH_SITE . '/tmp/' . 'card-' . (string)$item->id . '-em.pdf';
						$result = $ret->Output($dst, $nm);
						$res = ControlcardHelper::sendCard2Email($item, $nm, false);
						echo $res;
						//return true;
					}
					//$ret->Output($dst, JPATH_SITE . '/tmp/' . $nm, true);
				}
				//JRoute::_($this->input->get('refer_url', '', 'string'));
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_CARD_PERMISSION_NOT_DISPLAY'), 'warning');
				$res = false;
			}
		}
		//echo $result;
		//$this->redirect();
		$url = $this->input->getBase64('returnprint');
		//$this->setRedirect(JRoute::_(base64_decode($$url), false));
		$this->setRedirect(base64_decode($url));
		$this->redirect();
		return $res;
	}

}