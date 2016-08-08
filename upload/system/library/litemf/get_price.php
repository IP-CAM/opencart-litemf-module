<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	// создаем объект
	$obCache = new CPHPCache;
	// время кеширования - сутки
	$life_time = 60 * 60 * 24;
	$cache_id = $kladr * $weight;

//pre(array($kladr, $weight * 1000));
	// если кэш есть и он ещё не истек то
	if($obCache->InitCache($life_time, $cache_id, "/LITE_MF_PRICE/")) :
	    // получаем закешированные переменные
	    $getDeliveryPrice = $obCache->GetVars();
	else :
		//------------------------------------------------------------------------------------
		// подключим класс CjsonLiteMF
		//------------------------------------------------------------------------------------

		$arParamsDeliveryPrice = array(
	      "country_from"	=>	373,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	= 3287 "US"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    = 3159 "RU"
	      "weight"				=>	$weight * 1000,								//	Вес посылки, г
	      "zone"					=>	$kladr,		//"7500000000000",		//	КЛАДР или индекс
	      "filter"				=>	array(
		        "delivery_method"		=>	array(66,85),		//	ID метода(ов) доставки (см. DeliveryMethod)
	      )
		);
		$getDeliveryPrice = $LiteMF->Request("getDeliveryPrice", $arParamsDeliveryPrice);

	endif;

	// начинаем буферизирование вывода
	if($obCache->StartDataCache()):
	    // записываем предварительно буферизированный вывод в файл кеша
	    $obCache->EndDataCache($getDeliveryPrice);
	endif;

//pre($getDeliveryPrice);

/*
	$data=array(
      "id" => time(),
      "method" => "getDeliveryMethod",
      "params" => array(
			"country_from" => 373,
			"country_to" => 3159
      )
  );
  $options = array(
      'http' => array(
          'method'  => 'POST',
          'header'  => array(
              'POST /v2/rpc HTTP/1.1',
              'Host: api.litemf.com',
              'Content-type: application/json',
              'X-Auth-Api-Key: ec8f703be71f305188317259bb574635a21f0509',
              'Cache-Control:no-cache'
              ),
          'content' => json_encode($data),
      ),
  );
  $context = stream_context_create($options);
  $getDeliveryMethod = json_decode(file_get_contents('http://api.litemf.com/v2/rpc', false, $context),true);
*/

/*
	$data = array(
      "id" => time(),
      "method" => "getDeliveryPrice",
      "params" => array(
			"country_from" => 373,
			"country_to" => 3159,
			"weight" => $weight,
			"zone" => $kladr,
          "filter"=>array(
              "delivery_method"=>array(66)
          )
      )
  );
//pre($data);
  $options = array(
      'http' => array(
          'method'  => 'POST',
          'header'  => array(
              'POST /v2/rpc HTTP/1.1',
              'Host: api.litemf.com',
              'Content-type: application/json',
              'X-Auth-Api-Key: ec8f703be71f305188317259bb574635a21f0509',
              'Cache-Control:no-cache'
              ),
          'content' => json_encode($data),
      ),
  );
  $context = stream_context_create($options);
  $getDeliveryPrice = json_decode(file_get_contents('http://api.litemf.com/v2/rpc', false, $context),true);
*/


























?>