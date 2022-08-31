<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 11.01.2019
 * Time: 22:49
 */
defined('_JEXEC') or die;

#JHTML::_('behavior.tooltip');

class ControlcardViewPerson extends JViewLegacy
{
	protected $access;
    protected $form;
	protected $item;
    //protected $params;
    protected $state;
    protected $user;

	public function __construct($config = array())
	{
		parent::__construct($config);
		//$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
		$this->access = ControlcardHelper::getActions();
		/*
		AvvLog::logMsg(
			['msg'=>
				 [
					 'ControlcardViewPerson->__construct ===============================================================================',
					 'config: ', $config,
					 'this->_viewOnly: ' . $this->_viewOnly
				 ],'category'],
			defined('AVV_DEBUG'), NULL, 'controlcard.log'
		);
		*/
	}
    /**
     * Method to display the view.
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     *
     * @since   1.5
     */
    public function display($tpl = null)
    {
	    $this->access = ControlcardHelper::getActions();
	    if ($this->access->get('core.persons.edit'))
	    {
		    // Get the view data.
		    $app         = JFactory::getApplication();
		    $inp         = $app->input;
		    $this->user  = JFactory::getUser();
		    //$data        = array();
		    $this->form  = $this->get('Form');
		    $this->state = $this->get('State');
		    $id          = (int) $inp->get('id', 0);
		    $this->item  = $this->getModel()->getItem($id);
		    // добавить текущего пользователя в список
		    if ( !empty($id) && !empty($this->item->user_id) )
		    {
			    $u = JFactory::getUser($this->item->user_id, 0);
			    $f = $this->form->getField('user_id');
			    $f->addOption($u->name . ' (' . $u->username . ')', array('value' => $u->id));
		    }
		    // Check for errors.
		    if (count($errors = $this->get('Errors')))
		    {
			    JError::raiseError(500, implode('<br />', $errors));
			    return false;
		    }
		    /*
			AvvLog::logMsg(
				['msg'=>
					[
						'ControlcardViewPerson::display ===============================================================================',
						'tpl:' . $tpl
					],'category'],
				defined('AVV_DEBUG'), NULL, 'controlcard.log'
			);
		    */
		    parent::display($tpl);
	    } else {
		    JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_PERSON'), 'warning');
	    } //if ($this->>access->get('core.persons.edit')){

    }

}
