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

/*
//Clouds_height` smallint(5) unsigned DEFAULT NULL
echo "\n"."Уровень воды в срок наблюдений в текущие сутки, см"."\n";
echo $stormConv->water_level;

//Clouds_height` smallint(5) unsigned DEFAULT NULL
echo "\n"."Изменение уровня воды за 8-ой часовой срок наблюдения, см"."\n";
echo $stormConv->water_level_diff;

//Clouds_height` smallint(5) unsigned DEFAULT NULL
echo "\n"."Уровень воды за 20-ой часовой срок наблюдений, см"."\n";
echo $stormConv->water_level_last_20h;

//Weather group indicator
echo "\n"."Указатель типа станции (обслуживаемая персоналом или автоматическая)"."\n";
echo $stormConv->station_operation;

//Cloud_height, cloud base of lowest observed cloud
//echo "\n"."Высота нижней границы самых низких облаков"."\n";
echo "\n"."Высота облаков, м"."\n";
echo $stormConv->cloud_height;

//Total cloud cover in okta
echo "\n"."Общее количество облаков"."\n";
echo $stormConv->cloud_cover_tot;

//Clouds` enum('Малооблачно','Переменная облачность','Облачно с прояснениями','Сплошная облачность','') NOT NULL DEFAULT ''
echo "\n"."Облачность"."\n";
echo $stormConv->desc_clouds;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Температура воды, °C"."\n";
echo $stormConv->water_temp;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Температура воздуха, °C"."\n";
echo $stormConv->air_temp;

//TempD` varchar(5) DEFAULT NULL
echo "\n"."Температура точки росы, °C"."\n";
echo $stormConv->dew_point;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Характеристика ледовых явлений"."\n";
echo $stormConv->ice_phenomena;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Характеристика 2-ого ледового явления"."\n";
echo $stormConv->ice_phenomena_2;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Интенсивность ледового явления (степень покрытия реки или видимой аватории водоема), %"."\n";
echo $stormConv->ice_p_intensity;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Характеристика состояния реки"."\n";
echo $stormConv->condition_river;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Характеристика 2-ого состояния реки"."\n";
echo $stormConv->condition_river_2;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Интенсивность состояния реки (степень покрытия реки или видимой аватории водоема), %"."\n";
echo $stormConv->cond_river_intensity;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Толщина льда, см"."\n";
echo $stormConv->ice_thickness;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Высота снежного покрова на льду, см"."\n";
echo $stormConv->snow_depth_on_ice;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Ежедневный расход воды, м^3/с"."\n";
echo $stormConv->w_consumption;

//Precipitation duration
echo "\n"."Продолжительность выпадения осадков, ч"."\n";
echo $stormConv->precip_duration;

//Dir` enum('Северный','Южный','Западный','Восточный','С-З','С-В','Ю-З','Ю-В','Переменный') DEFAULT NULL
echo "\n"."Направление ветра"."\n";
echo $stormConv->wind_direction;

//Speed` varchar(5) DEFAULT NULL
echo "\n"."Скорость ветра, м/с"."\n";
echo $stormConv->wind_speed;

//Visib` varchar(5) DEFAULT NULL
echo "\n"."Видимость, км"."\n";
echo $stormConv->visibility;

//Clouds_height` smallint(5) unsigned DEFAULT NULL
echo "\n"."Высота облаков, м"."\n";
echo $stormConv->cloud_height;

//Pressure` smallint(5) unsigned DEFAULT NULL
echo "\n"."Давление на уровне станции, гПа"."\n";
echo $stormConv->barometer_st;

//Pressure` smallint(5) unsigned DEFAULT NULL
echo "\n"."Давление на уровне моря, гПа"."\n";
echo $stormConv->barometer;

//Trend` varchar(75) DEFAULT NULL
echo "\n"."Изменение давления"."\n";
echo $stormConv->barometer_trend;

//Trend` varchar(75) DEFAULT NULL
echo "\n"."Значение изменения давления, гПа"."\n";
echo $stormConv->barometer_diff;

//Precipitation group indicator
echo "\n"."Индикатор группы осадков"."\n";
echo $stormConv->precip_group;

//Reference time of precipitation
echo "\n"."Срок накопления осадков"."\n";
echo $stormConv->precip_ref_time;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Метеорологическое явление"."\n";
echo $stormConv->desc_weather;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Прошедшее метеорологическое явление 1"."\n";
echo $stormConv->w_course1;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Прошедшее метеорологическое явление 2"."\n";
echo $stormConv->w_course2;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Количество облаков CL или CM, если облаков CL нет"."\n";
echo $stormConv->cloud_cover_lowest;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Облака вертикального развития и облака нижнего яруса (кроме слоисто-дождевых)"."\n";
echo $stormConv->cloud_type_low;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Облака среднего яруса и слоисто-дождевые облака"."\n";
echo $stormConv->cloud_type_medium;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Облака верхнего яруса"."\n";
echo $stormConv->cloud_type_high;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Максимальная температура воздуха за день, °C"."\n";
echo $stormConv->t_max;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Минимальная температура воздуха за ночь, °C"."\n";
echo $stormConv->t_min;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Состояние поверхности почвы при отсутствии снежного покрова"."\n";
echo $stormConv->grd_conditions;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Минимальная температура поверхности почвы за ночь, °C"."\n";
echo $stormConv->t_ground;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Состояние подстилающей поверхности при наличии снежного покрова"."\n";
echo $stormConv->conditions_snow;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Высота снежного покрова, см"."\n";
echo $stormConv->snow_height;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Продолжительность солнечного сияния за сутки, ч"."\n";
echo $stormConv->sunshine_dur;

//Precipitation amount in section 3
echo "\n"."Количество осадков (секция 3), мм"."\n";
echo $stormConv->precip_section3;

//Reference time of precipitation
echo "\n"."Срок накопления осадков (секция 3)"."\n";
echo $stormConv->precip_ref_time_section3;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Дополнительная информация о погоде в срок наблюдения (требуется доработка)"."\n";
echo $stormConv->weather_addon1;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Дополнительная информация о погоде между сроками наблюдения (требуется доработка)"."\n";
echo $stormConv->weather_addon2;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Состояние поверхности почвы при отсутствии снежного покрова (секция 5)"."\n";
echo $stormConv->grd_conditions_section5;

//weather` varchar(64) DEFAULT NULL
echo "\n"."Температура подстилающей поверхности в срок наблюдения (секция 5), °C"."\n";
echo $stormConv->t_ground_section5;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Средняя  температура  воздуха  за  прошедшие  сутки, °C"."\n";
echo $stormConv->t_avg_last;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Минимальная температура воздуха за ночь на высоте 2 см от поверхности почвы, °C"."\n";
echo $stormConv->t_min_2cm_night;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Максимальная скорость ветра при порывах за прошедшие полусутки, м/с"."\n";
echo $stormConv->wind_gust_max_last;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Количество осадков, выпавших за сутки, мм"."\n";
echo $stormConv->precip_last;

//Temp` varchar(5) DEFAULT NULL
echo "\n"."Количество осадков за сутки, составляющее 30 мм и более, мм"."\n";
echo $stormConv->precip_last_30mm;

//Gust` tinyint(3) DEFAULT NULL
echo "\n"."Порыв ветра, м/с"."\n";
echo $stormConv->wind_gust_speed;

//Humidity` tinyint(3) DEFAULT NULL
echo "\n"."Влажность, %"."\n";
echo $stormConv->humidity;
*/
?>