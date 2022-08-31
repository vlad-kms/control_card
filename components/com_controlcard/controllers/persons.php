<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.01.2019
 * Time: 18:07
 */

defined('_JEXEC') or die;

/**
 * Controlcard controller.
 *
 * @package  controlcard
 * @since    1.0
 */
class ControlcardControllerPersons extends JControllerForm
{
    //protected $canDo;
    protected $default_view = 'persons';
    //protected $model_prefix = 'qqq';

    public function __construct($config = array())
    {
        parent::__construct($config);

        //$this->registerTask('dismiss', 'dismiss');
        $this->registerTask('recruit', 'dismiss');
	    $this->registerTask('deleteList', 'delete');
    }

	protected function allowAdd($data = array())
	{
		return ControlcardHelper::getActions()->get('core.persons.edit');
	}
	protected function allowEdit($data = array(), $key = 'id')
	{
		return \JFactory::getUser()->authorise('core.persons.edit', $this->option);
		//return ControlcardHelper::getActions()->get('core.persons.edit');
	}
	protected function allowDelete()
	{
		return \JFactory::getUser()->authorise('core.persons.delete', $this->option);
		//return ControlcardHelper::getActions()->get('core.persons.edit');
	}

	/*********************************************************
	 *  Удалить отвественные лица (сотрудников) из БД
	 *
	 *********************************************************/
	public function delete()
	{
		AvvLog::logMsg(['msg' =>
			                [
				                'ControlcardControllerPersons->delete ===============================================================================',
				                '$_REQUEST', $_REQUEST
			                ],
			'category'], defined('AVV_DEBUG'), null, 'controlcard.log'
		);
		// Check for request forgeries
		$this->checkToken();

		if ($this->allowDelete()){
			// Get items to remove from the request.
			$cid = $this->input->get('eid', array(), 'array');

			if (!is_array($cid) || count($cid) < 1)
			{
				\JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
				//\JLog::add(\JText::_('JGLOBAL_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
			}
			/*
			else
			{
				// Get the model.
				$model = $this->getModel();

				// Make sure the item ids are integers
				$cid = ArrayHelper::toInteger($cid);

				// Remove the items.
				if ($model->delete($cid))
				{
					$this->setMessage(\JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
				}
				else
				{
					$this->setMessage($model->getError(), 'error');
				}

				// Invoke the postDelete method to allow for the child class to access the model.
				$this->postDeleteHook($model, $cid);
			}
			*/
		}
		else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_DELETE'), 'warning');
		}

		$this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	public function getModel($name = '', $prefix = '', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

    public function dismiss()
    {
	    AvvLog::logMsg(['msg' =>
			[
				'ControlcardControllerPersons->dismiss ===============================================================================',
			    '$_REQUEST', $_REQUEST
		    ],
		    'category'], defined('AVV_DEBUG'), null, 'controlcard.log'
	    );
	    $this->checkToken();

//        $canDo = ControlcardHelper::getActions();
//        if ( $canDo->get('core.persons.edit') ) {
		if ( $this->allowEdit() ) {
            // установить(снять) флаг уволен
            //$model = $this->getModel('Person');
            $model = $this->getModel('Persons');
            $id = (int)$_REQUEST['eid'][0];
                // получить флаг Уволен данной записи работы или принять на работу: =0 принять на работу; =1 уволить с работы
            $table = $model->getTable('Persons');
            if ($table->load($id)) {
                $value = $table->dismiss ? 0: 1;
            }
            $d = $model->dismiss($id, $value);
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_PERMISSION_NOT_RIGHT_EDIT_PERSON'), 'warning');
        }

	    //$this->setRedirect(\JRoute::_('index.php?option=com_controlcard' . '&start=' . $this->input->get('start'), false));
        $this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=persons', false));
    }
}
