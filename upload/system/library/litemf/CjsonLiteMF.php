<?php
class jsonLiteMF {
    private $id;
    private $url;
	private $header;

	/** Error code */
	private $codeError = array(
		'1001'	 => "Доступ запрещен",
		'1002'	 => 'Некорректный заголовок аутентификации',
		'1100'	 => 'Пропущено обязательное поле фильтра',
		'1103'	 => 'Пропущен обязательный параметр метода',
		'1104'	 => 'Некорректное значение параметра',
		'1201'	 => 'Сущность не найдена',
		'1087'	 => 'Некорректные данные',
		'2033'	 => 'Пропущены обязательные поля',
		'2044'	 => 'Значение не может быть структурой',
		'2045'	 => 'Значение не может быть скалярным',
		'2055'	 => 'Пустая коллекция',
		'2022'	 => 'Некорретный тип значения',
		'1400'	 => 'Внутренняя ошибка сервера',
		'1401'	 => 'Ошибка импорта',
		'-32700' => 'Некорректный метод запроса; Некорректный или пустой заголовок Content-Type; Ошибка JSON структуры.',
		'-32600' => 'Пустой id в JSON структуре; Пустой method в JSON структуре; Пустой params в JSON структуре.',
		'-32602' => 'Запрошенный метод не найден; Ошибка вызова метода.'
	);

	/** LiteMf methods */
	private $arMethod = [
		'getCountry'			=> 'метод получени списка стран',
		'getDeliveryMethod'		=> 'метод получения списка методов доставки',
		'getDeliveryPrice'		=> 'получение цены доставки',
		'getDeliveryPointList'  => 'получение списка доступных ПВЗ',

		'createAddress'	=> 'метод создания адреса доставки',
		'editAddress'	=> 'метод редактирования адреса доставки', // <<< в описании "editAddrress" !!!
		'getAddress'	=> 'получение адреса доставки',

		'createIncomingPackage' => 'метод создания входящей посылки',
		'editIncomingPackage'	=> 'метод для редактирования входящей посылки',
		'getIncomingPackage'	=> 'получение списка входящих посылок',

		'createOutgoingPackage'	=> 'создание исходящей посылки',
		'editOutgoingPackage'	=> 'редактирование исходящей посылки',
		'getOutgoingPackage'	=> 'получение списка исходящих посылок',

		'addIncomingPackageToOutgoingPackage'		=> 'добавление входящих посылок в состав исходящей',
		'removeIncomingPackageFromOutgoingPackage'	=> 'удаление входящих посылок из состава исходящих',
		'disbandOutgoingPackage'					=> 'расформирование исходящей посылки',

		'getIncomingPackagePhotoLinks'	=> 'получение списка ссылок на фотографии входящей посылки',
		'allowSendOutgoingPackage'		=> 'разрешить отправку отложенной исходящей посылки',

	];

	/** Status Incoming Package */
	private $codeStatusIncomingPackage = array(
		'awaiting_arrival' => 'ожидается на складе',
		'in_stock'		   => 'на складе',
		'removed'		   => 'удалена'
	);

	/** Status Outgoing Package */
	private $codeStatusOutgoingPackage = array(
		'new'				  => 'Сформирована пользователем',
		'in_processing_queue' => 'В очереди на упаковку',
		'processing'		  => 'Упаковывается',
		'awaiting_payment'	  => 'Ожидается оплата',
		'awaiting_sending'	  => 'Ожидается отправка',
		'sent'				  => 'Отправлена',
		'received'			  => 'Получена',
		'wait_disband'		  => 'Ожидает расформирования',
		'disbanded'			  => 'Расформирована',
		'wait_recycle'		  => 'Ожидает утилизации',
		'recycled'			  => 'Утилизирована',
		'removed'			  => 'Удалена'
	);

	/**
	 * jsonLiteMF constructor.
	 * @param string $host
	 * @param string $auth
	 */
	public function __construct($host = 'api.litemf.com', $auth = 'ec8f703be71f305188317259bb574635a21f0509')
	{
        $this->id =	mt_rand(11111, 99999);
        $this->url = 'http://'.$host.'/v2/rpc';
        $this->header = [
            'POST /v2/rpc HTTP/1.1',
            'Host: '.$host,
            'Content-type: application/json',
            'X-Auth-Api-Key: '.$auth,
            'Cache-Control: no-cache',
		];
    }

	/**
	 * @param $method
	 * @return array|bool
	 */
	public function getMethod($method)
	{
		if ($method == '') {
			return $this->arMethod;
		}
		if (isset($this->arMethod[$method]) && $this->arMethod[$method] != '') {
			return $this->arMethod[$method];
		}

		return false;
	}

	/**
	 * @param string $code
	 * @return array|bool
	 */
	public function getError($code = '')
	{
		if ($code == '') {
			return $this->codeError;
		}
		if (isset($this->codeError[$code]) && $this->codeError[$code] != '') {
			return $this->codeError[$code];
		}

		return false;
	}

	/**
	 * @param string $status
	 * @return array|bool
	 */
	public function getStatusIncomingPackage($status = '')
	{
		if($status == '' ) {
			return $this->codeStatusIncomingPackage;
		}
		if(isset($this->codeStatusIncomingPackage[$status]) && $this->codeStatusIncomingPackage[$status] != '') {
			return $this->codeStatusIncomingPackage[$status];
		}

		return false;
	}

	/**
	 * @param string $status
	 * @return array|bool
	 */
	public function getStatusOutgoingPackage($status = ''){
		if( $status == '' ) {
			return $this->codeStatusOutgoingPackage;
		}
		if(isset($this->codeStatusOutgoingPackage[$status]) && $this->codeStatusOutgoingPackage[$status] != '') {
			return $this->codeStatusOutgoingPackage[$status];
		}
		return false;
	}


	/**
	 * @param $method
	 * @param array $arParams
	 * @return mixed|null|string
	 * @throws Exception
	 */
    public function Request($method, $arParams = array())
	{
        if (!is_scalar($method)) throw new Exception('Имя метода не имеет скалярное значение!');
        if (!isset($this->arMethod[$method]) && empty($this->arMethod[$method])) throw new Exception('Метод имеет неопределенную функцию!');
        if (!is_array($arParams)) throw new Exception('Параметры должны быть даны в виде массива!');

        $currentId	= $this->id;
        $this->id 	=	mt_rand(100000, 999999);

        $request = [
            'id' => $currentId,
            'method' => $method,
            'params' => $arParams,
        ];

        $options = [
        		'http' => [
                'method'  => 'POST',
                'header'  => $this->header,
                'content' => json_encode($request),
            ],
        ];
				//--------------------------------------------------------------------------------
        $context = stream_context_create($options);
        if ($fp = fopen($this->url, 'r', false, $context)) {
            $response = '';
            while($row = fgets($fp)) {
                $response .= trim($row)."\n";
            }
            $response = json_decode($response, true);
        } else {
            throw new Exception('Unable to connect to '.$this->url);
        }
				//--------------------------------------------------------------------------------
        if ($response['id'] != $currentId)
            throw new Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');

		return $response;
    }
}

