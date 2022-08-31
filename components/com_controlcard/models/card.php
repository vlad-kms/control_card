<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

//use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');


class ControlCardModelCard extends JModelForm {

	protected   $access= null;
	protected   $_context   = 'com_controlcard.card';

	public function canEdit($onlyOwner=0, $item=null)
	{
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}
		if ($item) {
			$person_id = $item->person_id;
		} else {
			$person_id = null;
		}
		$res = false;
		if ($this->access->get('core.card.access')) {
			$user = JFactory::getUser();
			// вернуть сотрудника связанного с пользователем
			$ownerPerson = ControlcardHelper::getPersonForUser();

			if ($onlyOwner == 1) { // редактирование только своих карточек
				$res = $this->access->get('core.card.access') && $this->access->get('core.card.edit.own');
			} elseif ($onlyOwner == 2) { // редактирование всех карточек {
				$res = $this->access->get('core.card.access') && $this->access->get('core.card.edit');
			} else { // редактирование любых карточек
				if ($this->access->get('core.card.edit')){
					$res= true;
				} elseif ($this->access->get('core.card.edit.own')) {
					$res = !empty($person_id) && ($person_id == $ownerPerson);
				}
			}
		}
		return $res;
	}

	public function canDisplay($onlyOwner=0, $item=null)
	{
		if (!$this->access){
			$this->access = ControlcardHelper::getActions();
		}
		if ($item) {
			$person_id = $item->person_id;
		} else {
			$person_id = null;
		}
		$res = false;
		if ($this->access->get('core.card.access')) {
			//$user = JFactory::getUser();
			// вернуть сотрудника связанного с пользователем
			$ownerPerson = ControlcardHelper::getPersonForUser();

			if ($onlyOwner == 1) { // редактирование только своих карточек
				$res = $this->access->get('core.card.display.own') &&
					!empty($person_id) && ($person_id == $ownerPerson);
			} elseif ($onlyOwner == 2) { // редактирование всех карточек {
				$res = $this->access->get('core.card.display');
			} else { // редактирование любых карточек
				if ($this->access->get('core.card.display')){
					$res= true;
				} elseif ($this->access->get('core.card.display.own')) {
					$res = !empty($person_id) && ($person_id == $ownerPerson);
				}
			}
		}
		return $res;

	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_controlcard.card', 'card', array('control'=> 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	public function getItem($pk=null, $addExtFields=true) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
		// Convert to the \JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = ArrayHelper::toObject($properties, '\JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new Registry($item->params);
			$item->params = $registry->toArray();
		}
		// заполнить дату следующего исполнения
		if ($addExtFields) {
			ControlcardHelper::fillDateNextPerformed($item);
		}

		return $item;
	}

	public function getTable($name = 'Cards', $prefix = 'Table', $options = array()) {
		// параметр $name проверитть  значение по умолчанию
		return parent::getTable($name, $prefix, $options);
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		//$data = $app->getUserState('com_controlcard.person.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->_context, $data);

		return $data;
	}

	protected function preprocessData($context, &$data, $group = 'content')
	{
		parent::preprocessData($context, $data, $group);
		if (empty($data->user_id)) {
			$data->user_id = null;
		}
	}

	protected function populateState()
	{
		$table = $this->getTable();
		$key = $table->getKeyName();

		// Get the pk of the record from the request.
		$pk = \JFactory::getApplication()->input->getInt($key);
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$value = \JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

	public function save($data)
	{
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlCardModelCard->save ========================================================================='
					, '(---------------------------------------------------------- parameters'
					, 'data: ' . $data
					, '-----------------------------------------------------------)'
					, 'this->input: ==========='
					, $this->input
					, '$_REQUEST: ============='
					, $_REQUEST
				],
			'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
		if (! $this->canEdit()) {
			\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
			return false;
		}

		// validate $data
		//
		if (!isset($data['performed'])) {
			$data['performed']=0;
		}
		$table      = $this->getTable();
		$context    = $this->option . '.' . $this->name;
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		/*
		if (!isset($data['dismiss'])) {
			$data['dismiss']=0;
		}
		*/
		// Allow an exception to be thrown.
		try
		{
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}
			if (!$this->canEdit(0, $table)){
				$this->setError($table->getError());
				return false;
			}
			// Bind the data.
			if (!$table->bind($data, array('tags')))
			{
				$this->setError($table->getError());
				return false;
			}
			/*
			 *  в данной модели это вообще не требуется
				// Prepare the row for saving
			$this->prepareTable($table);
			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}
			*/
			/**/
			// Сохранение данных.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			if ( empty($table->num_controlcard) || ($table->num_controlcard == 0) ) {
				$table->num_controlcard = $table->id;
				$table->store();
			}
			/**/
			// Clean the cache.
			$this->cleanCache();
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		if (isset($table->$key))
		{
			$this->setState($this->getName() . '.id', $table->$key);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

	/****************************************************************
	 * Проверка введенных в форме данных
	 ****************************************************************/
	public function validate($form, $data, $group = null)
	{
		$validatedData = parent::validate($form, $data, $group);
		// не прошла проверка
		if ($validatedData === false)
		{
			return false;
		}
		try
		{
			// проверить поле performed_ext_int
			if ( !isset($validatedData['performed_ext_int']) || $validatedData['performed_ext_int'] < 1 ) {
				$validatedData['performed_ext_int']=1;
			}
			// проверить поле performed
			if (!isset($validatedData['performed'])) {
				$validatedData['performed']=0;
			}
			// проверить поле performed_date
			if (ControlcardHelper::validateDate($validatedData['performed_date'])) {
				$validatedData['performed_date'] = DateTime::createFromFormat('d-m-Y', $validatedData['performed_date'])->format('Y-m-d');
			} else {
				$validatedData['performed_date'] = date('Y-m-d', time()) . ' h:i';
			}
			// проверить поле person_id_create
			if ( empty($validatedData['person_id_create']) ) {
				$validatedData['person_id_create'] = ControlcardHelper::getPersonForUser(JFactory::getUser()->id);
			}
			// проверить поле date_document
			if (ControlcardHelper::validateDate($validatedData['date_document'])) {
				$validatedData['date_document'] = DateTime::createFromFormat('d-m-Y', $validatedData['date_document'])->format('Y-m-d');
			} else {
				$validatedData['date_document'] = date('Y-m-d', time()) . ' h:i';
			}
			// проверить поле date_document
			if (ControlcardHelper::validateDate($validatedData['date_document_int'])) {
				$validatedData['date_document_int'] = DateTime::createFromFormat('d-m-Y', $validatedData['date_document_int'])->format('Y-m-d');
			} else {
				$validatedData['date_document_int'] = date('Y-m-d', time()) . ' h:i';
			}
			//return false; // ДЛЯ проверки ОТКАЗ
		}
		catch (\Exception $e)
		{
			$this->setError(Text::_('ControllerCard->validate Exception.Message :::' . $e->getMessage()));
			return false;
		}
		return $validatedData;
	}
}

