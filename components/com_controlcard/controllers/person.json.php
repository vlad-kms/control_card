<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

class ControlcardControllerPerson extends JControllerLegacy
{

	public function __construct(array $config = array())
	{
		parent::__construct($config);
		$this->registerTask('newUser', 'newUser');
		$this->registerTask('changeUser', 'newUser');
	}

	private function jsonData2Array($jsonData) {
		$result = array();
		foreach ($jsonData as $i=>$val)
		{
			$result[$val['name']] = $val['value'];
		}
		return $result;
	}

	/*******************************************************************
	 * Удалить пользователя
	********************************************************************/
	public function delUser() {
		// проверить Token формы
		$this->checkToken('post');

		//$app = JFactory::getApplication();
		$user = JFactory::getUser();
		//$conf = JComponentHelper::getParams('com_controlcard');
		$conf = JComponentHelper::getParams($this->input->get('option', 'com_controlcard'));
		$post=(JFactory::getApplication())->input->post->getArray();

		$islog = $conf->get('log_enable', false, 'boolean');
		$debug = $conf->get('debug_enable', false, 'boolean');
		$cat='delUser';
		$filename = 'person-ajax-delUser.log';

		AvvLog::logMsg(['msg'=>
			                [
				                'function ControlcardControllerPerson->delUser ==========================================================================='
				                , 'islog: '.$islog
				                , 'debug: '.$debug
				                , 'post: '
				                , $post
				                , '===================================='
			                ],
		                'category'=>$cat], $islog, NULL, $filename
		);
		$arrayData = $this->jsonData2Array($post['data']);
		$data = array(
			'id' => $arrayData['id'],
			'nameuser' => $arrayData['name'],
			'login' => $arrayData['username']
		);
		$response = array(
			'status'  => '',
			'error'   => '',
			'message' => '',
			'data'    => $data
		);

		$responseDebug = array('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
		$responseDebug[] = 'Проверить пользователя на право удаления Joomla user';
		//if (true)
		//if (!$user->authorise('core.user.delete', 'com_controlcard'))
		if ( !$user->authorise('core.user.delete', $this->input->get('option', 'com_controlcard')) )
		{
			// нет прав на удаление
			$response['status']  = '401';
			$response['error']   = JText::_('COM_CONTROLCARD_ERROR_NOT_RIGHT');
			$response['message'] = JText::sprintf('COM_CONTROLCARD_ERROR_NOT_DELETE_USER', $arrayData['fio']);
			$responseDebug[] = $response['message'];
			AvvLog::logMsg(['msg'=>
				                [
					                'function ControlcardControllerPerson->delUser ==========================================================================='
					                . '$response[\'error\']  : ' . $response['error']
					                , '$response[\'message\']: ' . $response['message']
					                , '===================================='
				                ],
			                'category'=>$cat], $islog, NULL, $filename
			);
		} else {
			// удалить
			$user = JFactory::getUser($arrayData['id']);
			if ($user->id) {
				// есть такой пользователь
				$r = $user->delete();
				if ($r) {
					PersonHelper::deleteUserFromPerson($arrayData['id']);
					// успешно удален
					$response['status']  = '200';
					$response['error']   = '';
					$response['message'] = JText::sprintf('COM_CONTROLCARD_DELETE_USER', $arrayData['name'] .
						' === ' . $arrayData['username'], $arrayData['id']);
					$responseDebug[] = $response['message'];
					AvvLog::logMsg(['msg'=>
						                [
							                'function ControlcardControllerPerson->delUser ==========================================================================='
							                . '$response[\'error\']  : ' . $response['error']
							                , '$response[\'message\']: ' . $response['message']
							                , '===================================='
						                ],
					                'category'=>$cat], $islog, NULL, $filename
					);
				} else {
					// ошибка удаления.
					$response['status']  = '401';
					$response['error']   = JText::_('COM_CONTROLCARD_ERROR_DELETE_USER');
					$response['message'] = JText::_('COM_CONTROLCARD_ERROR_DELETE_DATABASE');
					foreach ($user->_errors as $err) {
						$response['message'] .= "</br>" . $err;
					}
					$responseDebug[] = $response['message'];
					AvvLog::logMsg(['msg'=>
						                [
							                'function ControlcardControllerPerson->delUser ==========================================================================='
							                . '$response[\'error\']  : ' . $response['error']
							                , '$response[\'message\']: ' . $response['message']
							                , '===================================='
						                ],
					                'category'=>$cat], $islog, NULL, $filename
					);
				}
			} else {
				// нет такого пользователя
				$response['status']  = '401';
				$response['error']   = JText::_('COM_CONTROLCARD_ERROR_DELETE_USER');
				$response['message'] = JText::sprintf('COM_CONTROLCARD_ERROR_NOT_USER', $arrayData['id']);
				$responseDebug[] = $response['message'];
				AvvLog::logMsg(['msg'=>
					                [
						                'function ControlcardControllerPerson->delUser ==========================================================================='
										. '$response[\'error\']  : ' . $response['error']
						                , '$response[\'message\']: ' . $response['message']
						                , '===================================='
					                ],
				                'category'=>$cat], $islog, NULL, $filename
				);
			}
		}

		$responseDebug[] = '--------------------------------------------------------';
		if ($debug) {
			$response['debug'] = $responseDebug;
		}
		echo json_encode($response);
		return;
	}

	/*******************************************************************
	 * Вернуть данные пользователя
	 *******************************************************************/
	public function getUser($id=null) {
		// проверить Token формы
		$this->checkToken('get');

		$responseDebug = array('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
		$responseDebug[] = 'Проверка Token формы УСПЕШНО';

		//$conf = JComponentHelper::getParams('com_controlcard');
		$conf = JComponentHelper::getParams($this->input->get('option', 'com_controlcard'));
		$data = array();
		$response = array(
			'status'  => 200,
			'error'   => '',
			'message' => '',
			'data'    => $data
		);

		$post=(JFactory::getApplication())->input->get->getArray();
		//инициализация логов
		$islog = $conf->get('log_enable', false, 'boolean');
		$debug = $conf->get('debug_enable', false, 'boolean');
		$cat='changeUser';
		$filename = 'person-ajax-changeUser.log';

		if (!$id) {
			$id = (int)$post['id'];
		}
		AvvLog::logMsg(['msg'=>
			                [
				                'function ControlcardControllerPerson->newUser ==========================================================================='
				                , 'islog: '.$islog
				                , 'debug: '.$debug
				                , 'post(get): '
				                , $post
				                , '===================================='
			                ],
		                'category'=>$cat], $islog, NULL, $filename
		);

		//if ($user->id) {
		if (!$id) {
			// неверный параметр

			$response['status']  = '381';
			$response['error']   = JText::sprintf('COM_CONTROLCARD_ERROR_BAD_PARAM', (string) $id);
			$response['message'] = $response['error'];
			$responseDebug[] = $response['error'];
			$responseDebug[] = $response['message'];
			AvvLog::logMsg(
				[
					'msg'=>
						[
					        'function ControlcardControllerPerson->newUser ==========================================================================='
							, '$response[\'status\']  : ' . $response['status']
							, '$response[\'error\']   : ' . $response['error']
							, '$response[\'message\'] : ' . $response['message']
					        , '===================================='
						],
					'category'=>$cat
				], $islog, NULL, $filename
			);
		} else {
			$user = JFactory::getUser($id);
			if ( !is_null($user->id) ) {
				// есть пользователь
				$response['data']['id'] = $user->id;
				$response['data']['email'] = $user->email;
				$response['data']['name'] = $user->name;
				$response['data']['username'] = $user->username;
				//$response['data']['password'] = $user->password;
				$response['message'] = '';
				$responseDebug[] = $response['message'];
				AvvLog::logMsg(
					[
						'msg'=>
							[
								'function ControlcardControllerPerson->newUser ==========================================================================='
								, '$response[\'status\']  : ' . $response['status']
								, '$response[\'message\'] : ' . $response['message']
								, '$data :'
								, $response->data
								, '===================================='
							],
						'category'=>$cat
					], $islog, NULL, $filename
				);
				$responseDebug[] = $response['error'];
				$responseDebug[] = $response['message'];
				AvvLog::logMsg(
					[
						'msg'=>
							[
								'function ControlcardControllerPerson->newUser ==========================================================================='
								, '$response[\'status\']  : ' . $response['status']
								, '$response[\'error\']   : ' . $response['error']
								, '$response[\'message\'] : ' . $response['message']
								, '===================================='
							],
						'category'=>$cat
					], $islog, NULL, $filename
				);
			} else {
				// нет пользователя
				$response['status']  = '382';
				$response['error']   = JText::sprintf('COM_CONTROLCARD_ERROR_NOT_USER', $id);
				$response['message'] = $response['error'];
				$responseDebug[] = $response['error'];
				$responseDebug[] = $response['message'];
				AvvLog::logMsg(
					[
						'msg'=>
							[
								'function ControlcardControllerPerson->newUser ==========================================================================='
								, '$response[\'status\']  : ' . $response['status']
								, '$response[\'error\']   : ' . $response['error']
								, '$response[\'message\'] : ' . $response['message']
								, '===================================='
							],
						'category'=>$cat
					], $islog, NULL, $filename
				);
			}

		}
		$responseDebug[] = '--------------------------------------------------------';
		if ($debug) {
			$response['debug'] = $responseDebug;
		}
		echo json_encode($response);
		return;
	}

	/*******************************************************************
	 * Добавить пользователя
	 *******************************************************************/
	public function newUser() {
		// проверить Token формы
		$this->checkToken('post');

		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		//$conf = JComponentHelper::getParams('com_controlcard');
		$conf = JComponentHelper::getParams($this->input->get('option', 'com_controlcard'));
		$data = array();
		$response = array(
			'status'  => '',
			'error'   => '',
			'message' => '',
			'data'    => null
		);

		$post=(JFactory::getApplication())->input->post->getArray();
			//инициализация логов
		$islog = $conf->get('log_enable', false, 'boolean');
		$debug = $conf->get('debug_enable', false, 'boolean');
		$cat='addUser';
		$filename = 'person-ajax-newUser.log';

		AvvLog::logMsg(['msg'=>
			                [
				                'function ControlcardControllerPerson->newUser ==========================================================================='
				                , 'islog: '.$islog
				                , 'debug: '.$debug
				                , 'post: '
				                , $post
				                , '===================================='
			                ],
		                'category'=>$cat], $islog, NULL, $filename
		);

		$responseDebug = array('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
		$responseDebug[] = 'Проверить пользователя на право добавления Joomla user';
		//if (!$user->authorise('core.user.add', 'com_controlcard'))
		if ( !$user->authorise('core.user.add', $this->input->get('option', 'com_controlcard')) )
		{
			$response['status']  = '401';
			$response['error']   = JText::_('COM_CONTROLCARD_ERROR_NOT_RIGHT');
			$response['message'] = JText::sprintf('COM_CONTROLCARD_ERROR_NOT_ADD_USER', $user->name);
			$response['data'] = array(
				'id' => 0,
				'nameuser' => '',
				'login' => ''
			);
			$responseDebug[] = 'У пользователя нет прав на добавление user Joomla';
			$responseDebug[] = '--------------------------------------------------------';
			if ($debug) {
				$response['debug'] = $responseDebug;
			}
			echo json_encode($response);
			return;
		}

			// данные нового пользователя из POST в массив
//		$dp = $post['data'];
//		$arrayData = array();
//		foreach ($dp as $i=>$val)
//		{
//			$arrayData[$val['name']] = $val['value'];
//		}
		$arrayData = $this->jsonData2Array($post['data']);

			// иницировать данные в объект JUser
		$newUser = new JUser;
		if ( $post['task'] == 'person.newUser' ) {
			$userData = array(
				'name'      => $arrayData['nu_nameuser'],
				'username'  => $arrayData['nu_login'],
				'password'  => $arrayData['nu_password'],
				'password2' => $arrayData['nu_passwordconfirm'],
				'email'     => $arrayData['nu_email'],
				'groups'    => array( $conf->get('new_usertype', 2, 'int') )
			);
		}
		elseif ( $post['task'] == 'person.changeUser' )
		{
			$userData = array(
				'name'        => $arrayData['nu_nameuser']
				, 'username'  => $arrayData['nu_login']
				, 'email'     => $arrayData['nu_email']
				, 'id'        => $arrayData['id']
			);
			if ( !empty($arrayData['nu_password']) ) {
				$userData['password']  = $arrayData['nu_password'];
				$userData['password2'] = $arrayData['nu_passwordconfirm'];
			}
		}
		$rb = $newUser->bind( $userData );
		AvvLog::logMsg(['msg'=>
			        [
				        'NEW USER ===========================',
				        'newUser->id: '.$newUser->id,
				        'newUser->name: '.$newUser->name,
				        'newUser->password: '.$newUser->password,
				        'newUser->password2: '.$newUser->password2,
				        'res bind: '
				        , $rb
				        , '===================================='
			        ],
			'category'=>$cat], $islog, NULL, $filename
		);
		// запись объекта JUser  в БД
		$rs = $newUser->save();
		if ( $rs ) {
			$response['status'] = '200';
			$data['id'] = $newUser->id;
			$data['nameuser'] = $newUser->name;
			$data['login'] = $newUser->username;
			AvvLog::logMsg(['msg'=>
				                [
					                'Успешно зарегистрирован: '.$newUser->username,
					                'newUser->id: '.$newUser->id
				                ],
			                'category'=>$cat], $islog, NULL, $filename);
			$response['message'] = 'User ' . $newUser->username . ' success registered!';
			$response['data'] = $data;

		} else {
			// запись с ошибкой
			$data['id'] = 0;
			$data['nameuser'] = '';
			$data['login'] = '';
			$response['error'] = $newUser->_errors[0];
			$response['message'] = 'User ' . $newUser->username . ' NOT success registered!';
			foreach ($newUser->_errors as $err) {
				$response['message'] .= "</br>" . $err;
			}
			$response['data'] = $data;
			AvvLog::logMsg(['msg'=>'Не зарегистрирован: '.$newUser->username, 'category'=>$cat], $islog, NULL, $filename);
			AvvLog::logMsg(
				[
					'msg'=>[
						'JUser->errors'
						, $newUser->_errors
					],
					'category'=>$cat
				], $islog, NULL, $filename);
		}
		AvvLog::logMsg(['msg'=>'function addUserFrom1C END =======================================================================',
		                'category'=>$cat], $islog, NULL, $filename);

		$responseDebug[] = '--------------------------------------------------------';
		if ($debug) {
			$response['debug'] = $responseDebug;
		}
		echo json_encode($response);
		return;
	}
}
