<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 *
 */

defined('_JEXEC') or die;

class ControlcardControllerReason extends JControllerForm
{
	protected $default_view = 'reason';

	public function __construct(array $config = array())
	{
		parent::__construct($config);
	}

		/*********************************************************
	 * Сохранение измененой (новой) записи в БД
	 *********************************************************/
	public function save($key = null, $urlVar = null)
	{
		//AvvLog::logMsg([
		//	'msg'=>
		//		[
		//			'ControlcardControllerReason->save (?task="person.save") ========================================================================='
		//			, '(---------------------------------------------------------- parameters'
		//			, 'key: ' . $key
		//			, 'urlVar: ' . $urlVar
		//			, '-----------------------------------------------------------)'
		//			, 'this->input: ==============='
		//			, $this->input
		//		],
		//	'category'], defined('AVV_DEBUG'), NULL, 'controlcard.log'
		//);
		// проверить права

		$dataForm = $this->input->get('jform', array(), 'array');

		if (!$this->allowEdit($dataForm) )
		{
			\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
			return false;
		}


		$this->input->set('id', $dataForm['id']);

		// запись
		parent::save($key, 'id');
	}

}