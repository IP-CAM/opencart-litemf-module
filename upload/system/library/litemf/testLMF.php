<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Тестовая страничка");
?>

<?

	//------------------------------------------------------------------------------------
	// подключим класс CjsonLiteMF
	//------------------------------------------------------------------------------------
	require_once($_SERVER["DOCUMENT_ROOT"].'/local/classes/CjsonLiteMF.php');
	$host = 'api.litemf.com';
	$LiteMF = new jsonLiteMF($host);

/*
echo('Тестируем функцию «getAddress»');
		$arParams = array(
	      "filter"	=>	array(
            "id"		=>	array( 136560 ),
						"limit"	=>	999999,
	      )
		);
		pre($arParams);
		pre($LiteMF->Request("getAddress", $arParams));
echo('<hr>');
*/
/*
	echo('Тестируем функцию «getOutgoingPackage»');
		$arParams = array(
				"filter"	=>	array(
//						"id"								=>	array( 271567, 273493, 273721, 273731, 273766, 273773, 273776, 273780, 273912 ),
//						"outgoing_package"	=>	169200,
//						"warehouse"					=>	4,
//						"partner_fid"				=>	"ZOM169",
//						"status"						=>	"waiting_in_stock", // waiting_in_stock, in_stock, sent, removed
						"limit"							=>	999999,
				)
		);
		pre($arParams);
		pre($LiteMF->Request("getOutgoingPackage", $arParams));
	echo('<hr>');
		die('<hr>');
*/
/*
	echo('Тестируем функцию «getDeliveryPointList»');
		$arParams = array(
	      "filter"				=>	array(
            "kladr"	=>	"3600000100000",
						"limit"	=>	999999,
	      )
		);
		pre($arParams);
		$arResult = $LiteMF->Request("getDeliveryPointList", $arParams);

//pre($arResult['result']['data']);
$arPVZ = array();
foreach($arResult['result']['data'] as $ar_pvz){
	$arPVZ[$ar_pvz['address']] = $ar_pvz['id'];
}
pre($arPVZ);
*/

/*
		echo('Тестируем функцию «getDeliveryMethod»');
		$arParams = array(
	      "country_from"	=>	3287,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	=>	3287 "US"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    =>	3159 "RU"
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryMethod", $arParams));
		echo('<hr>');
		$arParams = array(
	      "country_from"	=>	373,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	=>	373	"DE"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    =>	3159 "RU"
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryMethod", $arParams));
		die('<hr>');
*/
/*
    [data] => Array(
        [0] => Array(
            [id] => 85
            [name] => Shiptor Air
            [delivery_currency] => EUR
        )
        [1] => Array(
            [id] => 66
            [name] => Express
            [delivery_currency] => EUR
        )
    )
*/
/*
	echo('Тестируем функцию «getOutgoingPackage»');
		$arParams = array(
				"filter"	=>	array(
//						"id"								=>	213357, //array( 213357 ),
//						"outgoing_package"	=>	213357,
						"warehouse"					=>	4,
						"partner_fid"				=>	198,
//						"status"						=>	"waiting_in_stock", // waiting_in_stock, in_stock, sent, removed
						"limit"							=>	999999,
				)
		);
		pre($arParams);
		pre($LiteMF->Request("getOutgoingPackage", $arParams));
	die('<hr>');
*/

	//------------------------------------------------------------------------------------
	//	Подключаем КЛАДР
	//------------------------------------------------------------------------------------
	include $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/kladr.php";
	$apiKladr = new Kladr\Api('567152cc0a69de80658b45bd', '86a2c2a06f1b2451a87d05512cc2c3edfdf41969');

	//------------------------------------------------------------------------------------
	//	Получаем заказы со статусом "V" - Выкуплен и если все OK переводим в "М" - Ожидание товара
	//------------------------------------------------------------------------------------
  $arOrders = array();
  $arSelectOrder = array("ID","ACCOUNT_NUMBER","USER_ID","PAYED","STATUS_ID","ALLOW_DELIVERY","CANCELED","LOCK_STATUS","DEDUCTED","PRODUCT_ID","PRODUCT_PRICE_ID", "COMMENTS","PRICE","CURRENCY","DISCOUNT_VALUE","SUM_PAID","PAY_SYSTEM_ID","DELIVERY_ID","PRICE_DELIVERY","ADDITIONAL_INFO","USER_DESCRIPTION");
  $arFilterOrder = array( "LID"=>"zm", /*"ID" => array(198),*/  "CANCELED"=>'N', 'PAYED'=>'Y', 'DEDUCTED'=>'Y', "ALLOW_DELIVERY"=>'Y',  /* "LOCK_STATUS"=>'green', */ "@STATUS_ID" => array("V") );
  $res = CSaleOrder::GetList( Array("ID"=>"DESC"), $arFilterOrder, false, false, array());
  while($arOrder = $res->Fetch()){
		//$arOrder["STATUS"] = CSaleStatus::GetByID($arOrder["STATUS_ID"]);
		//----------------------------------------------
		//	Получаем свойства заказа
		//----------------------------------------------
    $db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
		while ($arProps = $db_props->Fetch()){
/*
			if($arProps['CODE'] == 'PVZ'){
				if( CSaleOrderPropsValue::Update($arProps["ID"], array("VALUE"=>$arPVZ[$arProps["VALUE"]])) )
					echo('ЗАКАЗ #'.$arOrder["ID"].' PVZ #'.$arProps["ID"].' был '.$arProps["ID"].' а стал '.$arPVZ[$arProps["VALUE"]]);
				else
					echo('ОШИБКА! ЗАКАЗ #'.$arOrder["ID"].' PVZ #'.$arProps["ID"].' был '.$arProps["ID"].' не стал '.$arPVZ[$arProps["VALUE"]]);
			}
*/
			$arOrder["PROPERTIES"][$arProps["CODE"]] = $arProps["VALUE"];
			if($arProps['CODE'] == 'LOCATION'){

				$cache_id = $arProps['VALUE'];

				// если кэш есть и он ещё не истек то
				if($obCache->InitCache($life_time, $cache_id, "/Kladr/")) :
				    // получаем закешированные переменные
				    $kladrResult = $obCache->GetVars();
				else :
				    // иначе обращаемся к базе
						$arVal = CSaleLocation::GetByID($cache_id, LANGUAGE_ID);
						$arOrder['PROPERTIES']['DELIVERY']['COUNTRY'] = $arVal['COUNTRY_NAME'];
						$arOrder['PROPERTIES']['DELIVERY']['REGION'] = $arVal['REGION_NAME'];
						$arOrder['PROPERTIES']['DELIVERY']['CITY'] = $arVal['CITY_NAME'];
				    // Формирование запроса
						if(!empty($arVal['CITY_NAME'])) $string = $arVal['REGION_NAME'].", Город ".$arVal['CITY_NAME']; // $arVal['CITY_NAME']." город";
						else $string = $arVal['REGION_NAME'];
				    // Делаем запрос
				    $queryKladr = new Kladr\Query();
				    $queryKladr->ContentName	= $string;
				    $queryKladr->OneString		= TRUE;
				    $queryKladr->Limit				= 1;
				    // Получение данных в виде ассоциативного массива
				    $kladrResult = $apiKladr->QueryToArray($queryKladr);
				endif;

				// начинаем буферизирование вывода
				if($obCache->StartDataCache()):
				    // записываем предварительно буферизированный вывод в файл кеша
				    $obCache->EndDataCache($kladrResult);
				endif;

				if(!$kladrResult[0]['id']){
					$arOrder['PROPERTIES']['DELIVERY']['KLADR'] = 0;
				}else{
					$arOrder['PROPERTIES']['DELIVERY']['KLADR'] = $kladrResult[0]['id'];
					$arOrder['PROPERTIES']['DELIVERY']['KLADR_RESULT'] = $kladrResult;
				}

				unset($queryKladr);
				unset($kladrResult);
				unset($arVal);
			}
			unset($arProps);
		}
		//----------------------------------------------
		//	Получаем свойства доставки
		//----------------------------------------------
		$saleOrder = Bitrix\Sale\Order::load($arOrder['ID']);
		$shipmentCollection = $saleOrder->getShipmentCollection();
		foreach ($shipmentCollection as $shipment)
		{
			if($shipment->isSystem()) continue;
			$arOrder["SHIPMENT"]["STATUS_ID"] = $shipment->getField('STATUS_ID');
			$arOrder["SHIPMENT"]["TRACKING_NUMBER"] = $shipment->getField('TRACKING_NUMBER');
			$arOrder["SHIPMENT"]["DELIVERY_DOC_NUM"] = $shipment->getField('DELIVERY_DOC_NUM');
			$arOrder["SHIPMENT"]["DELIVERY_DOC_DATE"] = $shipment->getField('DELIVERY_DOC_DATE');
			$arOrder["SHIPMENT"]["COMMENTS"] = $shipment->getField('COMMENTS');
		}
		$arOrder["isShipped"] = $saleOrder->isShipped();;

		//----------------------------------------------
		//	Получаем содержимое заказа
		//----------------------------------------------
		$arBasketSelect = array("ID","FUSER_ID","ORDER_ID","PRODUCT_ID","PRODUCT_PRICE_ID","NAME","PRICE","CURRENCY","BASE_PRICE","WEIGHT","QUANTITY","DELAY","CAN_BUY","NOTES","DETAIL_PAGE_URL","PRODUCT_XML_ID", "DISCOUNT_PRICE","DISCOUNT_NAME","DISCOUNT_VALUE","DISCOUNT_COUPON","MEASURE_NAME");
		$resBasket = CSaleBasket::GetList(array(), array("ORDER_ID" => $arOrder['ID']), false, false, $arBasketSelect); // ID заказа
		while($arItem = $resBasket->Fetch()) {

			// Выведем все свойства элемента корзины с кодом $basketID
			$db_res = CSaleBasket::GetPropsList( array(), array("BASKET_ID" => $arItem['ID']) );
			while($ar_res = $db_res->Fetch()) $arItem["PROPERTIES"][$ar_res["CODE"]] = $ar_res["VALUE"];

#			$arItem["BARCODE"] = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $arItem['PRODUCT_ID']))->GetNext();


			$arFilter = array( "IBLOCK_TYPE"=>"site2_catalog", "ACTIVE" => "Y", "IBLOCK_ID" => 20, "ID" => $arItem['PRODUCT_ID'] );
			$arItem["ITEM_SKU"] = CIBlockElement::GetList( array(), $arFilter, false, false, array("ID","CODE","PROPERTY_CML2_LINK","PROPERTY_BD_size","PROPERTY_BD_barcode") )->Fetch();
			$arItem["ITEM_ID"] = CIBlockElement::GetByID($arItem['ITEM_SKU']["PROPERTY_CML2_LINK_VALUE"])->Fetch();

#			$arItem["ITEM_SKU"]["PROPS"] = CIBlockElement::GetByID($arItem['PRODUCT_ID']);

#			$arItem["PRODUCT"] = CCatalogProduct::GetByID($arItem['PRODUCT_ID']);
			$arItem['PRODUCT_PRICE'] = CPrice::GetByID($arItem['PRODUCT_PRICE_ID']);
			$arItem['EN_NAME'] = CUtil::translit($arItem['NAME'], "ru", array("replace_space"=>" ", "replace_other"=>" ", "change_case"=>false ));
			$arOrder['ITEMS'][] = $arItem;
			unset($arItem);
		}

		//----------------------------------------------
		//	Получаем данные пользователя
		//----------------------------------------------
    $arOrder["USER"] = CUser::GetByID($arOrder["USER_ID"])->Fetch();
		//----------------------------------------------

		//----------------------------------------------
		//	Полная проверка всех свойств Заказа
		//----------------------------------------------
		if( !$arOrder["isShipped"] ) continue;
		if( $arOrder["SHIPMENT"]["STATUS_ID"] != "DF" ) continue;
		if( empty($arOrder["SHIPMENT"]["TRACKING_NUMBER"]) ) continue;
		if( empty($arOrder["PROPERTIES"]["EMAIL"]) ) continue;
		if( empty($arOrder["PROPERTIES"]["PHONE"]) ) continue;
		if( intval($arOrder["PROPERTIES"]["PVZ"]) <= 0 ) continue;
		if( $arOrder["PROPERTIES"]["DELIVERY"]["KLADR"] == 0 ) continue;
		if( $arOrder["USER"]["UF_P_CHECK"] != 9 ) continue;

		//----------------------------------------------
		$arOrders[$arOrder["ID"]] = $arOrder;
		//----------------------------------------------
		unset($arOrder);

	}


echo("Найдено: ".count($arOrders).'<hr>');
/*
echo('<hr>');
pre($arOrders);
die('<hr>Happy end!');
*/


$nnn = 1;
foreach($arOrders as $idOrd => $arOrder){


	//----------------------------------------------
	// Пользователь и его паспорт
	//----------------------------------------------
	$arUser = $arOrder["USER"];
	if(intval($arUser["UF_P_LITEMF_ID"]) > 0)
	{
		pre('Уже есть "UF_P_LITEMF_ID" = '. $arUser["UF_P_LITEMF_ID"] . '<br>');
	}
	else
	{
		$phone = preg_replace('![^\d]*!','',$arOrder['PROPERTIES']['PHONE']);
		$arParams = array(
				"data"	=>	array(										//	+
						"format"		=>	"separated",			//	+	Формат адреса: международный('international') или разделенный('separated')
						"name"			=>	array(						//	+
								"last_name"		=>	$arUser['UF_P_FAMIL'],		//	+
								"first_name"	=>	$arUser['UF_P_NAME'],			//	+
								"middle_name"	=>	$arUser['UF_P_OTCH']			//	+
						),
						"delivery_country"	=>	3159,			//	+ 3159 - Россия
						"first_line"=>	array(						//	+
								"street"		=>	$arUser['UF_P_STREET'],
								"house"			=>	$arUser['UF_P_HOUSE']
						),
						"flat"			=>	( $arUser['UF_P_APARTMENT'] ? $arUser['UF_P_APARTMENT'] : "" ),							//	-
						"city"			=>	$arUser['UF_P_CITY'],				//	+
						"region"		=>	$arUser['UF_P_REGION'],	//	-
						"zip_code"	=>	$arUser['UF_P_INDEX'],					//	+
						"phone"			=>	array(						//	-
								"country"		=>	7,											//substr($phone, 0, 1), <== пока просто "7" для России
								"code"			=>	substr($phone, 1, 3),
								"number"		=>	substr($phone, 4, 7)
						),
						"email"			=>	$arOrder['PROPERTIES']['EMAIL'],//	-
						"passport"	=>	array(	//	+-	Паспортные данные - описываются структурой passport и только для разделенного (format = separated) адреса
								"series"			=>	str_replace(' ', '', $arUser['UF_P_SERIAL']),
								"number"			=>	$arUser['UF_P_NUMBER'],
								"issue_date"	=>	date("Y-m-d", MakeTimeStamp($arUser['UF_P_ISSUED_DATE'])),		//	Дата выдачи. Формат: “ГГГГ-ММ-ДД”
								"issued_by"		=>	$arUser['UF_P_ISSUED_BY'],		//	Кем выдан
								"birth_date"	=>	date("Y-m-d", MakeTimeStamp($arUser['UF_P_DATE_ROJD']))		//	Дата рождения. Формат: “ГГГГ-ММ-ДД”
						)
				)
		);
		pre('Запускаем функцию «createAddress»:');
		pre($arParams);
		$result["createAddress"] = $LiteMF->Request("createAddress", $arParams);
		pre($result["createAddress"]);

		if($result["createAddress"]['status'] == 'ok'){
			if($userID = $user->Update($arUser['ID'], array("UF_P_LITEMF_ID" => $result["createAddress"]['result']['address']))){
				$arUser["UF_P_LITEMF_ID"] = $result["createAddress"]['result']['address'];
			}else{
				$arOrder["ERRORS"]['PASSPORT_SAVE_LITEMF_ID'] = $user->LAST_ERROR;
			}
		}else{
			$arOrder["ERRORS"]['PASSPORT_SAVE'] = $result["createAddress"]['error'];
		}
	}

	//----------------------------------------------
	// проверим не созданы ли входящие посылки для этого заказа
	//----------------------------------------------
	$arParams = array(
			"filter"	=>	array(
					"warehouse"					=>	4,
					"shop_name"					=>	"BRANDDISTRIBUTION",
//					"outgoing_package"	=>	0,													// не сформерована в исходящую
					"partner_user_fid"	=>	$arOrder["ID"],							// ID Заказа
					"limit"							=>	999999,
//					"status"						=> "in_stock"
			)
	);
	$arIncomingPackage = $LiteMF->Request("getIncomingPackage", $arParams);
	if($arIncomingPackage['status'] != 'ok' ){ $arIncomingPackage = array(); echo('error!'.$arIncomingPackage['error']); }
	elseif($arIncomingPackage["result"]["pager"]["total"] <= 0){ $arIncomingPackage = array(); echo('Нет входящих посылок соответствующих заказу №'.$arOrder["ID"].'!'.'<br>'); }
	else $arIncomingPackage = $arIncomingPackage["result"]["data"];
pre($arIncomingPackage);

	$arInPacks = array();
	foreach($arIncomingPackage as $arPackage){
//		if($arPackage["status"] == "removed") continue;
		$arInPacks["id"][] = $arPackage["id"];
		$arInPacks["fid"][$arPackage["id"]] = $arPackage["partner_fid"];
		$arInPacks["fid_id"][$arPackage["partner_fid"]] = $arPackage["id"];
		$arInPacks["fid_full"][$arPackage["partner_fid"]] = $arPackage;
	}

pre($arInPacks);

	$arDeclarations = array();
	foreach($arOrder["ITEMS"] as $arItem){

		//----------------------------------------------
		// Создаем заготовку Declarations для Исходящей посылки
		//----------------------------------------------
		$arDeclarations[] = array(
				"description"		=>	$arItem['EN_NAME'],
				"weight"				=>	(intval($arItem['WEIGHT']) > 0 ? intval($arItem['WEIGHT'])*1000 : 2000),
				"quantity"			=>	intval($arItem['QUANTITY']),
				"value"					=>	round($arItem['PRODUCT_PRICE']['PRICE']-0.01,2),// 9.99,
				"url"						=>	'http://zomart.ru'.$arItem['DETAIL_PAGE_URL'],
		);
		//----------------------------------------------
		// Создаем TRACKING_NUMBER
		//----------------------------------------------
		$trec = '';
		if($arItem["PROPERTIES"]["barcode"]) $trec = $arItem["PROPERTIES"]["barcode"];
		if($arItem["ITEM_SKU"]["PROPERTY_BD_BARCODE_VALUE"]) $trec = $arItem["ITEM_SKU"]["PROPERTY_BD_BARCODE_VALUE"];
		else $trec = $arOrder["SHIPMENT"]["TRACKING_NUMBER"];
		//else $trec = $arItem["PROPERTIES"]["PRODUCT.XML_ID"];
		//----------------------------------------------
		// Создаем заготовку входящую посылку
		//----------------------------------------------
		$arParams = array(
				"data"	=>	array(
							"warehouse"					=>	4,
							"shop_name"					=>	"BRANDDISTRIBUTION",
							"partner_fid"				=>	$arOrder["ACCOUNT_NUMBER"]."-".$arItem['ID'],
							"partner_user_fid"	=>	$arOrder["ID"],
							"partner_url"				=>	'http://zomart.ru'.$arItem['DETAIL_PAGE_URL'],
							"name"							=>	$arItem['NAME'],
							"price"							=>	round($arItem['PRODUCT_PRICE']['PRICE']-0.01, 2), // 9.99,
							"tracking"					=>	$trec,
				)
		);
pre($arParams);
		//----------------------------------------------
		$fid = $arOrder["ACCOUNT_NUMBER"]."-".$arItem['ID'];
		//----------------------------------------------
		// Проверим на наличие уже созданной Входящей посылки
		if(in_array($fid, $arInPacks["fid"])){
				$idPack = $arInPacks["fid_id"][$fid];
				pre('Уже есть "Packet ID" = '. $idPack . ' "FID" = '. $fid . '<br>');
				$arNewData = array();
				foreach($arParams["data"] as $codeData => $valData)
					if(in_array($codeData, array("shop_name","partner_url","name"/* ,"tracking" */)) && $valData != $arInPacks["fid_full"][$fid][$codeData])
						$arNewData[$codeData] = $valData;

				if(count($arNewData) > 0){
pre($arNewData);
						$arParams = array(
				    		"incoming_package"	=>	$idPack,
								"data"	=>	$arNewData
						);
						pre('Запускаем функцию «editIncomingPackage»');
pre($arParams);
//						$result["editIncomingPackage"] = $LiteMF->Request("editIncomingPackage", $arParams);
pre($result["editIncomingPackage"]);
						if($result["editIncomingPackage"]['status'] == 'ok'){
//							$arInPacks["id"][] = $result["editIncomingPackage"]['result']['incoming_package'];
						}else{
							$arOrder["ERRORS"]['editIncomingPackage'] = $result["editIncomingPackage"]['error'];
						}
 				}


		}else{ // если нет
pre('Запускаем функцию «createIncomingPackage»');
pre($arParams);
			$result["createIncomingPackage"] = $LiteMF->Request("createIncomingPackage", $arParams);
pre($result["createIncomingPackage"]);
			if($result["createIncomingPackage"]['status'] == 'ok'){
//				$arInPacks["id"][] = $result["createIncomingPackage"]['result']['incoming_package'];
			}else{
				$arOrder["ERRORS"]['createIncomingPackage'] = $result["createIncomingPackage"]['error'];
			}
		}
	}

	//----------------------------------------------------
	// Если есть Входящие - создадим Исходящую посылку
	//----------------------------------------------------
	if(count($arInPacks["id"]) > 0)
	{
		//----------------------------------------------------
		// Если адрес пуст или 0 - присвоим проверенный
		//----------------------------------------------------
		if( empty($arUser["UF_P_LITEMF_ID"]) || intval($arUser["UF_P_LITEMF_ID"]) == 0 ) $arUser["UF_P_LITEMF_ID"] = 113492; // 113522

		//----------------------------------------------------
		//  Проверим на наличие уже созданной Исходящей посылки
		//----------------------------------------------------
		pre('Запускаем функцию «getOutgoingPackage»');
		$arParams = array(
				"filter"	=>	array(
						"warehouse"					=>	4,
						"partner_fid"				=>	$arOrder["ID"],
						"limit"							=>	999999,
				)
		);
pre($arParams);
		$result["getOutgoingPackage"] = $LiteMF->Request("getOutgoingPackage", $arParams);
pre($result["getOutgoingPackage"]);
		if($result["getOutgoingPackage"]['status'] == 'ok' && count($result["getOutgoingPackage"]['result']['data']) > 1){
			$result["getOutgoingPackage"] = $result["getOutgoingPackage"]['result']['data'][0];
			$idOutgoingPackage = intval($result["getOutgoingPackage"]['result']['data'][0]['id']);
		}else{
			$arOrder["ERRORS"]['getOutgoingPackage'] = $result["getOutgoingPackage"]['error'];
		}

		if($idOutgoingPackage > 1) {

			pre('Уже есть Исходящая посылка "ID" = '. $result["getOutgoingPackage"]['id'] . '<br>');
			pre($result["getOutgoingPackage"]);

		}else{
				pre('Запускаем функцию «createOutgoingPackage»');
				$arParams = array(
						"data"	=>	array(																	//	+
								"incoming_packages"	=>	$arInPacks["id"],
								"delivery_method"		=>	85, // [id] => 66 [name] => Express [delivery_currency] => EUR, [id] => 85 [name] => Shiptor Air [delivery_currency] => EUR
								"delivery_point"		=>	intval($arOrder["PROPERTIES"]["PVZ"]),
								"address"						=>	$arUser["UF_P_LITEMF_ID"], // попробуйте на 113392
								"name"							=>	"Заказ на сайте ZOMART.RU # ".$arOrder["ID"],
								"partner_fid"				=>	$arOrder["ID"],//$arOrder["ACCOUNT_NUMBER"],
								"partner_url"				=>	"http://zomart.ru/bitrix/admin/sale_order_view.php?ID=".$arOrder["ID"],
								"comment"						=>	($arOrder["SHIPMENT"]["COMMENTS"] ? $arOrder["SHIPMENT"]["COMMENTS"] : "нет"),
								"sender"						=>	"ZOMART.RU",
								"declarations"			=>	$arDeclarations
						)
				);
pre($arParams);
				$result["createOutgoingPackage"] = $LiteMF->Request("createOutgoingPackage", $arParams);
pre($result["createOutgoingPackage"]);
				if($result["createOutgoingPackage"]['status'] == 'ok'){
					if (!CSaleOrder::StatusOrder($arOrder["ID"], "R")) $arOrder["ERRORS"]["StatusOrder"] = "Ошибка установки нового статуса заказа";
				}else{
					$arOrder["ERRORS"]['createOutgoingPackage'] = $result["createOutgoingPackage"]['error'];
				}
		}

	}

	pre($arOrder["ERRORS"]);
	echo('<hr>');
#	$nnn++;if($nnn > 1) break;
#	pre($result);
}
echo('<hr>');
pre("arOrders:");
pre($arOrders);
die('<hr>Happy end!');

//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//====================================================================================
//------------------------------------------------------------------------------------
//												L			I			T			E			M			F
//------------------------------------------------------------------------------------
//====================================================================================
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//				М	е	т	о	д	ы	 	L	i	t	e	M	F
//------------------------------------------------------------------------------------
$arMethod = array
(

	'getCountry'																=>		'метод получени списка стран',
	'getDeliveryMethod'													=>		'метод получения списка методов доставки',
	'getDeliveryPrice'													=>		'получение цены доставки',
	'getDeliveryPrice'													=>		'получение цены доставки',
	'getDeliveryPointList'											=>		'получение списка доступных ПВЗ',

	'createAddress'															=>		'метод создания адреса доставки',
	'editAddrress'															=>		'метод редактирования адреса доставки',
	'getAddress'																=>		'получение адреса доставки',

	'createIncomingPackage'											=>		'метод создания входящей посылки',
	'editIncomingPackage'												=>		'метод для редактирования входящей посылки',
	'getIncomingPackage'												=>		'получение списка входящих посылок',

	'createOutgoingPackage'											=>		'создание исходящей посылки',
	'editOutgoingPackage'												=>		'редактирование исходящей посылки',
	'getOutgoingPackage'												=>		'получение списка исходящих посылок',

	'addIncomingPackageToOutgoingPackage'				=>		'добавление входящих посылок в состав исходящей',
	'removeIncomingPackageFromOutgoingPackage'	=>		'удаление входящих посылок из состава исходящих',
	'disbandOutgoingPackage'										=>		'расформирование исходящей посылки',

	'getIncomingPackagePhotoLinks'							=>		'получение списка ссылок на фотографии входящей посылки',
	'allowSendOutgoingPackage'									=>		'разрешить отправку отложенной исходящей посылки'

);
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------
//	Коды ошибок
// ------------------------------------------------------------------------------------
	$codeError = array(
			'1001'		=>		"Доступ запрещен",
			'1002'		=>		'Некорректный заголовок аутентификации',
			'1100'		=>		'Пропущено обязательное поле фильтра',
			'1103'		=>		'Пропущен обязательный параметр метода',
			'1104'		=>		'Некорректное значение параметра',
			'1201'		=>		'Сущность не найдена',
			'1087'		=>		'Некорректные данные',
			'2033'		=>		'Пропущены обязательные поля',
			'2044'		=>		'Значение не может быть структурой',
			'2045'		=>		'Значение не может быть скалярным',
			'2055'		=>		'Пустая коллекция',
			'2022'		=> 		'Некорретный тип значения',
			'1400'		=>		'Внутренняя ошибка сервера',
			'1401'		=>		'Ошибка импорта',
			'-32700'	=>		'Некорректный метод запроса; Некорректный или пустой заголовок Content-Type; Ошибка JSON структуры.',
			'-32600'	=>		'Пустой id в JSON структуре; Пустой method в JSON структуре; Пустой params в JSON структуре.',
			'-32602'	=>		'Запрошенный метод не найден; Ошибка вызова метода.'
	);
// ------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------
//	Статусы входящих посылок - Status Incoming Package
// ------------------------------------------------------------------------------------
	$codeStatusIncomingPackage = array(
			'awaiting_arrival'		=>		'ожидается на складе',
			'in_stock'						=>		'на складе',
			'removed'							=>		'удалена'
	);
// ------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------
//	Статусы исходящих посылок - Status Outgoing Package
// ------------------------------------------------------------------------------------
	$codeStatusOutgoingPackage = array(
			'new'									=>		'Сформирована пользователем',
			'in_processing_queue'	=>		'В очереди на упаковку',
			'processing'					=>		'Упаковывается',
			'awaiting_payment'		=>		'Ожидается оплата',
			'awaiting_sending'		=>		'Ожидается отправка',
			'sent'								=>		'Отправлена',
			'received'						=>		'Получена',
			'wait_disband'				=>		'Ожидает расформирования',
			'disbanded'						=>		'Расформирована',
			'wait_recycle'				=>		'Ожидает утилизации',
			'recycled'						=>		'Утилизирована',
			'removed'							=>		'Удалена'
	);
// ------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//	Р А Б О Т А   С О   С Т Р А Н А М И
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//	getCountry - метод получени списка стран
//	Параметры ответа:	data	array(struct)			-		Коллекция сущностей Country
//------------------------------------------------------------------------------------
//	Country - страна
//------------------------------------------------------------------------------------
//	id									int										-		Идентификатор
//	code								string(2)							-		Буквенный код страны ISO 3166-1 alpha-2
//	name								string(255)						-		Название страны
//	zone_type						enum(‘kladr’, ‘zip_code’, ‘flat_rate’)		-		Тип зоны: с КЛАДРом, индексом или без зоны
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getCountry»');
		$arParams = array(
		  "filter"	=>	array(			//	Фильтр по возможным атрибутам: id, code; Фильтр по пагинации: page, limit
		      "code"	=>	array("RU","DE","US"),
					"limit"	=>	999999,
		  ),
		);
		pre($arParams);
		pre($LiteMF->Request("getCountry", $arParams));
		echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	'id' => 373,	'code' => 'DE', 'name' => 'Germany',									'zone_type' => 'flat_rate'
//	'id' => 3159, 'code' => 'RU', 'name' => 'Russia',										'zone_type' => 'kladr'
//	'id' => 3287, 'code' => 'US', 'name' => 'United States of America', 'zone_type' => 'zip_code'
//------------------------------------------------------------------------------------




//------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------
//	Р А Б О Т А   С   М Е Т О Д А М И   Д О С Т А В К И
// ------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------
//	getDeliveryMethod — метод получения списка методов доставки
//	Параметры ответа:	data	array(struct)			-		Коллекция сущностей DeliveryMethod
//------------------------------------------------------------------------------------
//	id									int										-		Идентификатор
//	name								string(255)						-		Название страны
//	delivery_currency		enum('USD', 'GBP', 'CNY', 'JPY', 'EUR')		-		Валюта расчета
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getDeliveryMethod»');
		$arParams = array(
	      "country_from"	=>	3287,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	=>	3287 "US"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    =>	3159 "RU"
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryMethod", $arParams));
		echo('<hr>');
		$arParams = array(
	      "country_from"	=>	373,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	=>	373	"DE"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    =>	3159 "RU"
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryMethod", $arParams));
		echo('<hr>');
*/
// ------------------------------------------------------------------------------------
//	id => 66, name => Express, delivery_currency => EUR
// ------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------
//	getDeliveryPrice - получение цены доставки
//	Параметры ответа:	data	array(struct)			-		Список объектов с полями:
//		delivery_method (идентификатор (id) метода доставки)
//		price (цена)
//		currency (валюта, по умолч. USD)
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getDeliveryPrice»');
		$arParams = array(
	      "country_from"	=>	3287,	//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	= 3287 "US"
	      "country_to"		=>	3159,	//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    = 3159 "RU"
	      "weight"				=>	500,								//	Вес посылки, г
	      "zone"					=>	"3600000100000",		//"7500000000000",		//	КЛАДР или индекс
	      "filter"				=>	array(
						"limit"							=>	999999,
		        "delivery_method"		=>	array(1,2,59,76,91),		//	ID метода(ов) доставки (см. DeliveryMethod)
	      )
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryPrice", $arParams));
	echo('<hr>');
	echo('Тестируем функцию «getDeliveryPrice»');
		$arParams = array(
	      "country_from"	=>	3287,		//	int	-	Обязательный	-	Идентификатор страны отправления (см. метод getCountry)	= 373	"DE"
	      "country_to"		=>	3159,		//	int	-	Обязательный	-	Идентификатор страны доставки (см. метод getCountry)    = 3159 "RU"
	      "weight"				=>	500,								//	Вес посылки, г
	      "zone"					=>	"3600000100000",		//"7500000000000",		//	КЛАДР или индекс
	      "filter"				=>	array(
						"limit"							=>	999999,
		        "delivery_method"		=>	array(66),		//	ID метода(ов) доставки (см. DeliveryMethod)
	      )
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryPrice", $arParams));
	echo('<hr>');
*/
// ------------------------------------------------------------------------------------
/*
'delivery_method' => 66,
'price' => 13.69,
'currency' => 'EUR'
*/
// ------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------
//	Р А Б О Т А   С   П У Н К Т А М И   В Ы Д А Ч И   З А К А З А   ( П В З )
// ------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------
// getDeliveryPointList - получение списка доступных ПВЗ
//	Параметры ответа:	data	array(struct)			-		Коллекция сущностей DeliveryPoint
//------------------------------------------------------------------------------------
//	DeliveryPoint - пункт выдачи заказа
//------------------------------------------------------------------------------------
//	id									int										-		Идентификатор
//	name								string(255)						-		Название курьера и пункта выдачи
//	address							string(255)						-		Адрес
//	phone								string(50)						-		Телефон
//	trip_description		string(2047)					-		Описание как проехать
//	gps									string(255)						-		Координаты GPS
//	work_schedule				string(255)						-		Время работы
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getDeliveryPointList»');
		$arParams = array(
	      "filter"				=>	array(
            "kladr"	=>	"3600000100000",
						"limit"	=>	999999,
	      )
		);
		pre($arParams);
		pre($LiteMF->Request("getDeliveryPointList", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
/*
['id'] => 199,
['name'] => 'Boxberry Воронеж 45-ой Стрелковой дивизии_3601',
['address'] => '394016, Воронеж г, 45 стрелковой дивизии ул, д.108',
['phone'] => '8-800-700-54-30',
['trip_description'] => 'московский проспект - центральная улица города, рядом остановка, нужно выйти на улицу 45 стрелковой дивизии и пройти 230 метров в сторону школы №29, вход в офис со стороны двора',
['gps'] => '51.695629,39.186157',
['work_schedule'] => 'пн-пт: 08.00-20.00, сб: 09.00-18.00',
*/
//------------------------------------------------------------------------------------



//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//	Р А Б О Т А   С   А Д Р Е С А М И   Д О С Т А В К И
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	Address - адрес доставки
//------------------------------------------------------------------------------------
//	id								int						-		Идентификатор
//	name							array					-		ФИО получателя
//	delivery_country	int						-		Идентификатор страны доставки
//	first_line				array					-		Первая строка адреса
//	city							string(128)		-		Город
//	region						string(128)		-		Область/Регион
//	zip_code					string(128)		-		Индекс
//	phone							string(255)		-		Телефон
//	email							string(128)		-		Адрес электронной почты
//	flat							string(128)		-		Квартира
//	passport					array					-		Паспорт
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	name - фамилия, имя, отчество
//------------------------------------------------------------------------------------
//	last_name			string(255)		-		Фамилия
//	first_name		string(255)		-		Имя
//	middle_name		string(255)		-		Отчество
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	first_line - первая строка адреса
//------------------------------------------------------------------------------------
//	street				string(128)		-		Улица
//	house					string(12)		-		Дом
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	phone - телефонный номер
//------------------------------------------------------------------------------------
//	country				string(4)			-		Код страны
//	code					string(6)			-		Код региона
//	number				string(16)		-		Номер телефона
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	passport - паспортные данные получателя
//------------------------------------------------------------------------------------
//	series				string(10)		-		Серия
//	number				string(15)		-		Номер
//	issue_date		string				-		Дата выдачи. Формат: “ГГГГ-ММ-ДД”
//	issued_by			string(500)		-		Кем выдан
//	birth_date		string				-		Дата рождения. Формат: “ГГГГ-ММ-ДД”
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
// createAddress — метод создания адреса доставки
//------------------------------------------------------------------------------------
/*
		$arParams = array(
				"data"	=>	array(										//	+
						"format"		=>	"separated",			//	+	Формат адреса: международный('international') или разделенный('separated')
						"name"			=>	array(						//	+
								"last_name"		=>	"Иванов",		//	+
								"first_name"	=>	"Иван",			//	+
								"middle_name"	=>	"Иванович"	//	+
						),
						"delivery_country"	=>	3159,			//	+
						"first_line"=>	array(						//	+
								"street"		=>	"Мира",
								"house"			=>	"123"
						),
						"flat"			=>	"5",							//	-
						"city"			=>	"Воронеж",				//	+
						"region"		=>	"Воронежская область",	//	-
						"zip_code"	=>	"394000",					//	+
						"phone"			=>	array(						//	-
								"country"		=>	"7",
								"code"			=>	"951",
								"number"		=>	"5551400"
						),
						"email"			=>	"admin@zomart.ru",//	-
						"passport"	=>	array(	//	+-	Паспортные данные - описываются структурой passport и только для разделенного (format = separated) адреса
								"series"			=>	"2030",
								"number"			=>	"456789",
								"issue_date"	=>	"2015-05-20",		//	Дата выдачи. Формат: “ГГГГ-ММ-ДД”
								"issued_by"		=>	"Отделом УФМС России по Воронежской обл.",	//	Кем выдан
								"birth_date"	=>	"1977-11-22"		//	Дата рождения. Формат: “ГГГГ-ММ-ДД”
						)
				)
		);
*/
/*
		$arParams = array(
				"data"	=>	array(																	//	+
						"format"						=>	"international",				//	+	Формат адреса: международный('international') или разделенный('separated')
						"name"							=>	"Петров Петр Пертович",	//	+
						"delivery_country"	=>	3159,										//	+
						"first_line"				=>	"ул. Ленина, д. 5",			//	+
						"city"							=>	"Воронеж",							//	+
						"zip_code"					=>	"394001",								//	+
						"phone"							=>	"79515551401",					//	-
						"email"							=>	"adm@zomart.ru",				//	-
				)
		);
*/
/*
	echo('Тестируем функцию «createAddress»');
		pre($arParams);
		pre($LiteMF->Request("createAddress", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
/*
    ['address'] => 126934, 127055, 127064, 127067, 126028, 126029, 126030
*/
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
// editAddress - метод редактирования адреса доставки	 <<<<<<<  вместо заявленного в описании "editAddrress" !!!
//------------------------------------------------------------------------------------
/*
		$arParams = array(
				"address"	=>	126028,									//	+
				"data"	=>	array(
#						"format"		=>	"separated",			//	+	Формат адреса: международный('international') или разделенный('separated')
#						"name"			=>	array(						//	+
#								"last_name"		=>	"Иванов",		//	+
#								"first_name"	=>	"Иван",			//	+
#								"middle_name"	=>	"Иванович"	//	+
#						),
#						"delivery_country"	=>	3159,			//	+
#						"first_line"=>	array(						//	+
#								"street"		=>	"Мира",
#								"house"			=>	"123",
#								"house"			=>	"123",
#						),
						"flat"			=>	"555",							//	-
#						"city"			=>	"Воронеж",				//	+
#						"region"		=>	"Воронежская область",	//	-
#						"zip_code"	=>	"394000",					//	+
#						"phone"			=>	array(						//	-
#								"country"		=>	"7",
#								"code"			=>	"951",
#								"number"		=>	"5551400"
#						),
#						"email"			=>	"admin@zomart.ru",//	-
#						"passport"	=>	array(	//	+-	Паспортные данные - описываются структурой passport и только для разделенного (format = separated) адреса
#								"series"			=>	"2030",
#								"number"			=>	"456789",
#								"issue_date"	=>	"2015-05-20",		//	Дата выдачи. Формат: “ГГГГ-ММ-ДД”
#								"issued_by"		=>	"Отделом УФМС России по Воронежской обл.",	//	Кем выдан
#								"birth_date"	=>	"1977-11-22"		//	Дата рождения. Формат: “ГГГГ-ММ-ДД”
#						)
				)
		);
*/
/*
		$arParams = array(
				"address"	=>	126028,									//	+
				"data"	=>	array(																	//	+
						"format"						=>	"international",				//	-	Формат адреса: международный('international') или разделенный('separated')
						"name"							=>	"Петр Петров",
#						"delivery_country"	=>	3159,
#						"first_line"				=>	"ул. Ленина, д. 5",
#						"city"							=>	"Воронеж",
						"zip_code"					=>	"394002",
						"flat"							=>	"55",							//	-
#						"phone"							=>	"79515551402",
#						"email"							=>	"adm@ya.ru",
				)
		);
	echo('Тестируем функцию «editAddress»');
		pre($arParams);
		pre($LiteMF->Request("editAddress", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//        ['address'] => 126934
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	getAddress - получение адреса доставки							<<<<<<<<<<  Выдает даже непринадлежайшие нам адреса !!!
//------------------------------------------------------------------------------------
//	Параметры запроса:	filter	struct	Фильтр по возможным атрибутам: id, delivery_country
//	Фильтр по пагинации: page, limit
//------------------------------------------------------------------------------------
//	Параметры ответа:		data 		array(struct)		Коллекция сущностей Address
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getAddress»');
		$arParams = array(
	      "filter"	=>	array(
            "id"		=>	array( 126030 ),
//						"limit"	=>	999999,
	      )
		);
		pre($arParams);
		pre($LiteMF->Request("getAddress", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
/*
    ['result'] => Array(
        ['data'] => Array(
            [0] => Array(
                  ['id'] => 126934
                  ['name'] => 'Петр Иванович Сидоров',
                  ['delivery_country'] => 3159
                  ['first_line'] => 'Садовый пер. 12',
                  ['city'] => 'Москва',
                  ['region'] => 'Московская область',
                  ['zip_code'] => '127427',
                  ['phone'] => '79854444500',
                  ['passport'] => Array(
                      ['number'] => '8683591',
                      ['series'] => '0913',
                  ),
                  ['email'] => '',
                  ['flat'] => '7'
            )
        )
    )
*/
//------------------------------------------------------------------------------------




//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//	Р А Б О Т А   С   П О С Ы Л К А М И
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	Идентификаторы и расположение доступных складов
//------------------------------------------------------------------------------------
//	7			-		Делавэр, США
//	4			-		Берлин, Германия			<<<<<<<<<<<<<<<<===========
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//	createIncomingPackage — метод создания входящей посылки
//------------------------------------------------------------------------------------
//	warehouse									Обязн		int							-	Идентификатор склада LiteMF - 	4	(Берлин, Германия)
//	shop_name									Обязн		string(255)			-	Название магазина, где куплен товар
//	price											Нет			float(9,2)			-	Цена по декларации
//	name											Нет			string(255)			-	Название посылки (содержимое)
//	partner_fid								Нет			string(100)			-	Идентификатор посылки в Вашей системе		<<<<<- УНИКАЛЬНЫЙ!!!
//	partner_user_fid					Нет			int							-	Идентификатор пользователя в Вашей системе
//	tracking									Нет			string(40)			-	Трекинг-номер
//	is_remove_original_wrap		Нет			enum('n', 'y')	-	Заказана ли услуга удаления оригинальной упаковки (бесплатно)
//	is_check									Нет			enum('n', 'y')	-	Заказана ли услуга проверки товара в посылки на соответствие декларации
//	is_make_additional_photo	Нет			enum('n', 'y')	-	Заказана ли услуга дополнительного фотографирования содержимого посылки
//	partner_url								Нет			string(204  7)	-	Ссылка на Ваш сайт (может быть также ссылкой на страницу посылки в Вашей системе)
//------------------------------------------------------------------------------------
//	Параметры ответа:		incoming_package 		int		Идентификатор входящей посылки в сервисе LiteMF
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «createIncomingPackage»');
		$arParams = array(
				"data"	=>	array(																	//	+
							"warehouse"					=>	4,
							"shop_name"					=>	"Brands Distribution",
							"partner_fid"				=>	"ZOM173-75648",
							"partner_user_fid"	=>	173,
							"partner_url"				=>	"http://zomart.ru/catalog/zhenskaya-odezhda/topy-i-mayki/75648/",
							"name"							=>	"Кроссовки Nike 6.5 Чёрный Мужской",
							"price"							=>	9.99,
//							"tracking"					=>	"75644ZOM167",
//							"is_check"					=>	"n",
				)
		);
		pre($arParams);
		pre($LiteMF->Request("createIncomingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	['incoming_package'] => 271567, 273493, 273721, 273731, 273766, 273773, 273776, 273780, 273912, 273914, 273916, 273917, 273944
//	"partner_user_fid"	=>	170, == 271509, 271510, 271512
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	editIncomingPackage — метод для редактирования входящей посылки
//------------------------------------------------------------------------------------
//	incoming_package		Обязн		int			-	Идентификатор входящей посылки в сервисе LiteMF (см. метод getIncomingPackage)
//	data								Нет			array		-	Редактирумые параметры из структуры data метода createIncomingPackage
//------------------------------------------------------------------------------------
//	Параметры ответа:		incoming_package 		int		Идентификатор входящей посылки в сервисе LiteMF
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «editIncomingPackage»');
		$arParams = array(
    		"incoming_package"	=>	271509,
				"data"	=>	array(																	//	+
#							"warehouse"					=>	4,
							"shop_name"					=>	"Brands Distribution",
#							"partner_fid"				=>	"ZOM171-75645",
#							"partner_user_fid"	=>	171,
#							"partner_url"				=>	"http://zomart.ru/catalog/zhenskaya-odezhda/topy-i-mayki/75645/",
							"name"							=>	"Топ Datch F9U7845 L Синий Женский 1 шт",
#							"price"							=>	11.50,
#							"tracking"					=>	"75644ZOM167",
#							"is_check"					=>	"n",
				)
		);
		pre($arParams);
		pre($LiteMF->Request("editIncomingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	['code'] => 1401
//	['message'] => Edit incoming package not allowed
//------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------
//	getIncomingPackage - получение списка входящих посылок
//------------------------------------------------------------------------------------
//	filter								Нет			array		-	Фильтр по возможным атрибутам:
//	id, partner_fid, partner_user_fid, warehouse, package, status
//	желательно добавить фильтрацию по outgoing_package и shop_name
//------------------------------------------------------------------------------------
//	Параметры ответа:		data 		array		Коллекция сущностей IncomingPackage
//------------------------------------------------------------------------------------
/*
		$arParams = array(
				"filter"	=>	array(
						"id"								=>	array( 271520, 271510, 271512 ),
#						"warehouse"					=>	4,
#						"partner_fid"				=>	"ZOM169",
						"partner_user_fid"	=>	170,
#						"status"						=>	"waiting_in_stock", // waiting_in_stock, in_stock, sent, removed
						"limit"							=>	999999,
				)
		);
	echo('Тестируем функцию «getIncomingPackage»');
		pre($arParams);
		pre($LiteMF->Request("getIncomingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//      [id] => 273493
//      [shop_name] => brandsdistribution
//      [warehouse] => 4
//      [name] => Футболка Datch I7U1908 XL Синий Мужской
//      [partner_url] => http://zomart.ru/81082/
//      [partner_fid] => ZOM167
//      [partner_user_fid] => 81396
//      [outgoing_package] => 0
//      [tracking] => E1B1A14943DBD55
//      [is_make_additional_photo] => n
//      [is_remove_original_wrap] => n
//      [is_check] => n
//      [status] => waiting_in_stock
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//	createOutgoingPackage - создание исходящей посылки
//------------------------------------------------------------------------------------
//	delivery_method						Обязн		int							-	Идентификатор метода доставки LiteMF (см. метод getDeliveryMethod)	= 66
//	address										Обязн		int							-	Идентификатор адреса доставки LiteMF (см. метод getAddress) 				= 126934
//	declarations							Обязн		array(struct)		-	Список деклараций по товарам (см. ниже)
//	name											Нет			string(255)			-	Название посылки (содержимое)
//	partner_fid								Нет			string(100)			-	Идентификатор посылки в Вашей системе		<<<<<- УНИКАЛЬНЫЙ!!!
//	partner_url								Нет			string(2047)		-	Ссылка на страницу посылки в Вашей системе
//	incoming_packages					Нет			array(int)			-	Коллекция входящих посылок IncomingPackage
//	comment										Нет			string					-	Комментарий пользователя
//	sender										Нет			string(256)			-	Отправитель (отображается на странице отслеживания посылки)
//	is_postponed_send					Нет			enum('n', 'y')	-	Имеется ли отложенная отправка (по требованию)
//	is_insurance							Нет			enum('n', 'y')	-	Заказана ли услуга страхования
//	is_use_extra_wrap					Нет			enum('n', 'y')	-	Заказана ли услуга дополнительной упаковки
//	is_has_lithium_battery		Нет			enum('n', 'y')	-	Имеется ли товар с литий-ионной батареей
//------------------------------------------------------------------------------------
//	Параметры структуры declarations
//------------------------------------------------------------------------------------
//	description								Обязн		string(100)			-	Описание
//	weight										Нет			int							-	Вес в граммах
//	quantity									Обязн		int							-	Количество
//	value											Обязн		float(7,2)			-	Стоимость товаров
//	url												Нет			string(204)			-	Ссылка на страницу товара
//------------------------------------------------------------------------------------
//	Параметры ответа:		outgoing_package 		int				-	Идентификатор исходящей посылки в сервисе LiteMF
//------------------------------------------------------------------------------------
/*
		$arParams = array(
				"data"	=>	array(																	//	+
						"incoming_packages"	=>	array( 271520, 271521 ),
						"delivery_method"		=>	66,
						"address"						=>	126029, // попробуйте на 113392
						"name"							=>	"Заказ 173",
						"partner_fid"				=>	"ZOM173",
						"partner_url"				=>	"http://zomart.ru/bitrix/admin/sale_order_view.php?ID=173",
						"comment"						=>	"Кроссовки Nike 2 шт и Топ Datch F9U7845 L Синий Женский 1 шт",
						"sender"						=>	"ZOMART.RU",
						"declarations"			=>	array(
								array(
										"description"		=>	"Кроссовки Nike Черный Мужской",
										"weight"				=>	1250,
										"quantity"			=>	2,
										"value"					=>	9.90,
										"url"						=>	"http://zomart.ru/catalog/muzhskaya-odezhda/odezhda/81396/",
								),
						)
				)
		);
	echo('Тестируем функцию «createOutgoingPackage»');
		pre($arParams);
		pre($LiteMF->Request("createOutgoingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//				[code]		=> 1401
//				[message]	=> package_add_failed
//------------------------------------------------------------------------------------



//------------------------------------------------------------------------------------
//	getOutgoingPackage - получение списка исходящих посыло
//------------------------------------------------------------------------------------
//	Фильтр по возможным атрибутам:		id, partner_fid, status
//------------------------------------------------------------------------------------
//	Параметры ответа:		data 		array		Коллекция сущностей OutgoingPackage
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «getOutgoingPackage»');
		$arParams = array(
				"filter"	=>	array(
//						"id"								=>	array( 271567, 273493, 273721, 273731, 273766, 273773, 273776, 273780, 273912 ),
//						"outgoing_package"	=>	169200,
//						"warehouse"					=>	4,
//						"partner_fid"				=>	"ZOM169",
						"status"						=>	"waiting_in_stock", // waiting_in_stock, in_stock, sent, removed
						"limit"							=>	999999,
				)
		);
		pre($arParams);
		pre($LiteMF->Request("getOutgoingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
/*
    [id] => 97302
    [incoming_packages] => Array(
        [0] => 48028
    )
    [delivery_method] => 91
    [address] => 66129
    [status] => awaiting_arrival
    [name] => Package: test
    [tracking] => LP97302
    [partner_url] =>
    [comment] => test comment2
    [is_postponed_send] => n
    [is_insurance] => n
    [insurance_amount] => 0
    [is_use_extra_wrap] => n
    [is_has_lithium_battery] => n
    [sender] =>
    [is_has_document] => y
    [document_receiving_status] => received
    [shipment_fid] =>
    [pallet_fid] =>
    [price] => 0
    [currency] => USD
    [declarations] => Array(
        [0] => Array(
            [description] => test
            [quantity] => 1
            [value] => 1
            [weight] => 500
            [url] => http://test.com
        )
    )
    [weight] => 0
    [weight_unit] => g
    [dimensions] => Array(
        [x] => 0
        [y] => 0
        [z] => 0
    )
    [dimensions_unit] => cm
*/
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//	addIncomingPackageToOutgoingPackage - добавление входящих посылок в состав исходящей
//------------------------------------------------------------------------------------
//	incoming_packages			+			array(int)	-	Список идентификаторов входящих посылок LiteMF
//	outgoing_package			+			int					-	Идентификатор исходящей посылки LiteMF
//------------------------------------------------------------------------------------
//	Параметры ответа:		incoming_package - true/false - какие из входящих посылок были добавлены в состав исходящей
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «addIncomingPackageToOutgoingPackage»');
		$arParams = array(
				"incoming_package"	=>	array( 271567, 273493 ),
				"outgoing_package"	=>	9091,
		);
		pre($arParams);
		pre($LiteMF->Request("addIncomingPackageToOutgoingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	Request error: 1401::Outgoing package 9091 can not be edited:1401
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//	removeIncomingPackageFromOutgoingPackage - удаление входящих посылок из состава исходящих
//------------------------------------------------------------------------------------
//	incoming_packages			+			array(int)	-	Список идентификаторов входящих посылок LiteMF
//	outgoing_package			+			int					-	Идентификатор исходящей посылки LiteMF
//------------------------------------------------------------------------------------
//	Параметры ответа:		incoming_package - true/false - какие из входящих посылок были удалены из состава исходящей
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «removeIncomingPackageFromOutgoingPackage»');
		$arParams = array(
				"incoming_package"	=>	array( 271567, 273493 ),
				"outgoing_package"	=>	9091,
		);
		pre($arParams);
		pre($LiteMF->Request("removeIncomingPackageFromOutgoingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	Request error: 1401::Outgoing package 9091 can not be edited:1401
//------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------
//	disbandOutgoingPackage - удаление входящих посылок из состава исходящих
//------------------------------------------------------------------------------------
//	Фильтр по возможным атрибутам:		id, partner_fid
//------------------------------------------------------------------------------------
//	Параметры ответа:		outgoing_package - true/false - какие из ихсодящих посылок были расформированны
//------------------------------------------------------------------------------------
/*
	echo('Тестируем функцию «disbandOutgoingPackage»');
		$arParams = array(
				"filter"	=>	array(
						"id"								=>	array( 9091 ),
//						"partner_fid"				=>	"ZOM169",
				)
		);
		pre($arParams);
		pre($LiteMF->Request("disbandOutgoingPackage", $arParams));
	echo('<hr>');
*/
//------------------------------------------------------------------------------------
//	Request error: 1401::Outgoing package 9091 can not be edited:1401
//------------------------------------------------------------------------------------











//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//pre($data);
//------------------------------------------------------------------------------------
/*
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
//pre($options);
  $context = stream_context_create($options);
*/
//------------------------------------------------------------------------------------
//  $arResult = json_decode( file_get_contents('http://api.dev.litemf.com/v2/rpc', false, $context), true);
//------------------------------------------------------------------------------------
/*
	if($arResult["status"] == 'error'){
		if($codeError[$arResult["error"]["code"]]) echo('<hr>Ошибка '.$arResult["error"]["code"].': '.$codeError[$arResult["error"]["code"]].' ('.$arResult["error"]["message"].')<hr>');
		else echo('<hr>Error - '.$arResult["error"]["code"].': '.$arResult["error"]["message"].'<hr>');
	}else{
		echo('<hr>Status - OK!<hr>');
	}
*/
//pre($arResult);

?>




<br><?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>