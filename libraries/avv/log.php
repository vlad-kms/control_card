<?php
/*
*/

if ( ! defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

jimport('joomla.log.logger.formattedtext');

class AvvLog{
//	protected $logObject;
//	protected $logEntry;
	public $logObject;
	public $logEntry;
	private   $path;
	private   $filename;
	private   $category;
	private   $isLog;

	public function __construct($arrayParams){
		self::initObjects(
			$arrayParams['path'],
			$arrayParams['filename'],
			$arrayParams['category'],
			$this->logObject,
			$this->logEntry
		);
		if (!isset($arrayParams['isLog'])) {
			$this->isLog = False;
		} else {
			$this->isLog = $arrayParams['isLog'];
		}
		$this->path = $arrayParams['path'];
		$this->filename = $arrayParams['filename'];
		$this->category = $arrayParams['category'];
		//$this->logObject = $lo;
		//$this->logEntry  = $le;
	}
	
	public function __destruct(){
		unset($this->logEntry);
		unset($this->logObject);
		//parent::__destruct();
	}
	
	public function log($arrayMsg){
		if ($this->isLog){
			foreach ($arrayMsg as $key=>$value) {
    			if (isset($value['priority'])) {
    				$this->logEntry->priority = $value['priority'];
    			} else {
    				$this->logEntry->priority = JLog::INFO;
	    		}
    			if (isset($value['category'])) {
    				$this->logEntry->category = $value['category'];
    			} else {
    				$this->logEntry->category = $this->category;
	    		}
	    		if (isset($value['dt'])){
	    			$cd=$value['dt'];
	    		}
	    		elseif (isset($value['mDate'])) {
	    			$cd = new DateTime();
	    		}
	    		else {
	    			$cd=null;
	    		}
	    		if (!is_null($cd)){
	    			$this->logEntry->datetime = $cd->format(DateTime::ATOM);
	    			$this->logEntry->date = $cd->format('Y-m-d');
	    			$this->logEntry->time = $cd->format('H-i-s');
	    		}
    			if (isset($value['msg'])) {
    				$this->logEntry->message = self::prepareMsg($value['msg']);
    				$this->logObject->addEntry($this->logEntry);
    			}
    		}
		}
	}
	
	static function initObjects(&$path, &$filename, &$category, &$logObj, &$logObjEntry){
		if ( !isset($path) || ($path == '') ) {
			$path = JPATH_ROOT.DS.'tmp'.DS.'log'.DS;
		}
		if ( !isset($filename) || ($filename =='') ) {
			$filename = 'common.log';
		}
		if ( !isset($category) || ($category =='') ) {
			$category = 'avvlog';
		}
		$opt = array(
			// Имя текстового файла для логирования, по умолчанию error.php
			'text_file' => $filename,
			// Путь к папке с логами (если параметр отсутствует, путь возьмется из конфига)
			'text_file_path' => $path,
			// Параметр проверяющий формат, если файл .php используем тип false, если текстовый или другой формат, то true
			'text_file_no_php' => true,
			// Форматирование записываемого текста/сообщения
			'text_entry_format' => ''
		);
		if (!isset($logObj)) {
			$logObj  = new JLogLoggerFormattedtext($opt);
		}
		if (!isset($logObjEntry)) {
			$logObjEntry = new JLogEntry(
				'',
				JLog::INFO,
				$category,
				null,
				array()
			);
		}
	}
	
	static function prepareMsg($msg){
		if (is_object($msg) || is_array($msg) ){
			//return var_export($msg, true);
			return self::varDumpToString($msg);
		} else {
			return $msg;
			//return self::varDumpToString($msg);
		}
	}
	
	public static function varDumpToString ($var) {
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		return $result;
	}
	
	public static function logMsg($msg, $isLog, $path='', $filename='common.log'){
		if ($isLog) {
			AvvLog::initObjects($path, $filename, $msg['category'], $lft, $lfte);
			$arrmsg = $msg['msg'];
			if (is_array($arrmsg)) {
				foreach ($arrmsg as $key=>$value) {
					$lfte->message = self::prepareMsg($value);
					$lft->addEntry($lfte);
				}
			}
			elseif (is_string($arrmsg)) {
				$lfte->message = $arrmsg;
				$lft->addEntry($lfte);
			}
		}
	}
}

?>
