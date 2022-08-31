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

class ControlcardModelReasons extends JModelList
{
	protected $access = null;
	protected $default_order = '`a`.`name` ASC';
	public $params = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'a.name',
				'id', 'a.id',
			);
		}
		parent::__construct($config);
	}

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
			&& $res = $this->access->get('core.card.delete');
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
			&& $this->access->get('core.reasons.display');
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
			&& $this->access->get('core.reasons.edit');
	}

	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}
	/*
		public function getItems($addExtFields=false)
		{
			$items = parent::getItems();
			// что сделать с items (возвращенными записями таблицы)
			return $items;
		}
	*/
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('a') . '.*')
			->from($db->quoteName('#__controlcard_reasons', 'a'));

		// обработка фильтров
		$fltStr = $this->getState('filter.search');
		if ($fltStr !== '')
		{
			$searchStr = $db->q('%' . $fltStr . '%');
			$sqlStr    = '( ' . $db->qn('a.name') . ' LIKE ' . $searchStr . ')';
			$query->where($sqlStr);
		}

		// Сортировка
		$strOrder = $this->getState('list.fullordering', $this->default_order);
		if (empty(trim($strOrder)))
		{
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
		//$this->setState('list.fullordering', $this->getUserStateFromRequest($this->context . '.list.fullordering', 'list_fullordering', '', 'string'));
		//$this->setState('filter.show_panel', $this->getUserStateFromRequest($this->context . '.filter.show_panel', 'show_filter_panel', '', 'integer'));

		parent::populateState($ordering, $direction);

		$app = JFactory::getApplication();

		$list = $app->input->get('list', array(), 'array');

		//$this->setState('list.fullordering', $this->getUserStateFromRequest($this->context . '.list.fullordering', 'list_fullordering', '', 'string'));

		$this->params = ControlcardHelper::getParams();

		// установка данных pagination
		//**********************************************************************************************
		//$app->get('list_limit') - значение из configuration.php, т.е. глобальные настройки JApplication
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		if (isset($this->state->params) && ($this->state->params->get('pagination_limit', 0) != -1))
		{
			//$key = $this->get('option') . '.' . $this->getName() . '.' . 'list.limit';
			$limit = $app->getUserStateFromRequest($this->get('option') . '.' . $this->getName() . '.' .
				'list.limit', 'limit', $this->state->params->get('pagination_limit', 'uint'));
			$limit = $app->input->get('limit', $limit, 'uint');
		}
		$this->setState('list.limit', $limit);
		$value      = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

}