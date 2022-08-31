<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

//JLoader::register('ControlcardModelPerson', JPATH_COMPONENT . '/models/person.php');
//require_once JPATH_COMPONENT . '/models/person.php';

//define('FPDF_FONTPATH', JPATH_LIBRARIES . '/fpdf/font/');
//require JPATH_LIBRARIES.'/fpdf/fpdf.php';
//JLoader::register('FPDF', JPATH_LIBRARIES . '/fpdf/fpdf.php');

defined('_JEXEC') or die;

/**
 * Controlcard helper.
 *
 * @package     A package name
 * @since       1.0
 */
class ControlcardHelper
{
	/**
	 * Render submenu.
	 *
	 * @param   string  $vName  The name of the current view.
	 *
	 * @return  void.
	 *
	 * @since   1.0
	 */
	public static $myFormat = "d-m-Y";

	public function addSubmenu($vName)
	{
		#$t=Text::_('COM_CONTROLCARD');
		JHtmlSidebar::addEntry(Text::_('COM_CONTROLCARD'), 'index.php?option=com_controlcard&view=controlcard', $vName == 'controlcard');
	}

	/********************************************************
	 * Вернуть список прав для пользователя
	 *
	 ********************************************************/
	public static function getActions($section = 'component', $id=0, $user_id=0, $person_id=0 )
	{
		$user   = JFactory::getUser();
		$result = new \JObject;

		$path = JPATH_ADMINISTRATOR . '/components/com_controlcard/access.xml';

		if (empty($section))
		{
			$section = 'component';
		}

        if (empty($id) && empty($user_id) && empty($person_id))
        {
            $section = 'component';
        }
        $actions = JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");

        foreach ($actions as $action)
        {
            $result->set($action->name, $user->authorise($action->name, 'com_controlcard'));
        }

        return $result;
    }

    public static function getParams($component='com_controlcard', $firstComponent=false) {
	    $paramsApp  = JFactory::getApplication()->getParams();
	    $paramsComp = JComponentHelper::getParams('com_controlcard');
	    if ($firstComponent) {
		    $paramsComp->merge($paramsApp);
		    $paramsApp = clone($paramsComp);
	    } else {
		    $paramsApp->merge($paramsComp);
	    }
	    return $paramsApp;
    }

	/********************************************************
	 * Проверить дату на валидность
	 *
	 ********************************************************/
	public static function validateDate($date, $format='d-m-Y')
	{
		//$da = getdate(date_create_from_format($format, $date, new DateTimeZone('Aisa/Vladivostok'))->getTimestamp());
		try {
			if ($dt = date_create_from_format('d-m-Y', $date)) {
				$da = getdate($dt->getTimestamp());
				return checkdate($da['mon'], $da['mday'], $da['year']);
			}
			else {
				return false;
			}
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/********************************************************
	 * Вернуть строку для performed_type, т.е. типа исполнения
	 *
	 ********************************************************/
	public static function getStringPerformedType( int $performed_type=1) {
		$s = 'COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_' . (string) $performed_type;
		$strType = JText::_($s);
		if (empty($strType)) {
			$strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_1');
		}
		$strCmp = 'COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_';
		if ( mb_substr($strType, 0, mb_strlen($strCmp)) == $strCmp) {
			$strType = JText::_('COM_CONTROLCARD_CARDS_PERFORMED_TYPE_VALUE_1');
		}
		return $strType;
	}

	/****************************************
	 * @param     $dateBegin
	 * @param     $dateEnd
	 * @param     $period   - количество единиц в периоде, т.е. дней, месяцев или лет
	 *                      Но период лет сводится к периоду месяцев 12, 24, 36 и т.д.
	 * @param int $type
	 *             =1 - период в днях, т.е.       ДатаСлед = dateBegin + period дней
	 *             =2 - период в месяцах, т.е.    ДатаСлед = dateBegin + period месяцев
	 *             =3 - период в годах, т.е.      ДатаСлед = dateBegin + period лет
	 * @since version
	 * Возвращает дату следующего выполнения.
	 *        =dateBegin , если dateBegin > dateEnd
	 */
	public static function getNextDateForPeriod( DateTime $dateBegin, DateTime $dateEnd, int $period, int $type=1) {
		try {
			if ($period == 0) { $period = 1; }
			if ($dateBegin > $dateEnd) {
				return $dateBegin;
			}
			$result = $dateEnd->diff($dateBegin);

			if ($result) {
				if ($type == 1)       // период в днях
				{
					$dayDiff = $result->days;
					$ostatok = $dayDiff % $period;
					if ($ostatok == 0) {
						$dayDiifNext = $dayDiff;
					} else {
						$dayDiifNext = $dayDiff + ($period - $ostatok);
					}
					$result = $dateBegin->add(new DateInterval('P' . (string)$dayDiifNext .'D'));
				}
				elseif ($type == 2)   // период в месяцах}
				{
					$monthDiff = $result->m + $result->y*12;
					$ostatok = $monthDiff % $period;
					if ( ($ostatok == 0) && ($result->d == 0) ) {
						$monthDiffNext = $monthDiff;
					} else
					{
						$monthDiffNext = $monthDiff + $period - $ostatok;
					}
					$result = $dateBegin->add(new DateInterval('P' . (string)$monthDiffNext .'M'));
				}
				elseif ($type == 3)   // период в годах}
				{
					$result = self::getNextDateForPeriod($dateBegin, $dateEnd, $period*12, 2);
				}
				else
				{
					$result = false;
				}
			} // if ($dateInterval) {
		}
		catch (Exception $e) {
			$result = false;
		}
		return $result;

	}
	/********************************************************
	 * Рассчитать дату следующего исполнения (для циклических)
	 *
	 ********************************************************/
    public static function getDateNextPerformed($item=null)
    {
    	$result = '';
    	if ( isset($item) && isset($item->performed_date)
		        && isset($item->performed_type) && isset($item->performed_ext_int)
		        && isset($item->performed)
		        && !$item->performed
	        )
	    {
		    $date = $item->performed_date;
		    $type = $item->performed_type;
		    $ext_int = $item->performed_ext_int;
		    //$ofs = JFactory::getApplication()->get('offsett');
		    $dateDT_performed = new DateTime($date);
		    $dateDT_performed_BD = new DateTime($date);
		    $dateDT_current = new DateTime('now');
		    $dateDT_performed->setTime(0,0);
		    $dateDT_current->setTime(0,0);
		    //$myFormat = 'd-m-Y';

		    $isCurrentLessBegin = $dateDT_current < $dateDT_performed;
		    $dateInterval = $dateDT_current->diff($dateDT_performed);
		    if ($dateInterval) {
			    switch ($type) {
				    case 1: // однократно
					    //$result = (new DateTime($date, $ofs))->format('d.m.Y');
					    //$result = $dateDT_performed->format(self::$myFormat);
					    $result = $dateDT_performed;
					    break;
				    case 2: // еженедельно
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P1W'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P1W'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, 7, 1);
					    //if ($result) {$result->format($myFormat);}
					    break;
				    case 3: // ежемесячно
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P1M'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P1M'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, 1, 2);
					    break;
				    case 4: // ежегодно
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P1Y'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P1Y'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, 12, 2);
					    break;
				    case 5: // каждые N дней
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P' . (string)$ext_int .'D'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P' . (string)$ext_int .'D'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, $ext_int, 1);
					    //if ($result) {$result->format($myFormat);}
					    break;
				    case 6: // ежеквартально
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P' . (string)$ext_int .'D'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P3M'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, 3, 2);
					    break;
				    case 7: // каждые полгода
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P' . (string)$ext_int .'D'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P6M'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, 6, 2);
					    break;
				    case 8: // каждые N месяцев
					    //$result = (new DateTime($date, $ofs))->add(new DateInterval('P' . (string)$ext_int .'D'))->format('d.m.Y');
					    //$result = $dateDT_performed->add(new DateInterval('P' . (string)$ext_int .'M'))->format($myFormat);
					    $result = self::getNextDateForPeriod($dateDT_performed, $dateDT_current, $ext_int, 2);
					    break;
				    default:
					    $result = '';
			    } // switch ($type) {
			    if ($result and !empty($result)) {
			    	//$result = $result->format($myFormat);
				    $result = $result->format(self::$myFormat);
			    }
		    } // if ($dateInterval) {

//		    if ( !empty($result) && ($dateDT_current < $dateDT_performed_BD) )
		    if ( !empty($result) && $isCurrentLessBegin )
		    {
			    //$result = $dateDT_performed_BD->format($myFormat);
			    $result = $dateDT_performed_BD->format(self::$myFormat);
		    }
        }

	    //$result = (new DateTime($date))->format('d.m.Y');
	    if ( !empty($result) )
	    {
	    	if ( self::validateDate($result) ) {
			    $result = (new DateTime($result))->setTime(9,0)->format(self::$myFormat);
		    } else {
			    $result = '';
		    }
	    }
    	return $result;
    }

	/********************************************************
	 * Рассчитать и заполнить в карточке поля performed_date_next и day_to_performed,
	 * дата следующего исполнения (для циклических) и кол-во дней до
	 * следующего исполднения
	 ********************************************************/
	public static function fillDateNextPerformed($item, $absolute=true)
	{
		$dt = self::getDateNextPerformed($item);
		$item->performed_date_next = $dt;
		if (! empty($item->performed_date_next)) {
			//$ofs = JFactory::getApplication()->get('offsett');
			//$dtNow = (new DateTime('now', $ofs))->setTime(0, 0);
			//$dt = new DateTime($dt, $ofs);
			$dtNow = (new DateTime('now'))->setTime(0, 0);
			$dt = new DateTime($dt);
			$res = 0;
			if ($dt > $dtNow) {
				//$res = (new DateTime($item->performed_date_next, $ofs))->diff(new DateTime('now', $ofs))->days;
				//$res = (new DateTime($item->performed_date_next))->diff(new DateTime('now', $ofs))->days;
				$interval = $dt->diff($dtNow);
				$res = $interval->days;
			}
		} else {
			$res = '';
		}
		if ($res < 0)
		{
			$res = 0;
		}
		/*
		elseif ( ($res == 0) && ($interval->h > 9 ) )
		{
			$res += 1;
		}
		*/
		$item->day_to_performed = $res;
		if (!$absolute)
		{
			$item->date_document_int    = (new DateTime($item->date_document_int))->format("d-m-Y");
			$item->date_document        = (new DateTime($item->date_document))->format("d-m-Y");
			$item->performed_date       = (new DateTime($item->performed_date))->format("d-m-Y");
		}
	}

	/********************************************************
	 * Вернуть связанного сотрудника с текущим пользователем
	 *
	 ********************************************************/
	public static function getPersonForUser($user_id=null){
		if ( empty($user_id) ) {
			$user_id = JFactory::getUser()->id;
		}
		if ( $user_id ) {
			// Create a new query object.
			$db    = JFactory::getDbo();
			//$query = $db->getQuery(true);
			$db->setQuery(
				'select ' . $db->qn('id') . ' FROM ' . $db->qn('#__controlcard_persons')
				. ' WHERE ' . $db->qn('user_id') . '=' . (string)$user_id
			);

			try
			{
				$result = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				//$this->setError($e->getMessage());
				JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
				return false;
			}
			$result = $result ? $result : false;
			//return $result;
		} else {
			$result = false;
		}
		return $result;
	}

	/********************************************************
	 * печать карточки в PDF
	 *
	 ********************************************************/
	public static function getPrintPDF($item=null, $params=array()) {
		try
		{
			if ($item == null)
			{
				try {
					$app = JFactory::getApplication();
					$fd  = $app->input->get('jform', array(), 'array');
					if (empty($fd)) {
						$app->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_CREATEPDF_NOTDATA'), 'warning');
						$res = false;
					}
					$item = \Joomla\Utilities\ArrayHelper::toObject($fd);
					$res = !empty($item);
				}
				catch (Exception $e)
				{
					$app->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_CREATEPDF_BADDATA'), 'warning');
					$app->enqueueMessage(JText::_($e->getMessage()), 'warning');
					$res = false;
				}
				if (!res) {
					return $res;
				}
			}

			// печать в PDF
			$pdf = new FPDF( 'P', 'mm', 'A4' );
			$pdf->addPage();
			$params['sizefont']     = 10;
			$params['rightmargin']  = 10;
			$params['leftmargin']   = 10;
			$params['fontname']     = 'Times';
			$pdf = self::pdfPrintBody($item, $pdf, $params);
		} catch (\Exception $e) {
			$pdf = false;
		}
		return $pdf;
	}

	/************************************************
	 * Печать списка карточек в PDF
	 *
	 * @param array $ids
	 *
	 * @return FPDF|null
	 *
	 * @since version
	 * @throws Exception
	 ************************************************/
	public static function pdfPrintListCards(array $ids=array(), $params=array())
	{
		if ( empty($ids) ) {
			$ids = JFactory::getApplication()->input->get('eid', array(), 'array');
		}
		$pdf = new FPDF( 'P', 'mm', 'A4' );
		$pdf->addPage();
		foreach ($ids as $id) {
			$item = self::getDataCard($id);
			self::fillDateNextPerformed($item, false);
			// поля типа TIMESTAMP привести к виду дд-мм-ГГГГ
			// date_document_int
			// date_document_int
			// performed_date
			//$item->date_document_int    = (new DateTime($item->date_document_int))->format("d-m-Y");
			//$item->date_document        = (new DateTime($item->date_document))->format("d-m-Y");
			//$item->performed_date       = (new DateTime($item->performed_date))->format("d-m-Y");
			$pdf = self::pdfPrintBody($item, $pdf, $params);
			$pdf->Ln(2);
			$pdf->Cell( 0, 0, '', 'B', 1, 'C' );
			$pdf->Ln(2);
		}
		return $pdf;
	}
	/*****************************************************
	 * Печать непосредственно тела одной карточки в PDF
	 *
	 *****************************************************/
	//public static function pdfPrintBody($item, $modelPerson, $pdf=null, $params=array(), $addPage = false)
	public static function pdfPrintBody($item, $pdf=null, $params=array(), $addPage = false)
	{
		if (is_null($pdf)) { $pdf = new FPDF( 'P', 'mm', 'A4' ); }
		if ($addPage) { $pdf->AddPage(); }
		// подсчет даты исполнения
		if (!isset($item->performed_date_next)) {
			$item->performed_date_next = self::getDateNextPerformed();
		}

		// вернуть данные сотрудника
		if ($item->person_id) {
//			if ( ($modelPerson == null) || !isset($modelPerson)
//				|| !is_a($modelPerson, 'ControlCardModelPerson') )
//			{
//				$modelPerson = new ControlCardModelPerson();
//			}
//			$itemPerson = $modelPerson->getItem($item->person_id);
			$itemPerson = self::getDataPerson($item->person_id);
			if (isset($itemPerson)) {
				$item->fio = $itemPerson->fio;
				$item->person_post = $itemPerson->person_post;
				$item->dismiss = $itemPerson->dismiss;
			} else {
				$item->fio = '';
				$item->person_post = '';
				$item->dismiss = 0;
			}
		} else {
			// нет сотрудника
			//$app->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_CREATEPDF_NOTDATA'), 'warning');
			//$res = false;
			$item->fio = '';
			$item->post = '';
			$item->dismiss = 0;
		}

		$sizeFont = isset($params['fontsize']) ? $params['fontsize'] : 10;
		$rightMargin = isset($params['rightmargin']) ? $params['rightmargin'] : 10;
		$leftMargin = isset($params['leftmargin']) ? $params['leftmargin'] : 10;
		$fontName =  isset($params['fontname']) ? $params['fontname'] : 'Times';

		$pdf->SetFont( $fontName, '', $sizeFont );
		//$pdf->SetFont( 'Arial', '', 12 );
		$title = iconv('utf-8', 'windows-1251', "КОНТРОЛЬНАЯ КАРТОЧКА № " . $item->num_controlcard);
		$pdf->Cell( 0, 5, $title, 0, 1, 'C' );
		$pdf->Cell( 0, 0, '', 'B', 1, 'C' );
		$yTitle = $pdf->getY();

		// таблица № док-тов, исп-ль и т.д.
		$hs = 6;
		$begY = $pdf->getY();
		// first column TITLE
		$wt1 = 28;
		$wd1 = 40;
		$wt2 = 30;
		$xt1 = $rightMargin;
		$xd1 = $xt1 + $wt1;
		$xt2 = $xd1 + $wd1;
		$xd2 = $xt2 + $wt2;

		// LINE 1
		//--------- column 1
		$yLine1 = $pdf->getY();
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setX($xt1);
		$str = iconv('utf-8', 'windows-1251', "№ док. внутрен.: ");
		$pdf->Cell( $wt1, $hs, $str, '', 1, 'L' );
		$y1 = $pdf->getY();
		//--------- data 1
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd1, $yLine1);
		$str = iconv('utf-8', 'windows-1251', $item->num_document_int);
		$pdf->Cell( $wd1, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- column 2
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt2, $yLine1);
		$str = iconv('utf-8', 'windows-1251', "Доклад                 : ");
		$pdf->Cell( $wt2, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- data 2
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd2, $yLine1);
		$wd2=$pdf->GetPageWidth() - $xd2 - $rightMargin;
		$str = iconv('utf-8', 'windows-1251', $item->report);
		$pdf->MultiCell( $wd2, $hs, $str, '', 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		// y для следующей строки
		$yLine2 = $y1;
		$pdf->setXY($rightMargin, $yLine2);
		$pdf->Cell(0,0, '', 'B');

		// LINE 2
		//--------- column 1
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt1, $yLine2);
		$str = iconv('utf-8', 'windows-1251', "Дата поступлен.: ");
		$pdf->Cell( $wt1, $hs, $str, '', 1, 'L' );
		$y1 = $pdf->getY();
		//--------- data 1
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd1, $yLine2);
		$str = is_string($item->date_document_int) ? $item->date_document_int : date_format($item->date_document_int, 'd.m.Y');
		$str = iconv('utf-8', 'windows-1251', $str);
		$pdf->Cell( $wd1, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- column 2
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt2, $yLine2);
		$str = iconv('utf-8', 'windows-1251', "Исполнитель       :");
		$pdf->Cell( $wt2, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- data 2
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd2, $yLine2);
		$wd2=$pdf->GetPageWidth() - $xd2 - $rightMargin;
		$str = iconv('utf-8', 'windows-1251', $item->fio);
		$pdf->MultiCell( $wd2, $hs, $str, '', 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		// y для следующей строки
		$yLine3 = $y1;
		$pdf->setXY($rightMargin, $yLine3);
		$pdf->Cell(0,0, '', 'B');

		// LINE 3
		//--------- column 1
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt1, $yLine3);
		$str = iconv('utf-8', 'windows-1251', "№ документа     : ");
		$pdf->Cell( $wt1, $hs, $str, '', 1, 'L' );
		$y1 = $pdf->getY();
		//--------- data 1
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd1, $yLine3);
		$str = iconv('utf-8', 'windows-1251', $item->num_document);
		$pdf->Cell( $wd1, $hs, $str, '', 1, 'L' );
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- column 2
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt2, $yLine3);
		$str = iconv('utf-8', 'windows-1251', "Основание           :");
		$pdf->Cell( $wt2, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- data 2
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd2, $yLine3);
		$wd2=$pdf->GetPageWidth() - $xd2 - $rightMargin;
		$str = iconv('utf-8', 'windows-1251', $item->reason);
		$pdf->MultiCell( $wd2, $hs, $str, '', 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		// y для следующей строки
		$yLine4 = $y1;
		$pdf->setXY($rightMargin, $yLine4);
		$pdf->Cell(0,0, '', 'B');


		// LINE 4
		//--------- column 1
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt1, $yLine4);
		$str = iconv('utf-8', 'windows-1251', "Дата документа : ");
		$pdf->Cell( $wt1, $hs, $str, '', 1, 'L' );
		$y1 = $pdf->getY();
		//--------- data 1
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd1, $yLine4);
		$str = is_string($item->date_document) ? $item->date_document : date_format($item->date_document, 'd.m.Y');
		$str = iconv('utf-8', 'windows-1251', $str);
		$pdf->Cell( $wd1, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- column 2
		$pdf->SetFont( $fontName, '', $sizeFont );
		$pdf->setXY($xt2, $yLine4);
		$str = iconv('utf-8', 'windows-1251', "Срок исполнения:");
		$pdf->Cell( $wt2, $hs, $str, '', 1, 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		//--------- data 2
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$pdf->setXY($xd2, $yLine4);
		$wd2=$pdf->GetPageWidth() - $xd2 - $rightMargin;
		$str = is_string($item->performed_date_next) ? $item->performed_date_next : date_format($item->performed_date_next, 'd.m.Y');
		$str = iconv('utf-8', 'windows-1251', $str);
		$pdf->MultiCell( $wd2, $hs, $str, '', 'L' );
		//max $y1 and $y2
		$y2 = $pdf->getY();
		$y1 = $y1 > $y2 ? $y1 : $y2;
		// y для следующей строки
		$yLine5 = $y1;
		$pdf->setXY($rightMargin, $yLine5);
		$pdf->Cell(0,0, '', 'B');
		$pdf->setXY($xt2, $yTitle);
		$pdf->Cell(0,$yLine5-$yTitle, '', 'L');
		$pdf->setXY($rightMargin, $yLine5);

		$pdf->Ln(2);

		// содержание документа
		$begY_note = $pdf->getY();
		$pdf->SetFont( $fontName, '', $sizeFont );
		$str = iconv('utf-8', 'windows-1251', 'Содержание документа:');
		$pdf->MultiCell( $wt1, $hs, $str, 'R', 'L' );

		$begY_note1 = $pdf->getY();
		$pdf->setXY($x = $rightMargin + $wt1, $begY_note);
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$str = iconv('utf-8', 'windows-1251', $item->note);
		$pdf->MultiCell( 0, $hs, $str, 'L', 'J' );
		$begY_note2 = $pdf->getY();
		// max Y
		$y = ($begY_note1 < $begY_note2) ? $begY_note2: $begY_note1;
		$pdf->setY($y);
		//$pdf->setX($x);
		$pdf->Cell( 0, 0, '', 'B', 1, 'C' );

		// содержание поручения
		$begY_note = $pdf->getY();
		$pdf->SetFont( $fontName, '', $sizeFont );
		$str = iconv('utf-8', 'windows-1251', 'Содержание поручения:');
		$pdf->MultiCell( $wt1, $hs, $str, 'R', 'L' );

		$begY_note1 = $pdf->getY();
		$pdf->setXY($x, $begY_note);
		$pdf->SetFont( $fontName, 'BD', $sizeFont );
		$str = iconv('utf-8', 'windows-1251', $item->note_big);
		$pdf->MultiCell( 0, $hs, $str, 'L', 'J' );
		$begY_note2 = $pdf->getY();
		// max Y
		$y = ($begY_note1 < $begY_note2) ? $begY_note2: $begY_note1;
		$pdf->setY($y);
		$pdf->Ln(0);
		//$pdf->Ln(2);
		//$pdf->Cell( 0, 0, '', 'B', 1, 'C' );
		//$pdf->Ln(2);
		if ( isset($params['printpdf_date']) && $params['printpdf_date']) {
			$pdf->SetFont( $fontName, '', 6 );
			$pdf->Cell(0, 1, date('d.m.Y H:i'));
		}
		return $pdf;
	}

	/*****************************************************
	 * Послать сообщение на EMail
	 * @param        $item
	 * @param string $fileAttach
	 * @param bool   $fromCurrUser
	 * @param string $subj
	 * @param string $body
	 * @param bool   $fromCli
	 * @param string $fromName
	 *
	 * @return bool
	 *
	 * @since version
	 * @throws Exception
	 *****************************************************/
	public static function sendCard2Email($item, $fileAttach='', $fromCurrUser=true,
	                $subj='В прикрепленном файле содержится контрольная карточка',
	                $body='В прикрепленном файле содержится контрольная карточка',
					$fromCli=false,
					$fromName='')
	{
		$person = self::getDataPerson($item->person_id);
		if ( $person == null )
		{
			//ошибка получения данных о сотруднике
			if (!$fromCli) {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_PERSON_READ', (string)$item->person_id), 'warning');
			}
			return false;
		}
		if (empty($person->email)) {
			$userTo = JFactory::getUser($person->user_id);
			if ( !$userTo->id )
			{
				//к персоне (сотруднику) не привязан пользователь
				if (!$fromCli)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_PERSON_NOTLINK_USER', $person->fio), 'warning');
				}
				return false;
			}
			$to = $userTo->email;
		} else {
			$to = $person->email;
		}

		if (empty($to)){
			// нет почтового адреса у пользователя КОМУ
			if (!$fromCli)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_CONTROLCARD_ERROR_PERSON_NOT_EMAIL', $person->fio), 'warning');
			}
			return false;
		}
		if ($fromCurrUser) {
			$user = JFactory::getUser();
			$from = $user->email;
		}
		if (empty($from)) {
			$config = JFactory::getConfig();
			$from = $config->get('mailfrom', '', 'string');
		}
		if(empty($from)){
			// нет почтового адреса у пользователя или на сайте ОТ КОГО
			if (!$fromCli)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTROLCARD_ERROR_EMAIL_FROM'), 'warning');
			}
			return false;
		}
		if (empty($body)) {
			$body = $subj;
		}
		//echo '123';
		if (empty($fromName))
		{
			$fromName = $config->get('fromname', '', 'string');
		}
		$fromName = empty($fromName) ? $config->get('sitename', 'Контрольные карточки', 'string') : $fromName;

		if (empty($fileAttach)) { $fileAttach = null; }
		$mailer = JFactory::getMailer();
		return $mailer->sendMail($from, $fromName, $to, $subj, $body, false, null, null, $fileAttach);
	}

	/*********************************
	 * Возвращает данные о сотруднике
	 *
	 * @param $id
	 *
	 * @return bool|\Joomla\CMS\Table\Table|object
	 *
	 * @since version
	 *********************************/
	public static function getDataPerson($id) {
		if ( !$id )
		{
			return false;
		}
		//$table = new TablePersons(JFactory::getDbo());
		$table = JTable::getInstance('Persons', 'Table');
		if ($result = $table->load($id)) {
			$result = $table;
			$properties = $table->getProperties(1);
			$result = ArrayHelper::toObject($properties, '\JObject');
			if (property_exists($result, 'params'))
			{
				$registry = new Registry($result->params);
				$result->params = $registry->toArray();
			}
		} // if ($result = $table->load($id)) {
		return $result;
	}

	public static function getDataCard($id) {
		if ( !$id )
		{
			return false;
		}
		$table = JTable::getInstance('Cards', 'Table');
		if ($result = $table->load($id)) {
			$result = $table;
			$properties = $table->getProperties(1);
			$result = ArrayHelper::toObject($properties, '\JObject');
			if (property_exists($result, 'params'))
			{
				$registry = new Registry($result->params);
				$result->params = $registry->toArray();
			}
		} // if ($result = $table->load($id)) {
		return $result;
	}

	function _date($format="r", $timestamp=false, $timezone=false)
	{
		$userTimezone = new DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
		$gmtTimezone = new DateTimeZone('GMT');
		$myDateTime = new DateTime(($timestamp!=false?date("r",(int)$timestamp):date("r")), $gmtTimezone);
		$offset = $userTimezone->getOffset($myDateTime);
		return date($format, ($timestamp!=false?(int)$timestamp:$myDateTime->format('U')) + $offset);
	}

}
