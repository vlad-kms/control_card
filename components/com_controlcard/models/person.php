<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11.01.2019
 * Time: 19:17
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
//use Joomla\Utilities\IpHelper;

class ControlCardModelPerson extends JModelForm
{
    protected   $_context   = 'com_controlcard.person';
    protected   $_extension = 'com_controlcard';
    //protected   $_item      = null;

	protected function canDelete($record=null)
	{
		return \JFactory::getUser()->authorise('core.persons.delete', $this->option);
	}

	protected function canEdit($record=null)
	{
		return \JFactory::getUser()->authorise('core.persons.edit', $this->option);
	}

	public function getForm($data = array(), $loadData = true) {
		//$loadData = true;
		//JForm::addFieldPath(JPATH_COMPONENT . '/com_controlcard/models/fields');
        $form = $this->loadForm('com_controlcard.person', 'person', array('control'=> 'jform', 'load_data' => $loadData));
		//$form = $this->loadForm('com_controlcard.person', 'person', array('load_data' => $loadData));
		#$form = $this->loadForm('com_users.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        return $form;
    }
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
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

	public function getTable($name = 'Persons', $prefix = 'Table', $options = array()) {
        return parent::getTable('Persons', $prefix, $options);
    }

    public function getItem($pk=null) {
	    $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
	    #if ($this->_item === null)
	    #{
		#    $this->_item = array();
	    #}
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

	    return $item;
    }

	protected function preprocessData($context, &$data, $group = 'content')
	{
		parent::preprocessData($context, $data, $group);
		if (empty($data->user_id)) {
			$data->user_id = null;
		}
	}

	public function validate($form, $data, $group = null)
	{
		$res = parent::validate($form, $data, $group);
		if (!$res) {
			return $res;
		}
		if (array_key_exists('user_id', $data) && empty($data['user_id'])) {
			$data['user_id'] = null;
			//unset($data['user_id']);
		}
		return $data;
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

	public function isUserAlreadyLink($user_id, $person_id, &$alreadyData=null ) {
		if ( empty($user_id) ) {
			return false;
		}
		$queryTxt = 'SELECT  a.id, a.fio, a.user_id FROM #__controlcard_persons AS a WHERE a.id<>"' . (string)$person_id .'" AND a.user_id="' . (string)$user_id . '"';
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->setQuery($queryTxt);

		$db->setQuery($query);
		$data = $db->loadObject();

		/*
		SELECT  `a`.* FROM `#__controlcard_persons` AS `a` WHERE `a`.`id`<>$person_id AND `a`.`user_id`=$user_id;
		$query = '
    	    SELECT
    	    FROM
                1-РАБОЧИЙ ЗАПРОС query="SELECT `a`.`id`, `a`.`username`, CONCAT(`a`.`username` , \' \' , `a`.`name`) AS `name` FROM `#__users` AS `a` WHERE `a`.`block`=0 AND NOT EXISTS(SELECT  1 FROM #__controlcard_persons m WHERE   a.id = m.user_id)"
    	    
    	';
		if
		*/
		$result = !empty($data);
		if ($result && !($alreadyData === null))
		{
			$alreadyData = $data;
		}
		return $result;
	}

	public function save($data)
	{
		AvvLog::logMsg([
			'msg'=>
				[
					'ControlCardModelPerson->save ========================================================================='
					, 'this->input:', $this->input
					, '$_REQUEST', $_REQUEST
					, 'data: ' . $data
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
		$context    = $this->option . '.' . $this->name;
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		if (!isset($data['dismiss'])) {
			$data['dismiss']=0;
		}

		// Allow an exception to be thrown.
		try
		{
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}
			if (!$this->canEdit($table)){
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
				/* Если значение в форме user_id = "", тогда в таблице установитьв NULL*/
			if (is_null($data['user_id'])) {$table->user_id = null; }
				// Сохранение данных.
			if (!$table->store(true))
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
