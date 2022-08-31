<?php

const _JEXEC = 1;

define ('DEBUG', 1);
const _DEBUG = 0;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';


define('JPATH_COMPONENT', JPATH_BASE . '/components/com_controlcard');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_BASE . '/administrator/components/com_controlcard');

JLoader::register('ControlcardControllerCard', JPATH_COMPONENT . '/controllers/card.php');
JLoader::register('ControlCardModelCard', JPATH_COMPONENT . '/models/card.php');
JLoader::register('ControlCardModelPerson', JPATH_COMPONENT . '/models/person.php');
JLoader::register('ControlCardModelCards', JPATH_COMPONENT . '/models/cards.php');
JLoader::register('ControlcardHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controlcard.php');
JLoader::register('ControlcardHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/controlcard.php');
JLoader::register('TablePersons', JPATH_COMPONENT_ADMINISTRATOR . '/tables/persons.php');

define('FPDF_FONTPATH', JPATH_LIBRARIES . '/fpdf/font/');
require JPATH_LIBRARIES.'/fpdf/fpdf.php';
//JLoader::register('FPDF', JPATH_LIBRARIES . '/fpdf/fpdf.php');

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

JLoader::registerPrefix('Avv', JPATH_LIBRARIES.'/avv');


class ccService extends JApplicationCli
//class ccService extends JApplication
{
    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
    	//$model_cards = new ControlcardModelCards();
    	//$cards = $model_cards->getItems(true);
	    $param = JComponentHelper::getParams('com_controlcard');
	    $debug = _DEBUG || $param->get('notification_enablelog', false);
	    $fileLog = $param->get('notification_filelog','controlcard-service.log');

	    AvvLog::logMsg(
		    ['msg' =>
			     [
				     '======================================================================================'
				     ,'==============' . time() . '=============='
				     ,'======================================================================================'
			     ],
			    'category'], $debug, null, $fileLog
	    );

	    AvvLog::logMsg(
		    ['msg' =>
			     [
			     	 '++++++++++++++++++++++++++++++++++++++++++++++++++++'
				     ,'Параматеры: '
				     ,$param
			     	 ,'++++++++++++++++++++++++++++++++++++++++++++++++++++'
			     ],
			    'category'], $debug, null, $fileLog
	    );

	    // Подготовка и отправка уведомлений
	    // о приближении сроков исполнения по карточкам
	    //
	    $arrayDaysStr = explode(',', $param->get('notification_days', '', 'string'));
	    $f = false;
	    $arrayDays = array();
	    while ( ($el  = array_shift($arrayDaysStr))!= null )
	    {
		    try
		    {
			    $el = (int)$el;
			    if ($el) {
				    $arrayDays[] = $el;
			    }
		    }
		    catch (Exception $e) {
		    	$el=0;
		    }
	    }
	    $f = !empty($arrayDays);
	    AvvLog::logMsg(
		    ['msg' =>
			     [
				     '++++++++++++++++++++++++++++++++++++++++++++++++++++'
				     ,'дни в которые надо отправить уведомления о сроке (arrayDays): '
				     ,$arrayDays
			     	 ,'++++++++++++++++++++++++++++++++++++++++++++++++++++'
			     ],
			    'category'], $debug, null, $fileLog
	    );
	    $un = $param->get('notification_use', false, 'boolean') && $f;
	    AvvLog::logMsg(
		    ['msg' =>
			     [
				     '++++++++++++++++++++++++++++++++++++++++++++++++++++'
				     ,'Используем уведомления: ' . $un
				     ,'++++++++++++++++++++++++++++++++++++++++++++++++++++'
			     ],
			    'category'
		    ], $debug, null, $fileLog
	    );
	    if ($un) {
	    	// Отправить уведомления на почту.
		    $db = JFactory::getDbo();
		    $query = $db->getQuery(true);
		    $query->select($db->qn('a') . '.*, '
			    . $db->qn('b.fio') . ', '
			    . $db->qn('b.dismiss') . ', '
			    . $db->qn('b.person_post') . ', '
			    . $db->qn('u.username', 'login') . ', '
			    . $db->qn('u.name') . ', '
			    . $db->qn('u.email')
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
			    )
			    ->where($db->qn('a.performed') . '=0');
		    $db->setQuery($query);
		    $db->query();
		    //$cards = $db->loadAssocList();
		    $cards = $db->loadObjectList();
		    foreach ($cards as $item) {
		    	// заполнить данными о следующем сроке исполнения
			    ControlcardHelper::fillDateNextPerformed($item, false);
			    if ( array_search((int)$item->day_to_performed, $arrayDays) !== false) {
				    // отправить уведомление
				    $fileAtt=null;
				    if ($param->get('notification_sendpdf', true, 'boolean')) {
				    	// подготовить pdf файл с карточкой
					    try {
						    $ret = ControlcardHelper::getPrintPDF($item);
						    if ($ret) {
							    // собственно сформировать PDF файл отправить по E-Mail или ывовести на печать
							    $dst = 'F';
							    $fileAtt = JPATH_SITE . '/tmp/' . 'card-' . (string)$item->id . '-em-service.pdf';
							    $result = $ret->Output($dst, $fileAtt);
						    }
					    }
					    catch (Exception $e) {
						    $fileAtt = null;
					    }
				    } //if ($param->get('notification_sendpdf', true, 'boolean'))
				    $sSubj = sprintf('Осталось %s дней до срока исполнения карточки № %s',
					    (string) $item->day_to_performed,
					    (string) $item->num_controlcard);
				    AvvLog::logMsg(
					    ['msg' =>
						     [
							     '++++++++++++++++++++++++++++++++++++++++++++++++++++'
							     ,'item->email: ' . $item->email
							     ,'item->fio: ' . $item->fio
							     ,'fileAtt: ' . $fileAtt
							     ,'sSubj: ' . $sSubj
							     ,'++++++++++++++++++++++++++++++++++++++++++++++++++++'
						     ],
						    'category'
					    ], $debug, null, $fileLog
				    );
				    $res = ControlcardHelper::sendCard2Email($item, $fileAtt, false,
					        $sSubj,
				            $sSubj,
				            true, 'Админ карточек'

				        );
				    AvvLog::logMsg(
					    ['msg' =>
							[
								'++++++++++++++++++++++++++++++++++++++++++++++++++++'
							    ,'Отправлено: ' . $res
								,'++++++++++++++++++++++++++++++++++++++++++++++++++++'
						    ],
							'category'
					    ], $debug, null, $fileLog
				    );

			    } // if ( array_search((int)$item->day_to_performed, $arrayDays) ) {
		    } // foreach ($cards as $item) { ***************************************
	    } //if ($param->get('use_notification', false, 'boolean'))

	    // Подготовка и удаление файлов *.pdf из каталога /tmp
//	    $pathPDF = JPATH_SITE .'/tmp/*.pdf';
//	    array_map("unlink", glob($pathPDF));
    }

}

JApplicationCli::getInstance('ccService')->execute();
//$a = new ccService();
//$a->doExecute();
