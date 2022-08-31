<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.01.2019
 * Time: 13:52
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers/controlcard.php';

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

/*
if (defined('AVV_DEBUG')) {
    echo '/models/persons.php';
    echo '<br/>';
}
*/

/**
 * Controlcard model.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardModelPersons extends JModelList
{
    protected   $_context   = 'com_controlcard.persons';
    protected   $_extension = 'com_controlcard';
    //protected   $_pagination;

    public function getTable($name = '', $prefix = 'Table', $options = array()) {
        return parent::getTable($name, $prefix, $options);
    }

    protected function getListQuery()
    {
        $app            = JFactory::getApplication('site');
        $user           = JFactory::getUser();

        #$groups         = implode(',', $user->getAuthorisedViewLevels());
        #$pid            = $this->getState('tag.parent_id');

        #$orderby        = $this->state->params->get('all_tags_orderby', 'title');
        #$published      = $this->state->params->get('published', 1);
        #$orderDirection = $this->state->params->get('all_tags_orderby_direction', 'ASC');
        #$language       = $this->getState('tag.language');

        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $tblName   = $this->getTable()->getTableName();

        // Select required fields from the tags.
        $query->select($db->qn('a').'.*,'.$db->qn( 'b').'.'.$db->qn('name').','.
            $db->qn( 'b').'.'.$db->qn('username').','.$db->qn( 'b.email', 'emailuser'))
            //->from($db->quoteName($tblName) . ' AS `a`')
            ->from($db->quoteName('#__controlcard_persons', 'a'))
            ->join('LEFT', $db->qn('#__users', 'b') . ' ON a.user_id = b.id');
	    /*
	    $query->select($db->qn('a.*') . ',' . $db->qn( 'b.name') . ',' . $db->qn( 'b.username') .
		            ',' . $db->qn( 'b.email'))
		    //->from($db->quoteName($tblName) . ' AS `a`')
		    ->from($db->qn('#__controlcard_persons') . ' AS ' . $db->qn('a'))
		    ->join('LEFT', $db->qn('#__users', 'b') . ' ON ' . $db->qn('a.user_id') .
	                '=' . $db->qn('b.id'));
	    */
        if($this->getState('filter.search') !== '')
        {
	        $query->where (
	        	'(' . $db->quoteName('a.fio') . ' LIKE ' .
		            $db->quote('%' . $this->state->get('filter.search') . '%') . ')'
		            . ' OR (' . $db->quoteName('b.username') . ' LIKE ' .
		            $db->quote('%' . $this->state->get('filter.search') . '%') . ')'
		            . ' OR (' . $db->quoteName('b.email') . ' LIKE ' .
	                $db->quote('%' . $this->state->get('filter.search') . '%') . ')'
		            . ' OR (' . $db->quoteName('a.person_post') . ' LIKE ' .
		            $db->quote('%' . $this->state->get('filter.search') . '%') . ')'
	        );
        }


//        where (`a`.fio like "%12%") or (b.username like "%d%") or b.email like "%123%"
//        SELECT `a`.*, `b`.* FROM `l9e7a_controlcard_persons` AS `a`
//        left join `l9e7a_users` AS `b` ON `a`.user_id=b.id
//        where (`a`.fio like "%12%") or (b.username like "%d%") or b.email like "%123%"

/*
        // Optionally filter on entered value
        if ($this->state->get('list.filter'))
        {
            $query->where($db->quoteName('a.title') . ' LIKE ' . $db->quote('%' . $this->state->get('list.filter') . '%'));
        }

        $query->where($db->quoteName('a.published') . ' = ' . $published);

        $query->order($db->quoteName($orderby) . ' ' . $orderDirection . ', a.title ASC');
*/
        return $query;
    }

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		/*
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . serialize($this->getState('filter.category_id'));
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.tag');
		$id .= ':' . $this->getState('filter.level');
		*/
		return parent::getStoreId($id);
	}

	/*
	 * Вызывается каждый раз при обновлении страницы для получения пременных из сессии и их обработки
	 */
    protected function populateState($ordering = null, $direction = null)
    {
	    $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
	    /*
	    $this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));
	    $this->setState('filter.category_id',
		    $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '', 'string')
	    );
	    $this->setState('filter.access', $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'cmd'));
	    $this->setState('filter.language', $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '', 'string'));
	    $this->setState('filter.tag', $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '', 'string'));
	    $this->setState('filter.level', $this->getUserStateFromRequest($this->context . '.filter.level', 'filter_level', null, 'int'));
	    */

	    $app = JFactory::getApplication();

        parent::populateState($ordering, $direction);
        //$paramsApp  = $app->getParams();
        //$paramsComp = JComponentHelper::getParams('com_controlcard');
        //$paramsApp->merge($paramsComp);
        //$this->state->params = clone($paramsApp);

	    $this->state->params = ControlcardHelper::getParams($this->get('option'), true);

        // установка данных для pagination
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

    protected function canEdit($record)
    {
        return \JFactory::getUser()->authorise('core.persons.edit', $this->option);
    }

    public function dismiss($pks, $value=1) {
        $dispatcher = \JEventDispatcher::getInstance();
        $user = \JFactory::getUser();
        $table = $this->getTable();
        $pks = (array) $pks;

        // Include the plugins for the change of state event.
        //\JPluginHelper::importPlugin($this->events_map['change_state']);

        // Access checks.
        foreach ($pks as $i => $pk)
        {
            $table->reset();
            if ($table->load($pk))
            {
                if (!$this->canEdit($table))
                {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
                    return false;
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
                if ($table->get($table->getColumnAlias('dismiss'), $value) == $value)
                {
                    unset($pks[$i]);
                    continue;
                }
            } //if ($table->load($pk))
        } //foreach ($pks as $i => $pk)
        // Check if there are items to change
        if (!count($pks))
        {
            return true;
        }
        // Attempt to change the state of the records.
        if (!$table->dismiss($pks, $value))
        {
            $this->setError($table->getError());
            return false;
        }
        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

}
