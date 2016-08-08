<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	// создаем объект
	$obCache = new CPHPCache;
	// время кеширования - сутки
	$life_time = 60*60*24;
	$cache_id = $kladr;

	// если кэш есть и он ещё не истек то
	if($obCache->InitCache($life_time, $kladr, "/LITE_MF_PVZ/")) :
	    // получаем закешированные переменные
	    $LITE_MF_PVZ = $obCache->GetVars();
	else :

		$arParamsDeliveryPointList = array(
	      "filter"		=>	array(
            "kladr"	=>	$kladr,
						"limit"	=>	9999
	      )
		);
//pre($arParamsDeliveryPointList);
		$LITE_MF_PVZ = $LiteMF->Request("getDeliveryPointList", $arParamsDeliveryPointList);

	endif;

	// начинаем буферизирование вывода
	if($obCache->StartDataCache()):
	    // записываем предварительно буферизированный вывод в файл кеша
	    $obCache->EndDataCache($LITE_MF_PVZ);
	endif;

/*
        $data = array(
            "id" => time(),
            "method" => "getDeliveryPointList",
            "params" => array(
                "filter" => array(
                    "kladr" => $kladr
                )
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
        $LITE_MF_PVZ = json_decode(file_get_contents('http://api.litemf.com/v2/rpc', false, $context),true);
*/
?>