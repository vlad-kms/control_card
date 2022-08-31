<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11.01.2019
 * Time: 22:49
 */
defined('_JEXEC') or die;

#JHTML::_('behavior.tooltip');

class ControlcardViewMainpage extends JViewLegacy
{
	protected $access;
    //protected $form;
	//protected $item;
    //protected $params;
    //protected $state;
	protected $toolbar;
    //protected $user;

	public function __construct($config = array())
	{
		parent::__construct($config);
		//$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->access = ControlcardHelper::getActions();
		AvvLog::logMsg(
			['msg'=>
				 [
					 'ControlcardViewMainpage->__construct ==============================================================================='
					 , 'config: ', $config
					 , 'this->access: ' . $this->access
				 ],'category'],
			defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
	}

    public function display($tpl = null)
    {
	    $this->access = ControlcardHelper::getActions();
	        // Подготовит данные для отображения гл.страницы

	    # добавить кнопки
		$this->toolbar = $this->addToolBar();

	    // отобразить
		parent::display($tpl);
    }

	public function addToolBar () {
		$this->access = ControlcardHelper::getActions();
		if ( $this->access->get('core.persons.access')) {
			// есть доступ к сотрудникам, т.е. отобразить кнопку Сотрудники
			JToolBarHelper::custom('persons.display', '', '', JText::_('COM_CONTROLCARD_PERSONS'), false);
		}
		if ($this->access->get('core.card.access')) {
			JToolBarHelper::custom('controlcards.display', '','', JText::_('COM_CONTROLCARD_LISTS'), false);
		}
		if ($this->access->get('core.reasons.access')) {
			JToolBarHelper::custom('controlcards.display', '','', JText::_('COM_CONTROLCARD_REASONS'), false);
		}
		return JToolbar::getInstance('toolbar')->render();
	}

}
