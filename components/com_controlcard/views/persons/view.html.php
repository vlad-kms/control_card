<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

#use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Controlcard view.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardViewPersons extends JViewLegacy
{
    protected $canDo;
    protected $params;
	protected $state;
	protected $items;
	protected $pagination;
	protected $filterForm;
	protected $toolbarPersons;

	public function __construct($config = array()){
		parent::__construct($config);
		//$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->canDo = ControlcardHelper::getActions();
	}

	public function display($tpl=null) {
		// проверить права на просмотр пользователей
		$this->canDo = ControlcardHelper::getActions();

        //if ($this->canDo->get('core.persons.access') && $this->canDo->get('core.persons.display')){
        if ($this->canDo->get('core.persons.display')){
			//$this->filterForm   = $this->get('FilterForm');

            if ( isset($this->state->params)) {
                $this->params = clone($this->state->params);
			} else {
                //$paramsApp  = JFactory::getApplication()->getParams();
				//$paramsComp = JComponentHelper::getParams('com_controlcard');
				//$paramsApp->merge($paramsComp);
				//$this->params = clone($paramsApp);
	            $this->params = ControlcardHelper::getParams();
            }
	        $this->items        = $this->get('items');
	        $this->pagination   = $this->get('Pagination');
	        $this->state        = $this->get('state');
                # добавить кнопки
			$this->toolbarPersons = $this->addToolBar();
			#$this->sidebar = JHtmlSidebar::render();

            // Check for errors.
			if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
				return false;
			}
			parent::display($tpl);
		} else {
			//JFactory::getApplication()->enqueueMessage(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), 'warning');
			//JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT'), 'warning');
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_DISPLAY'), 'warning');
		}
	}

    public function addToolBar () {
	    //$this->canDo = ControlcardHelper::getActions();
	    //JToolbarHelper::title(JText::_('ToolBar PERSONS'));
	    if ( $this->canDo->get('core.persons.edit')) {
		    JToolBarHelper::addNew('person.add', JText::_('COM_CONTROLCARD_PERSONS_ADD'));
	    }
	    //if (JFactory::getUser()->authorise('core.admin')) {
	    if ($this->canDo->get('core.admin') || $this->canDo->get('core.persons.delete') ) {
		    JToolBarHelper::deleteList('','persons.deleteList', JText::_('COM_CONTROLCARD_PERSONS_DELETE'));
	    }
        return JToolbar::getInstance('toolbar')->render();
    }
}
