<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
**/

defined('_JEXEC') or die;

//use Joomla\CMS\MVC\Model\BaseDatabaseModel;
//use Joomla\CMS\MVC\Model\FormModel;
//use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;


JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class ControlCardModelReason extends JModelForm {

	protected   $access= null;
	protected   $_context   = 'com_controlcard.reason';

	public function canDelete($item = null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!$this->access)
		{
			$this->access = ControlcardHelper::getActions();
		}

		return $this->access->get('core.reasons.access')
			&& $res = $this->access->get('core.reasons.delete');
	}

	public function canDisplay($item = null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!$this->access)
		{
			$this->access = ControlcardHelper::getActions();
		}

		return $this->access->get('core.reasons.access')
			&& $res = $this->access->get('core.reasons.display');
	}

	public function canEdit($onlyOwner = 0, $item = null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!$this->access)
		{
			$this->access = ControlcardHelper::getActions();
		}

		return $this->access->get('core.reasons.access')
			&& $res = $this->access->get('core.reasons.edit');
	}

	public function getTable($name = 'Reasons', $prefix = 'Table', $options = array()) {
		// параметр $name проверитть  значение по умолчанию
		return parent::getTable($name, $prefix, $options);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_controlcard.reason', 'reason', array('control'=> 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	public function getItem($pk=null) {
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
		// сохранить данные в сессии
		JFactory::getApplication()->setUserState($this->_context . '.data', $properties);
		$item = ArrayHelper::toObject($properties, '\JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new Registry($item->params);
			$item->params = $registry->toArray();
		}
		return $item;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		//$data = $app->getUserState('com_controlcard.person.data', array());
		$data = JFactory::getApplication()->getUserState($this->_context . '.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->_context, $data);

		return $data;
	}

	public function save($data)
	{
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlCardModelReason->save ========================================================================='
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
		$table      = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		//$context    = $this->option . '.' . $this->name;

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

}