<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class ControlcardModelCards extends JModelList
{
	protected   $default_order = 'a.performed_date DESC';
	//protected   $default_filterPerformed = 0;
	public      $params=null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'num_controlcard', 'a.num_controlcard',
				'performed', 'a.performed',
				'performed_date', 'a.performed_date',
				'performed_type', 'a.performed_type',
			);
		}
		parent::__construct($config);
	}

	public function canDelete($onlyOwner=0, $item=null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!isset($this->access)){
			$this->access = ControlcardHelper::getActions();
		}
		/*
		if (isset($item) && isset($item->person_id)) {
			$person_id = $item->person_id;
		} else {
			$person_id = null;
		}
		*/
		$person_id = (isset($item) && isset($item->person_id)) ? $item->person_id : null;

		$res = false;
		if ($this->access->get('core.card.access')) {
			$user = JFactory::getUser();
			// вернуть сотрудника связанного с пользователем
			$ownerPerson = ControlcardHelper::getPersonForUser();

			if ($onlyOwner == 1) { // удаление только своих карточек
				$res = $this->access->get('core.card.delete.own');
			} elseif ($onlyOwner == 2) { // удаление всех карточек {
				$res = $this->access->get('core.card.delete');
			} else { // удаление любых карточек
				if ($this->access->get('core.card.delete')){
					$res= true;
				} elseif ($this->access->get('core.card.delete.own')) {
					$res = !empty($person_id) && ($person_id == $ownerPerson);
				}
			}
		}
		return $res;
	}

	public function canEdit($onlyOwner=0, $item=null)
	{
		if (JFactory::getUser()->authorise('core.admin'))
		{
			return true;
		}
		if (!isset($this->access)){
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
				$res = $this->access->get('core.card.edit.own');
			} elseif ($onlyOwner == 2) { // редактирование всех карточек {
				$res = $this->access->get('core.card.edit');
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

	public function getTable($name = '', $prefix = 'Table', $options = array()) {
		return parent::getTable($name, $prefix, $options);
	}

	public function getItems($addExtFields=false)
	{
		$items = parent::getItems();
		if ($addExtFields) {
			foreach ($items as $item) {
				ControlcardHelper::fillDateNextPerformed($item);
			}
		}
		return $items;
	}

	protected function getListQuery()
	{
		// вернуть ID сотрудника связанного с текущим пользователем
		$ownerPerson = ControlcardHelper::getPersonForUser();
		$access = ControlcardHelper::getActions();

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('a') . '.*, '
							. $db->qn('b.fio') . ', '
							. $db->qn('b.dismiss') . ', '
							. $db->qn('b.person_post') . ', '
							. $db->qn('b.email') . ', '
							. $db->qn('u.username', 'login') . ', '
							. $db->qn('u.name') . ', '
							. $db->qn('u.email', 'emailuser')
		)
		//->from($db->quoteName($tblName) . ' AS `a`')
		->from($db->quoteName('#__controlcard_cards', 'a'))
		->join(
			'LEFT',
			$db->qn('#__controlcard_persons', 'b') . ' ON ' .
			$db->qn('b.id') . '=' . $db->qn('a.person_id')
		)
		->join(
			'LEFT',
			$db->qn('#__users', 'u') . ' ON ' .
			$db->qn('u.id') . '=' . $db->qn('b.user_id')
		);

		// формирование условий отбора
		// 1. просмотр только своих карточек
		if ( $this->params->get('show_cards_onlyowner')
			&& (
				$access->get('core.card.access') &&
				$access->get('core.card.display.own') &&
				! $access->get('core.card.display')
			)
			&& ! JFactory::getUser()->authorise('core.admin')

		)
		{
			// добавить условие только свои карточки
			if ($ownerPerson)
			{
				$query->where($db->qn('a.person_id') . '=' . $ownerPerson);
			} else {
				// вернуть ошибку пользователя. Сотрудник не связан с пользователем, т.е. ему нельза смотреть карточки
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_CARD_PERMISSION_NOT_DISPLAY'), 'warning');
				return null;
			}
		}

		// обработка фильтров
		$fltStr = $this->getState('filter.search');
		//$fltStr = 'u432';
		$filterBegin= '' ;
		if ($fltStr !== '')
		{
			$searchStr = $db->q('%' . $fltStr . '%');
			$sqlStr =
				'( (' . $db->qn('b.fio') . ' LIKE ' . $searchStr . ')'
				. ' OR (' . $db->quoteName('a.note') . ' LIKE ' . $searchStr . ')'
				. ' OR (' . $db->quoteName('a.note_big') . ' LIKE ' . $searchStr . ')'
				. ' OR (' . $db->quoteName('u.username') . ' LIKE ' . $searchStr . ') '
				. ' OR (' . $db->quoteName('b.email') . ' LIKE ' . $searchStr . ') '
				. ' OR (' . $db->quoteName('u.email') . ' LIKE ' . $searchStr . ') '
				. ' OR (' . $db->quoteName('a.num_controlcard') . ' LIKE ' . $searchStr . ') '
				. ' OR (' . $db->quoteName('b.person_post') . ' LIKE ' . $searchStr . ') '
				. ' )';
			$query->where($sqlStr);
			$filterBegin = ' AND ';
		}

		$filterBegin= '' ;
		$filterPerformed = $this->getState('filter.performed');
		if ( $filterPerformed == 1 ) {
			$query->where($filterBegin . $db->quoteName('a.performed') . '=0');
			$filterBegin = ' AND ';
			$filterBegin= '' ;
		}
		elseif ($filterPerformed == 2)
		{
			$query->where($filterBegin . $db->quoteName('a.performed') . '<>0');
			$filterBegin = ' AND ';
			$filterBegin= '' ;
		}
		$filterPerformedType = (int)$this->getState('filter.performed_type');
		if (!empty($filterPerformedType)) {
			$query->where($filterBegin . $db->quoteName('a.performed_type') . '=' . (string)$filterPerformedType);
			$filterBegin = ' AND ';
			$filterBegin= '' ;
		}

		// Сортировка
		$strOrder = $this->getState('list.fullordering', $this->default_order);
		if (empty(trim($strOrder))) {
			$strOrder = $this->default_order;
		}
		$query->order($strOrder);
		return $query;
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		//$id .= ':' . 'cards';
		return parent::getStoreId($id);
	}

	//protected function populateState($ordering = 'a.performed_date', $direction = 'desc')
	protected function populateState($ordering = null, $direction = null)
	{
		// инициализация фильтров
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));

		// Инициализация фильтров
		//$this->setState('list.fullordering', $this->getUserStateFromRequest($this->context . '.list.fullordering', 'list_fullordering', '', 'string'));
		//$this->setState('filter.show_panel', $this->getUserStateFromRequest($this->context . '.filter.show_panel', 'show_filter_panel', '', 'integer'));

		parent::populateState($ordering, $direction);

		$app = JFactory::getApplication();

		$list = $app->input->get('list', array(), 'array');

		//$this->setState('list.fullordering', $this->getUserStateFromRequest($this->context . '.list.fullordering', 'list_fullordering', '', 'string'));


		$this->params = ControlcardHelper::getParams();

			//инициализация сортировки
		//$appSort = $this->getUserStateFromRequest($this->context . '.sort.order', 'sort', '', 'string');


		// установка данных pagination
		//**********************************************************************************************
		//$app->get('list_limit') - значение из configuration.php, т.е. глобальные настройки JApplication
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		if ( isset($this->state->params) && ($this->state->params->get('pagination_limit', 0)!=-1) )
		{
			//$key = $this->get('option') . '.' . $this->getName() . '.' . 'list.limit';
			$limit = $app->getUserStateFromRequest($this->get('option') . '.' . $this->getName() . '.' .
				'list.limit', 'limit', $this->state->params->get('pagination_limit', 'uint'));
			$limit = $app->input->get('limit', $limit, 'uint');
		}
		$this->setState('list.limit', $limit);
		$value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	public function perform($pks, $value=null) {
		//$dispatcher = \JEventDispatcher::getInstance();
		$user = \JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();
			if ($table->load($pk))
			{
				if (!$this->canEdit(0, $table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					\JLog::add(\JText::sprintf('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_CARD_P', (string)$table->num_controlcard,
							(string)ControlcardHelper::getPersonForUser($user)), \JLog::WARNING, 'jerror');
					//return false;
					continue;
				}
				// If the table is checked out by another user, drop it and report to the user trying to change its state.
				if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
				{
					\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), \JLog::WARNING, 'jerror');
					// Prune items that you can't change.
					unset($pks[$i]);
					return false;
				}

				// Prune items that are already at the given state
				if ( !is_null($value)) {
					if ($table->get($table->getColumnAlias('performed'), $value) == $value)
					{
						unset($pks[$i]);
						continue;
					}

				}
			} //if ($table->load($pk))
		} //foreach ($pks as $i => $pk)

		// Check if there are items to change
		if (!count($pks))
		{
			return true;
		}

		// Attempt to change the state of the records.
		if (!$table->perform($pks, $value))
		{
			$this->setError($table->getError());
			return false;
		}
		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	public function delete($array_id) {
		if (is_array($array_id)) {
			$str_id = join(',', $array_id);
		} else {
			$str_id = (string)$array_id;
		}

		$db    = $this->getDbo();
		/*
		$query = $db->getQuery(true);
		$cond = array(
			$db->qn('id') . ' in (' . $str_id . ')'
			//$db->qn('id') . ' = ' . (int)$str_id
		);
		$query
			->delete($db->qn('#__controlcard_cards', 'a'))
			->where($db->qn('id') . ' = ' . (int)$str_id);
		*/
		$queryStr = 'DELETE '.$db->qn('a').' FROM '.$db->qn('#__controlcard_cards', 'a').' WHERE '.$db->qn('a.id').' in ('.$str_id.')';

		$db->setQuery($queryStr);
		return $db->execute();
	}
}