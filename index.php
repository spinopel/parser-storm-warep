<?php
/*
## Example of a line of raw STORM code in format RF6/04 WAREP

### Change the variable raw to get the result.
### The variable raw has the format RF6/04 WAREP.
*/

// Raw STORM code string
/*
$raw = 'STORM

WAREP 26923 09 05101 30 86703=';
*/
/*
$raw = 'STORM

26531 WAREP 09 08581 11 12006 1//13=';
*/
/*
$raw = 'STORM

26730 WAREP 09 08471 11 12207 1//13=';
*/
/*
$raw = 'STORM

26509 WAREP 09 09451 11 12009 1//15=';
*/
//$raw = '26923 09 05101 30 86703=';
//$raw = '26923 09 05101 19 21881=';
//$raw = '26923 09 05101 75 362001=';
//$raw = '26923 09 05101 36 71535//=';
//$raw = '26923 09 05101 30 88505=';
//$raw = '26923 09 05101 92 93208=';
//$raw = '26923 09 05101 55 320032=';
//$raw = '26923 09 05101 68 102=';
//$raw = '26923 09 05101 41 95063=';
//$raw = '26923 09 05101 40 71535// 1180811=';
//$raw = '26923 09 05101 30 1110209=';
//$raw = '26509 09 09451 11 12009 1//15=';
//$raw = '33001 12 04401 29 10402 1//03 214//=';
$raw = '26887 20 10561 11 10808 1//12=';

//подключаем классы для расшифровки STORM
require_once 'Storm.php';
require_once 'StormConv.php';

//удалить перевод каретки и конец строки в многострочных STORM
$arr_new_line = array("\n", "\r\n");  //специальные символы
$raw = str_replace($arr_new_line, '', $raw);

// Create class instance for parse STORM string with debug output enable
$stormConv = new StormConv($raw, TRUE);

// Parse STORM
$parameters = $stormConv->parse();

/*
print_r($parameters)."\n\n"; // get parsed parameters as array

// Debug information
$debug = $stormConv->debug();
print_r($debug)."\n\n"; // get debug information as array
*/

// Get all converted parameters
$stormConv->convParam();

/*
## Отображаем результаты декодирования STORM для наполнения БД
*/
echo "\n\n"."Представление результатов декодирования Storm для наполнения БД"."\n";
echo $stormConv->raw;

/*
//Пост обработка полученных результатов
//DATAS` date NOT NULL DEFAULT '1000-01-01'
echo "\n\n"."Дата получения данных"."\n";
echo $stormConv->observed_date;

//TIMES` time NOT NULL DEFAULT '00:00:00'
echo "\n"."Срок наблюдения, UTC"."\n";
echo $stormConv->observed_time;
*/

//TIMES` time NOT NULL DEFAULT '00:00:00'
echo "\n"."Число месяца"."\n";
echo $stormConv->monthdayr;

//TIMES` time NOT NULL DEFAULT '00:00:00'
echo "\n"."Время начала/окончания телеграммы, UTC"."\n";
echo $stormConv->hourr.":".$stormConv->minuter;

/*
//DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
echo "\n"."Дата и время"."\n";
echo $stormConv->observed_date_time;

//STATION_TYPE_CODE` varchar(20) NOT NULL DEFAULT ''
echo "\n"."Тип станции"."\n";
echo $stormConv->station_report;
*/

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Индекс станции"."\n";
echo $stormConv->station_id;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №1"."\n";
echo $stormConv->storm_code_1;

//wind direction in dekadegree (10 minute mean)
//Dir` enum('Северный','Южный','Западный','Восточный','С-З','С-В','Ю-З','Ю-В','Переменный') DEFAULT NULL
echo "\n"."Направление ветра"."\n";
echo $stormConv->wind_direction;

//Wind speed (10 minute mean)
//Speed` varchar(5) DEFAULT NULL
echo "\n"."Скорость ветра, м/с"."\n";
echo $stormConv->wind_speed;

//Gust speed
//Speed` varchar(5) DEFAULT NULL
echo "\n"."Порыв ветра, м/с"."\n";
echo $stormConv->wind_gust;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №2"."\n";
echo $stormConv->storm_code_2;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Текущее метеорологическое явление"."\n";
echo $stormConv->current_weather;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №3"."\n";
echo $stormConv->storm_code_3;

//Precipitation amount
echo "\n"."Количество осадков, мм"."\n";
echo $stormConv->precip;

//Storm period
echo "\n"."Период, за который произошло НЯ или ОЯ, ч"."\n";
echo $stormConv->storm_period;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №4"."\n";
echo $stormConv->storm_code_4;

//Horizontal visibility in km
echo "\n"."Видимость, км"."\n";
echo $stormConv->visibility;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №5"."\n";
echo $stormConv->storm_code_5;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Количество облачности, кол-во баллов"."\n";
echo $stormConv->amount_cloudiness;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Форма облаков"."\n";
echo $stormConv->clouds_shape;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Высота нижней границы облаков, м"."\n";
echo $stormConv->cld_low_height;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №6"."\n";
echo $stormConv->storm_code_6;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Диаметр града, мм"."\n";
echo $stormConv->hail_size;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №7"."\n";
echo $stormConv->storm_code_7;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Диаметр гололедно-изморозевых отложений, мм"."\n";
echo $stormConv->rime_ice_size;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Температура воздуха, °C"."\n";
echo $stormConv->temperature;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Показатель стадии метеоявления"."\n";
echo $stormConv->trend_phenomena;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №8"."\n";
echo $stormConv->storm_code_8;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ №9"."\n";
echo $stormConv->storm_code_9;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Состояние облачности над горами и перевалами"."\n";
echo $stormConv->cloudness_over_mount;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Эволюция облачности над горами и перевалами"."\n";
echo $stormConv->cloud_evolution;

//ID_STATION` varchar(5) NOT NULL DEFAULT ''
echo "\n"."Тип НЯ или ОЯ (обобщенный)"."\n";
echo $stormConv->storm_code_combine;		
?>