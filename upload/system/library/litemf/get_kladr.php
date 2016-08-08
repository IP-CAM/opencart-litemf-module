<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if($cityID > 0){

	// создаем объект
	// время кеширования - сутки
	$life_time = 60*60*24;
	$cache_id = $cityID;
	$obCache = new Cache('LiteMf - '.$cityID, $life_time);

	// если кэш есть и он ещё не истек то
	if($obCache->get($cache_id)) :
	    // получаем закешированные переменные
	    $kladrResult = $obCache->get($cache_id);
	else :
	    // иначе обращаемся к базе
			$arVal = CSaleLocation::GetByID($cityID, LANGUAGE_ID);
			if(!empty($arVal['CITY_NAME'])) $string = $arVal['REGION_NAME'].", Город ".$arVal['CITY_NAME']; // $arVal['CITY_NAME']." город";
			else $string = $arVal['REGION_NAME'];
	    // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
			include "kladr.php";
	    $apiKladr = new Kladr\Api('567152cc0a69de80658b45bd', '86a2c2a06f1b2451a87d05512cc2c3edfdf41969');
	    // Формирование запроса
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

	if(!$kladrResult[0]['id']) $kladr = "error"; else $kladr = $kladrResult[0]['id'];

}else{

	$kladr = "error";

}
?>