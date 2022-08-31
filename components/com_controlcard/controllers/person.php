<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11.01.2019
 * Time: 19:32
 */
defined('_JEXEC') or die;

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerPerson extends JControllerForm
{
    protected $default_view = 'person';
    protected $_viewOnly = false;

    public function __construct(array $config = array())
    {
	    parent::__construct($config);
	    $this->_viewOnly = $config['viewOnly'] ? true : false;
	    AvvLog::logMsg(
		    ['msg'=>
			     [
				    'ControlcardControllerPerson->__construct ==============================================================================='
				    , 'config:', $config
	                , 'this:', $this
			     ],'category'],
		    defined('AVV_DEBUG'), NULL, 'controlcard.log'
	    );
    }

	/*********************************************************
	 *  Проверить разрешение на редактирование ответственных лиц (сотрудников)
	 *********************************************************/
	protected function allowEdit($data = array(), $key = 'id')
	{
		return \JFactory::getUser()->authorise('core.persons.edit', $this->option);
		//return ControlcardHelper::getActions()->get('core.persons.edit');
	}

	/*********************************************************
	 *  Добавить новое ответственное лицо (сотрудника)
	 *********************************************************/
	public function add()
	{
		// Если используется parent:add , тогда эта строка не нужна
		//$context = "$this->option.edit.$this->context";
		AvvLog::logMsg(['msg' =>
			                [
				                'ControlcardControllerPersons->add ==============================================================================='
				                ,'$_REQUEST:', $_REQUEST
				                //, 'JFactory::getApplication()->input:', JFactory::getApplication()->input
				                //,'context = ' . $context
			                ],
			'category'], defined('AVV_DEBUG'), null, 'controlcard.log');

		$this->checkToken();
		#
		#if (!$this->allowAdd()) {
		#	$this->setError(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
		#	$this->setMessage($this->getError(), 'error');
		#
		#	$this->setRedirect(
		#		\JRoute::_(
		#			'index.php?option=' . $this->option . '&view=' . $this->view_list
		#			. $this->getRedirectToListAppend(), false
		#		)
		#	);
		#
		#	return false;
		#}
		parent::add();

		// Если используется parent:add , тогда эта строка не нужна
		//\JFactory::getApplication()->setUserState($context . '.data', null);
		$this->redirect();
	}

	/*********************************************************
	 *  Выйти без сохранения даннх в БД и закрыть форму
	 *********************************************************/
	public function cancel($key = null)
	{
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlcardControllerPerson->cancel (?task="person.cancel") =========================================================================',
				    'key: '.$key
			    ],
			'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);

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
					'ControlcardControllerPerson->save (?task="person.save") ========================================================================='
					, 'key: ' . $key
					, 'urlVar: ' . $urlVar
					, 'this->input:', $this->input
				],
			'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);

		$this->checkToken();

		$dataForm = $this->input->get('jform', array(), 'array');

		if ( !$this->allowEdit($dataForm) )
		{
			\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
			return false;
		}

		//$user_id = $this->input->get('jform.user_id', null);

		// проверить используется ли уже $__users.id в таблице $__controlcard_persons.user_id
		//ошибка если да
		$user_id = $dataForm['user_id'];
		$person_id = (int) $dataForm['id'];
		$alreadyData=array();
		$alreadyUse =  $this->getModel()->isUserAlreadyLink($user_id, $person_id, $alreadyData);
		if ($alreadyUse) {
			#JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_USER_ALREADY_USE'), 'warning');
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_USER_ALREADY_USE', $alreadyData->user_id, $alreadyData->fio), 'warning');
			return false;
		}
		$this->input->set('id', $dataForm['id']);
		if (empty($user_id)) {
			$dataForm['user_id']=null;
		}
		parent::save($key, 'id');
	}

} ### class ControlcardControllerPerson extends JControllerForm
