<?php
/*
	author: Eugene Bos
	website: http://thegp.ru
	Good luck!:)
*/

class api_smartresponder {
    public $config, $lists, $error = NULL;
    public $debug = FALSE, $debug_output = '';

    function __construct($config, $lists)
    {
        $this->config = $config;
        $this->lists = $lists;
    }


    // Получает данные подписчика по email, может попытаться сделать это прямо из конкретного списка рассылок
    // при запросе с конкретным списком в качестве даты подписки возвращает дату подписки на этот список
    function getSubscriber($list = NULL, $email)
    {
    	$this->debugLog('getSubscriber start');

		$data = array(
		    'action' => 'list',
		    'search[email]' => $email,
		);

		// если задан конкретный список
		if (NULL !== $list) {
			$data['search[deliveries_ids]'] = $this->lists[$list];
		} 

		$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);

		if (FALSE !== $results) {

			if (isset($results['list']['elements'][0])) {
				//return $results->list->elements[0];
				/*
				[deliveries] => Array
			    (
			        [0] => Array
			            (
			                [id] => 213806
			                [date_added] => 02.11.2012 12:10:40
			            )*/

				// если указан лист подписчика - то возвращаем дату добавления именно в этот лист если возможно
				if (NULL !== $list) {

					//print_r($results['list']['elements'][0]['deliveries']);
					//exit();

					foreach ($results['list']['elements'][0]['deliveries'] as $key => $that_list) if ($this->lists[$list] == (int)$that_list['id']) {

						$this->debugLog('add date replaced from ' . $results['list']['elements'][0]['date_added'] . ' to' . $results['list']['elements'][0]['deliveries'][$key]['date_added']);

						$results['list']['elements'][0]['date_added'] = $results['list']['elements'][0]['deliveries'][$key]['date_added'];

					}
				} 

				return $results['list']['elements'][0];
			} else {
				//exit('!!!');
				return FALSE;
			}
		} else {
			return FALSE;
		}
    }


    // добавление подписчика вообще или в конкретный список
    // умееет корректно работать если подписчик уже добавлен
    function addSubscriber($list = NULL, $s_data)
    {
    	$this->debugLog('addSubscriber start');

    	if (FALSE !== $subscriber = $this->getSubscriber(NULL, $s_data['email'])) {

    		$this->debugLog('subscriber exists:');
    		$this->debugLog($subscriber);
//exit($s_data['email']);
    		// если подписчик уже у нас есть - просто добавляем его в список рассылки
    		if (NULL !== $list) {

				$data = array(
				    'action' => 'link_with_delivery',
			   		'search[email]' => $s_data['email'],

				    'delivery_id' => $this->lists[$list],
				);

				$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);

    			$this->debugLog('Add in list result:');
    			$this->debugLog($results);
    		}

			// и обновляем данные если их достаточно и запись не рид онли
			if (1 != count($s_data)) {

				if (0 == $subscriber['f_readonly']) {
					/*$data = array(
				   		'id' => $subscriber['id'],
					);
					if (isset($s_data['name'])) $data['name'] = $s_data['name'];
					if (isset($s_data['hop'])) $data['extra_s1'] = $s_data['hop'];
					if (isset($s_data['subscribed'])) $data['extra_s2'] = $s_data['subscribed'];*/
					
					$results = $this->updateSubscriber($list, $s_data);

					$this->debugLog('Results of updating data:');
					$this->debugLog($results);

				} else {

					$this->debugLog('Subsciber data read only so updating data is impossible');

				}


			} else {
				$this->debugLog('Not enought details for update data: ignor');
			}



			return $results;

    	} else {

    		$this->debugLog('subscriber not found');

			$data = array(
			    'action' => 'create',
			    //'search[email]' => $email,
			    //'search[deliveries_ids]' => $this->lists[$list],
			    //'search[f_included_in_deliveries]' => 1,

			    'first_name' => ((!isset($s_data['first_name']) && isset($s_data['name'])) ? $s_data['name'] : $s_data['first_name']),
			    'email' => $s_data['email'],
			    'last_name' => $s_data['last_name'],
			    'extra_s1' => $s_data['extra_s1'],
                'birth_day' => $s_data['birthday'],
                'birth_month' => $s_data['birth_month'],
                'birth_year' => $s_data['birth_year']
			);

            $this->debugLog($data);
            
            if (NULL !== $list) {
				$data['delivery_id'] = $this->lists[$list];
			}

			if (isset($s_data['hop'])) $data['extra_s1'] = $s_data['hop'];
			if (isset($s_data['subscribed'])) $data['extra_s2'] = $s_data['subscribed'];
			if (isset($s_data['bought'])) $data['extra_s3'] = $s_data['bought'];
			
			$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);
			$this->debugLog('Results of adding in list:');
			$this->debugLog($results);

			if (FALSE !== $results) {

				if (isset($results['id'])) {
					//return $results->list->elements[0];
					return $results['id'];
				} else {
					//exit('!!!');
					return FALSE;
				}
			} else {
				return FALSE;
			}

    	}
    }


    // обновлеяет данные подписчка
    // если нужно может подписать на конкретный список
    function updateSubscriber($list = NULL, $s_data)
    {
    	$this->debugLog('updateSubscriber start');

		// если подписчик уже у нас есть - просто добавляем его в список рассылки
		if (NULL !== $list && isset($s_data['email'])) {

			//if ($s_data['email'])

			$data = array(
			    'action' => 'link_with_delivery',
		   		'search[email]' => $s_data['email'],

			    'delivery_id' => $this->lists[$list],
			);
			$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);

    		$this->debugLog('Adding in list results:');
    		$this->debugLog($results);
		}

		// если заполнено только поле емайла, а другие не заполнены, значит запрос был для добавления человека в список, и обновление его данных игнорируем
    	//$this->debugLog('s_data:');
		//$this->debugLog($s_data);
		//$this->debugLog(count($s_data));

		if (1 != count($s_data)) {

			$subscriber = $this->getSubscriber(NULL, $s_data['email']);

			if (FALSE !== $subscriber) {
				if (0 == $subscriber['f_readonly']) {
					$data = array(
					    'action' => 'update',
					);
					$data = array_merge($data, $s_data);

					// ::TODO:: поддержка ФИО - разбитие на first last и middle
					if (isset($data['name'])) {
						$data['first_name'] = $data['name'];
						unset($data['name']);
					}
					if (isset($data['hop'])) {
						$data['extra_s1'] = $data['hop'];
						unset($data['hop']);
					}
					if (isset($data['subscribed'])) {
						$data['extra_s2'] = $data['subscribed'];
						unset($data['subscribed']);
					}
					if (isset($data['bought'])) {
						$data['extra_s3'] = $data['bought'];
						unset($data['bought']);
					}
					unset($data['email']);
					$data['id'] = $subscriber['id'];


					$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);

		    		$this->debugLog('Changing subscriber data results:');
		    		$this->debugLog($results, TRUE);

					if (FALSE !== $results) {

						if (isset($results['id'])) {
							//return $results->list->elements[0];
							return $results['id'];
						} else {
							//exit('!!!');
							return FALSE;
						}
					} else {
						return FALSE;
					}
				} else {
	    			$this->debugLog('Changing subscriber data forbidded (item is readonly)');

					return FALSE;
				}
			} else {
    			$this->debugLog('Ignoring of changing subscriber data (subscriber not found)');

				return FALSE;
			}


		} else {
    		$this->debugLog('Ignoring of changing subscriber data (data its not exists)');

			return $results;
		}

    }

    function addSubscribe($list = NULL, $email)
    {
    	$this->debugLog('changeSubscribe start');

		// если подписчик уже у нас есть - подписываем его на указанную рассылку
		if (NULL !== $list && $email) {
			$data = array(
			    'action' => 'on_delivery',
		   		'search[email]' => $email,

			    'delivery_id' => $this->lists[$list],
			);
			$results = $this->query('http://api.smartresponder.ru/subscribe.html', $data);

    		$this->debugLog('Adding in list results:');
    		$this->debugLog($results);
		}else{
            $this->debugLog('Error to change Subscribe');
        }
    }


    // просто исключает подписчика из выбранной рассылки но не удаляет его из общего списка
    function deleteSubscriber($list, $email)
    {
    	$this->debugLog('deleteSubscriber start');

		$data = array(
		    'action' => 'unlink_with_delivery',
		    'delivery_id' => $this->lists[$list],

		   	'search[email]' => $email,
		);

		$results = $this->query('http://api.smartresponder.ru/subscribers.html', $data);

		$this->debugLog('Results of deleting from the list:');
		$this->debugLog($results);

		if (FALSE !== $results && 1 == $result['result']) {

			return TRUE;

		} else {
			return FALSE;
		}
    }










    function query($url, $data) {
    	$data = array_merge($this->config, $data);
    	//print_r($data);

    	$content = http_build_query($data);

		$options = array(
			'http' => array(
				'method'  => 'POST',
				'content' => $content,
	           	'header' => "Connection: close\r\n".
	                        "Content-Type: application/x-www-form-urlencoded\r\n".
	                        "Content-Length: ".strlen($content)."\r\n",
				)
		);
		$context  = stream_context_create($options);
		$results = file_get_contents($url, false, $context);

		return $this->objectToArray($this->saveErrors(json_decode($results)));
    }

    function saveErrors($results) {
    	//print_r($results);
    	if (isset($results->error)) {
    		$this->error = $results->error->message . '(code:' . $results->error->code . ')';
//exit('!');
    		return FALSE;
    	} else {
    		$this->error = NULL;

    		return $results;
    	}
    }

    /*function toUtf($data) {

    	if (TRUE === is_array($data)) {
    		foreach ($data as $key => $value) {

    			if (TRUE === is_array($value)) {
    				$data[$key] = $this->toUtf($value);
    			} else {
    				$data[$key] = $this->toUtfString($value);
    			}

    		}
    	} else {

    		$data = $this->toUtfString($value);

    	}

    	return $data;
    }

    function toUtfString($data) {

    	return iconv('cp1251', 'UTF-8', $data);

    }*/


    function debugLog($text, $is_results = FALSE) {
    	if (TRUE === $this->debug) { 
    		if (is_string($text)) {
    			
    		} elseif (is_array($text)) {
    			ob_start();
    			print_r($text);
    			$text = ob_get_clean();
    		} elseif (is_object($text)) {
    			$text = json_encode($text);
    		} else {
    			if (TRUE === $is_results && NULL !== $this->error) {
    				// добавляем лог ошибки если она есть
    				$postfix = '(return error:' . $this->error . ')';
    			}

    			ob_start();
    			var_dump($text);
    			$text = ob_get_clean();
    		}
    		
    		$this->debug_output .= trim($text) . ';' . ((isset($postfix)) ? $postfix : '') .  "<br />\r\n";
    	}
    }


	// stdClass в многомерный массив
	function objectToArray($d) {

		if (is_object($d)) {
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			return array_map(array(&$this, 'objectToArray'), $d); // __FUNCTION__
		}
		else {
			return $d;
		}
	}
}