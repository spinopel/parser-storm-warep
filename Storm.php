<?php
/*
	===========================
	HYDRO Parser Class
	===========================

	Version: 1.0
	
	This library is based on GetWx script by Mark Woodward.

	(c) 2024, Spin Opel (https://spinopel.top/)
	(c) 2013-2020, Information Networks, Ltd. (http://www.hsdn.org/)
	(c) 2001-2006, Mark Woodward (http://woody.cowpi.com/phpscripts/)

		This script is a PHP library which allows to parse the storm code
	in format RF6/04 WAREP, and convert it to an array of data parameters. WAREP code
	parsed using the syntactic analysis and regular expressions. It solves
	the problem of parsing the data in the presence of any error in the code WAREP.
	In addition to the return parameters, the script also displays the interpreted
	(easy to understand) information of these parameters.
*/
	
/***********BEGIN STORM STRUCTURE ******************************************************/
	# section 0
	# YYGGGGi IIiii
	
	# section 1
	# CC: 11, 12, 17, 18, 19, 36, 78
	# 1ddfffxfx
	
	# section 2
	# CC: 19, 91
	# 2ddww
	
	# section 3
	# CC: 61, 62, 64, 65, 66, 71, 75 
	# 3RRRtt
	
	# section 4
	# CC: 36, 40, 41, 78
	# 7VVwwtt
	
	# section 5
	# CC: 30, 40
	# 8NChh
	
	# section 6
	# CC: 90, 92
	# 932RR
	
	# section 7
	# CC: 50, 51, 52, 53, 54, 55, 56, 57
	# RRsTTk
	
	# section 8
	# CC: 68
	# sTT
	
	# section 9
	# CC: all	
	# 950Nn
/***********END STORM STRUCTURE ******************************************************/

class Storm
{
	/*
	 * Array of decoded result, by default all parameters is null.
	*/
	private $result = array
	(
		'raw'                      => NULL,
		'observed_date'            => NULL,
		'observed_time'            => NULL,
		'monthdayr'                => NULL,
		'hourr'                    => NULL,
		'minuter'                  => NULL,
		'station_id'               => NULL,
		'storm_code_1'             => NULL,
		'wind_direction'           => NULL,
		'wind_speed'               => NULL,
		'wind_gust'                => NULL,
		'storm_code_2'             => NULL,
		'current_weather'          => NULL,
		'storm_code_3'             => NULL,
		'precip'                   => NULL,
		'storm_period'             => NULL,
		'storm_code_4'             => NULL,
		'visibility'               => NULL,
		'amount_cloudiness'        => NULL,
		'clouds_shape'             => NULL,
		'cld_low_height'           => NULL,
		'storm_code_6'             => NULL,
		'hail_size'                => NULL,
		'storm_code_7'             => NULL,
		'rime_ice_size'            => NULL,
		'temperature'              => NULL,
		'trend_phenomena'          => NULL,
		'storm_code_8'             => NULL,
		'storm_code_9'             => NULL,
		'cloudness_over_mount'     => NULL,
		'cloud_evolution'          => NULL
	);

	/*
	 * Methods used for parsing in the order of data
	*/
	private $method_names = array
	(
		'station_id',
		'date',
		'time',
		//'station_type',
		'storm_code_1',
		'wind',
		'wind_recent_weather',	// additional group for section 1
		'storm_code_2',
		'wind_recent_weather',
		'storm_code_3',
		'precip_period',
		'storm_code_4',
		'vis_weather_period',
		'wind',					// additional group for section 4
		'storm_code_5',
		'cloud_report',
		'wind',					// additional group for section 5
		'storm_code_6',
		'hail_size',
		'storm_code_7',
		'rime_temp_trend',
		'storm_code_8',
		'temperature',
		'storm_code_9',
		'cloud_mount'
	);

	/*
	 * Interpretation station type codes.
	*/
	/*
	private $STATION_TYPE_CODE = array
	(
		'AAXX' => 'Landstation (FM 12)',
		'BBXX' => 'Seastation (FM 13)',
		'OOXX' => 'Mobile landstation (FM 14)'
	);
	*/
	
/***********BEGIN STORM OPTIONS ******************************************************/

	#section 0 - YYGGGGi group
	private $WIND_UNIT_CODE = array
	(
		"0" => "meters per second estimate",
		"1" => "meters per second measured",
		"3" => "knots estimate",
		"4" => "knots measured"
	);
	
	#section 1-9 - CC group
	/*
	 * Interpretation phenomena codes.
	*/
	private $PHENOMENA_CODE = array
	(
		'10' /*11*/ => 'Сильный ветер',
		'11' /*11*/ => 'Сильный ветер',
		'12' /*11*/ => 'Сильный ветер',
		'17' /*18*/ => 'Шквал',
		'18' /*18*/ => 'Шквал',
		'19' /*19*/ => 'Смерч',
		'29' /*29*/ => 'Гроза',
		'30' /*30*/ => 'Низкая облачность',
		'35' /*35*/ => 'Пыльная или песчаная буря',
		'36' /*35*/ => 'Пыльная или песчаная буря',
		'39' /*39*/ => 'Метель',
		'40' /*40*/ => 'Горизонтальная видимость',
		'41' /*40*/ => 'Горизонтальная видимость',
		'48' /*49*/ => 'Изморозь',
		'49' /*49*/ => 'Изморозь',
		'50' /*67*/ => 'Гололед',
		'52' /*69*/ => 'Налипание мокрого снега',
		'53' /*67*/ => 'Гололед',
		'54' /*49*/ => 'Изморозь',
		'55' /*68*/ => 'Сложное отложение',
		'56' /*67*/ => 'Гололед',
		'57' /*57*/ => 'Гололедица',
		'61' /*65*/ => 'Интенсивные осадки',
		'62' /*65*/ => 'Интенсивные осадки',
		'64' /*65*/ => 'Интенсивные осадки',
		'65' /*65*/ => 'Интенсивные осадки',
		'67' /*67*/ => 'Гололед',
		'68' /*68*/ => 'Сложное отложение',
		'69' /*69*/ => 'Налипание мокрого снега',
		'71' /*65*/ => 'Интенсивные осадки',
		'75' /*65*/ => 'Интенсивные осадки',
		'76' /*39*/ => 'Метель',
		'78' /*39*/ => 'Метель',
		'79' /*79*/ => 'Ледяной дождь',
		'82' /*65*/ => 'Интенсивные осадки',
		'86' /*65*/ => 'Интенсивные осадки',
		'89' /*90*/ => 'Град',
		'90' /*90*/ => 'Град',
		'91' /*29*/ => 'Гроза',
		'92' /*90*/ => 'Град'
	);
	
	#section 1 or 2 - 1ddfffxfx or 2ddww group
	private $wind_dir_degrees = array
	(
		"01" => "05-14",
		"02" => "15-24",
		"03" => "25-34",
		"04" => "35-44",
		"05" => "45-54",
		"06" => "55-64",
		"07" => "65-74",
		"08" => "75-84",
		"09" => "85-94",
		"10" => "95-104",
		"11" => "105-114",
		"12" => "115-124",
		"13" => "125-134",
		"14" => "135-144",
		"15" => "145-154",
		"16" => "155-164",
		"17" => "165-174",
		"18" => "175-184",
		"19" => "185-194",
		"20" => "195-204",
		"21" => "205-214",
		"22" => "215-224",
		"23" => "225-234",
		"24" => "235-244",
		"25" => "245-254",
		"26" => "255-264",
		"27" => "265-274",
		"28" => "275-284",
		"29" => "285-294",
		"30" => "295-304",
		"31" => "305-314",
		"32" => "315-324",
		"33" => "325-334",
		"34" => "335-344",
		"35" => "345-354",
		"36" => "355-04",
		"99" => "VRB",  //wind_direction_varies
		"00" => "calm wind",
		"//" => NULL	
	);
	
	#section 1 or 2 - 1ddfffxfx or 2ddww group
	private $wind_dir_compass = array
	(
		"01" => "NNE",
		"02" => "NNE",
		"03" => "NNE",
		"04" => "NNE",
		"05" => "NE",
		"06" => "ENE",
		"07" => "ENE",
		"08" => "ENE",
		"09" => "E",
		"10" => "ESE",
		"11" => "ESE",
		"12" => "ESE",
		"13" => "ESE",
		"14" => "SE",
		"15" => "SSE",
		"16" => "SSE",
		"17" => "SSE",
		"18" => "S",
		"19" => "SSW",
		"20" => "SSW",
		"21" => "SSW",
		"22" => "SSW",
		"23" => "SW",
		"24" => "WSW",
		"25" => "WSW",
		"26" => "WSW",
		"27" => "W",
		"28" => "WNW",
		"29" => "WNW",
		"30" => "WNW",
		"31" => "WNW",
		"32" => "NW",
		"33" => "NNW",
		"34" => "NNW",
		"35" => "NNW",
		"36" => "N",
		"99" => "VRB",  //wind direction varies
		"00" => "calm wind",
		"//" => NULL	
	);

	#7wwWW
	#section 2 or 4 - 2ddww or 7VVwwtt group
	private $CURRENT_WEATHER_CODE = array
	(
		"00" => "Bewölkungsentwicklung nicht beobachtet",
		"01" => "Bewölkung abnehmend",
		"02" => "Bewölkung unverändert",
		"03" => "Bewölkung zunehmend",
		"04" => "Sicht durch Rauch oder Asche vermindert",
		"05" => "trockener Dunst (relative Feuchte < 80 %)",
		"06" => "verbreiteter Schwebstaub, nicht vom Wind herangeführt",
		"07" => "Staub oder Sand bzw. Gischt, vom Wind herangeführt",
		"08" => "gut entwickelte Staub- oder Sandwirbel",
		"09" => "Staub- oder Sandsturm im Gesichtskreis, aber nicht an der Station",
		"10" => "feuchter Dunst (relative Feuchte > 80 %)",
		"11" => "Schwaden von Bodennebel",
		"12" => "durchgehender Bodennebel",
		"13" => "Wetterleuchten sichtbar, kein Donner gehört",
		"14" => "Niederschlag im Gesichtskreis, nicht den Boden erreichend",
		"15" => "Niederschlag in der Ferne (> 5 km), aber nicht an der Station",
		"16" => "Niederschlag in der Nähe (< 5 km), aber nicht an der Station",
		"17" => "Gewitter (Donner hörbar), aber kein Niederschlag an der Station",
		"18" => "Markante Böen im Gesichtskreis, aber kein Niederschlag an der Station",
		"19" => "Tromben (trichterförmige Wolkenschläuche) im Gesichtskreis",
		"20" => "nach Sprühregen oder Schneegriesel",
		"21" => "nach Regen",
		"22" => "nach Schneefall",
		"23" => "nach Schneeregen oder Eiskörnern",
		"24" => "nach gefrierendem Regen",
		"25" => "nach Regenschauer",
		"26" => "nach Schneeschauer",
		"27" => "nach Graupel- oder Hagelschauer",
		"28" => "nach Nebel",
		"29" => "nach Gewitter",
		"30" => "leichter oder mäßiger Sandsturm, an Intensität abnehmend",
		"31" => "leichter oder mäßiger Sandsturm, unveränderte Intensität",
		"32" => "leichter oder mäßiger Sandsturm, an Intensität zunehmend",
		"33" => "schwerer Sandsturm, an Intensität abnehmend",
		"34" => "schwerer Sandsturm, unveränderte Intensität",
		"35" => "schwerer Sandsturm, an Intensität zunehmend",
		"36" => "leichtes oder mäßiges Schneefegen, unter Augenhöhe",
		"37" => "starkes Schneefegen, unter Augenhöhe",
		"38" => "leichtes oder mäßiges Schneetreiben, über Augenhöhe",
		"39" => "starkes Schneetreiben, über Augenhöhe",
		"40" => "Nebel in einiger Entfernung",
		"41" => "Nebel in Schwaden oder Bänken",
		"42" => "Nebel, Himmel erkennbar, dünner werdend",
		"43" => "Nebel, Himmel nicht erkennbar, dünner werdend",
		"44" => "Nebel, Himmel erkennbar, unverändert",
		"45" => "Nebel, Himmel nicht erkennbar, unverändert",
		"46" => "Nebel, Himmel erkennbar, dichter werdend",
		"47" => "Nebel, Himmel nicht erkennbar, dichter werdend",
		"48" => "Nebel mit Reifansatz, Himmel erkennbar",
		"49" => "Nebel mit Reifansatz, Himmel nicht erkennbar",
		"50" => "unterbrochener leichter Sprühregen",
		"51" => "durchgehend leichter Sprühregen",
		"52" => "unterbrochener mäßiger Sprühregen",
		"53" => "durchgehend mäßiger Sprühregen",
		"54" => "unterbrochener starker Sprühregen",
		"55" => "durchgehend starker Sprühregen",
		"56" => "leichter gefrierender Sprühregen",
		"57" => "mäßiger oder starker gefrierender Sprühregen",
		"58" => "leichter Sprühregen mit Regen",
		"59" => "mäßiger oder starker Sprühregen mit Regen",
		"60" => "unterbrochener leichter Regen oder einzelne Regentropfen",
		"61" => "durchgehend leichter Regen",
		"62" => "unterbrochener mäßiger Regen",
		"63" => "durchgehend mäßiger Regen",
		"64" => "unterbrochener starker Regen",
		"65" => "durchgehend starker Regen",
		"66" => "leichter gefrierender Regen",
		"67" => "mäßiger oder starker gefrierender Regen",
		"68" => "leichter Schneeregen",
		"69" => "mäßiger oder starker Schneeregen",
		"70" => "unterbrochener leichter Schneefall oder einzelne Schneeflocken",
		"71" => "durchgehend leichter Schneefall",
		"72" => "unterbrochener mäßiger Schneefall",
		"73" => "durchgehend mäßiger Schneefall",
		"74" => "unterbrochener starker Schneefall",
		"75" => "durchgehend starker Schneefall",
		"76" => "Eisnadeln (Polarschnee)",
		"77" => "Schneegriesel",
		"78" => "Schneekristalle",
		"79" => "Eiskörner (gefrorene Regentropfen)",
		"80" => "leichter Regenschauer",
		"81" => "mäßiger oder starker Regenschauer",
		"82" => "äußerst heftiger Regenschauer",
		"83" => "leichter Schneeregenschauer",
		"84" => "mäßiger oder starker Schneeregenschauer",
		"85" => "leichter Schneeschauer",
		"86" => "mäßiger oder starker Schneeschauer",
		"87" => "leichter Graupelschauer",
		"88" => "mäßiger oder starker Graupelschauer",
		"89" => "leichter Hagelschauer",
		"90" => "mäßiger oder starker Hagelschauer",
		"91" => "Gewitter in der letzten Stunde, zurzeit leichter Regen",
		"92" => "Gewitter in der letzten Stunde, zurzeit mäßiger oder starker Regen",
		"93" => "Gewitter in der letzten Stunde, zurzeit leichter Schneefall/Schneeregen/Graupel/Hagel",
		"94" => "Gewitter in der letzten Stunde, zurzeit mäßiger oder starker Schneefall/Schneeregen/Graupel/Hagel",
		"95" => "leichtes oder mäßiges Gewitter mit Regen oder Schnee",
		"96" => "leichtes oder mäßiges Gewitter mit Graupel oder Hagel",
		"97" => "starkes Gewitter mit Regen oder Schnee",
		"98" => "starkes Gewitter mit Sandsturm",
		"99" => "starkes Gewitter mit Graupel oder Hagel",
		"" => NULL
	);
	
	#section 5 - 8NChh group
	private $cloud_cover_code = array
	(
		"0" => 0,		// "0/8 (wolkenlos)",
		"1" => 1,		// "1/8 oder weniger (fast wolkenlos)",
		"2" => 3,		// "2/8 (leicht bewölkt)",
		"3" => 4,		// "3/8",
		"4" => 5,		// "4/8 (wolkig)",
		"5" => 6,		// "5/8",
		"6" => 8,		// "6/8 (stark bewölkt)",
		"7" => 9,		// "7/8 oder mehr (fast bedeckt)",
		"8" => 10,		// "8/8 (bedeckt)",
		"9" => NULL,	// "Himmel nicht erkennbar",
		"/" => NULL		// "nicht beobachtet"
	);

	#section 5 - 8NChh group
	private $CLOUD_TYPE_CODE = array
	(
		"0" => "Cirrus (Ci)",
		"1" => "Cirrocumulus (Cc)",
		"2" => "Cirrostratus (Cs)",
		"3" => "Altocumulus (Ac)",
		"4" => "Altostratus (As)",
		"5" => "Nimbostratus (Ns)",
		"6" => "Stratocumulus (Sc)",
		"7" => "Stratus (St)",
		"8" => "Cumulus (Cu)",
		"9" => "Cumulonimbus (Cb)",
		"/" => "Wolkengattung nicht erkennbar"
	);
	
	#section 7 - RRsTTk group
	/*
	 * Interpretation of phenomena tendency codes.
	*/
	private $P_TENDENCY_CODE = array
	(
		'1' => 'increasing',
		'2' => 'conservating or completing'
	);
	
	#section 9 - 950Nn group
	/*
	 * Interpretation of cloudness over mountains codes.
	*/
	private $CLOUDNESS_OVER_MOUNT_CODE = array
	(
		'0' => 'Все горы открыты (или имеется небольшое количество облаков)',
		'1' => 'Горы частично закрыты разрозненными облаками (видно не более половины вершин гор)',
		'2' => 'Все склоны гор закрыты облаками, вершины гор и перевалы открыты',
		'3' => 'Горы открыты со стороны наблюдателя (или имеется небольшое количество облаков), но за горами – сплошная стена облаков',
		'4' => 'Над горами низко нависла облачность, вершины гор и склоны открыты (или имеется небольшое количество облаков)',
		'5' => 'Над горами низко нависла облачность, вершины гор частично закрыты облаками или полосами падения осадков',
		'6' => 'Все вершины гор закрыты облаками, перевалы открыты, склоны открыты или закрыты ',
		'7' => 'Горы в основном закрыты облаками (видны лишь отдельные вершины гор, а склоны закрыты полностью или частично)',
		'8' => 'Все вершины гор, перевалы и склоны закрыты облаками',
		'9' => 'Горы не видны из-за темноты, тумана, метели, осадков и т.д.'
	);
	
	#section 9 - 950Nn group
	/*
	 * Interpretation of cloud evolution codes.
	*/
	private $CLOUD_EVOLUTION_CODE = array
	(
		'0' => 'No changes',												// 'Без изменений',
		'1' => 'Development of cumulus cloudiness',							// 'Развитие облачности кучевых форм',
		'2' => 'The rise of clouds is slow',								// 'Подъём облачности медленный',
		'3' => 'Clouds rise fast',											// 'Подъём облачности быстрый',
		'4' => 'The rise of clouds, the clouds rose and became layered',	// 'Подъём облачности, облачность поднялась и стала слоистой',
		'5' => 'Decrease of cloudiness is slow',							// 'Снижение облачности медленное',
		'6' => 'Cloudiness is decreasing fast',								// 'Снижение облачности быстрое',
		'7' => 'Development of cloud layering',								// 'Развитие слоистости облачности',
		'8' => 'Development of bedding and decrease in cloudiness',			// 'Развитие слоистости и снижение облачности',
		'9' => 'Rapid changes'												// 'Быстрые изменения'
	);
	
/***********END STORM OPTIONS ******************************************************/

	/*
	 * Interpretation of weather conditions type codes.
	*/
	/*

	/*
	 * Debug and parse errors information.
	*/
	private $errors = NULL;
	private $debug  = NULL;
	private $debug_enabled;

	/*
	 * Other variables.
	*/
	private $raw;
	private $raw_parts = array();
	private $method    = 0;
	private $part      = 0;


	/**
	 * This method provides STORM information, you want to parse.
	 *
	 * Examples of raw STORM for test:
	 * 26923 09 05101 30 86703=
	 * 26923 09 05101 19 21881=
	 * 26923 09 05101 75 362001=
	 * 26923 09 05101 36 71535//=
	 * 26923 09 05101 30 88505=
	 * 26923 09 05101 92 93208=
	 * 26923 09 05101 55 320032=
	 * 26923 09 05101 68 102=
	 * 26923 09 05101 41 95063=
	 * 26923 09 05101 40 71535// 1180811=
	 * 26923 09 05101 30 1110209=
	 * 26509 09 09451 11 12009 1//15=
	*/
	public function __construct($raw, $debug = FALSE)
	{
		$this->debug_enabled = $debug;
		
		if (empty($raw))
		{
			throw new Exception('The STORM information is not presented.');
		}

		$raw_lines = explode("\n", $raw, 2);

		if (isset($raw_lines[1]))
		{
			$raw = trim($raw_lines[1]);

			// Get observed time from a file data
			$observed_time = strtotime(trim($raw_lines[0]));

			if ($observed_time != 0)
			{
				$this->set_observed_date($observed_time);

				$this->set_debug('Observation date is set from the STORM in first line of the file content: '.trim($raw_lines[0]));
			}
		}
		else
		{
			$raw = trim($raw_lines[0]);
		}

		$this->raw = rtrim(trim(preg_replace('/[\s\t]+/s', ' ', $raw)), '=');

		$this->set_debug('Infromation presented as STORM.');

		$this->set_result_value('raw', $this->raw);
	}

	/**
	 * Gets the value from result array as class property.
	*/
	public function __get($parameter)
	{
		if (isset($this->result[$parameter]))
		{
			return $this->result[$parameter];
		}

		return NULL;
	}

	/**
	 * Parses the STORM information and returns result array.
	*/
	public function parse()
	{
		$this->raw_parts = explode(' ', $this->raw);

		$current_method = 0;

		// See parts
		while ($this->part < sizeof($this->raw_parts))
		{
			$this->method = $current_method;

			// See methods
			while ($this->method < sizeof($this->method_names))
			{
				$method = 'get_'.$this->method_names[$this->method];
				$token  = $this->raw_parts[$this->part];

				if ($this->$method($token) === TRUE)
				{
					$this->set_debug('Token "'.$token.'" is parsed by method: '.$method.', '.
						($this->method - $current_method).' previous methods skipped.');

					$current_method = $this->method;

					$this->method++;

					break;
				}

				$this->method++;
			}

			if ($current_method != $this->method - 1)
			{
				$this->set_error('Unknown token: '.$this->raw_parts[$this->part]);
				$this->set_debug('Token "'.$this->raw_parts[$this->part].'" is NOT PARSED, '.
						($this->method - $current_method).' methods attempted.');
			}

			$this->part++;
		}

		return $this->result;
	}

	/**
	 * Returns array with debug information.
	*/
	public function debug()
	{
		return $this->debug;
	}

	/**
	 * Returns array with parse errors.
	*/
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * This method formats observation date and time in the local time zone of server, 
	 * the current local time on server, and time difference since observation. $time_utc is a
	 * UNIX timestamp for Universal Coordinated Time (Greenwich Mean Time or Zulu Time).
	*/
	private function set_observed_date($time_utc)
	{
		$local = $time_utc + date('Z');
		$now   = time();

		$this->set_result_value('observed_date', date('r', $local)); // or "D M j, H:i T"

		$time_diff = floor(($now - $local) / 60);

		if ($time_diff < 91)
		{
			$this->set_result_value('observed_age', $time_diff.' min. ago');
		}
		else
		{
			$this->set_result_value('observed_age', floor($time_diff / 60).':'.sprintf("%02d", $time_diff % 60).' hr. ago');
		}
	}

	/**
	 * Sets the new value to parameter in result array.
	*/
	private function set_result_value($parameter, $value, $only_is_null = FALSE)
	{
		if ($only_is_null)
		{
			if (is_null($this->result[$parameter]))
			{
				$this->result[$parameter] = $value;

				$this->set_debug('Set value "'.$value.'" ('.gettype($value).') for null parameter: '.$parameter);
			}
		}
		else
		{
			$this->result[$parameter] = $value;

			$this->set_debug('Set value "'.$value.'" ('.gettype($value).') for parameter: '.$parameter);
		}
	}

	/**
	 * Sets the data group to parameter in result array.
	*/
	private function set_result_group($parameter, $group)
	{
		if (is_null($this->result[$parameter]))
		{
			$this->result[$parameter] = array();
		}

		array_push($this->result[$parameter], $group);

		$this->set_debug('Add new group value ('.gettype($group).') for parameter: '.$parameter);
	}

	/**
	 * Sets the report text to parameter in result array.
	*/
	private function set_result_report($parameter, $report, $separator = ';')
	{
		$this->result[$parameter] .= $separator.' '.$report;

		if (!is_null($this->result[$parameter]))
		{
			$this->result[$parameter] = ucfirst(ltrim($this->result[$parameter], ' '.$separator));
		}

		$this->set_debug('Add group report value "'.$report.'" for parameter: '.$parameter);
	}

	/**
	 * Adds the debug text to debug information array.
	*/
	private function set_debug($text)
	{
		if ($this->debug_enabled)
		{
			if (is_null($this->debug))
			{
				$this->debug = array();
			}

			array_push($this->debug, $text);
		}
	}

	/**
	 * Adds the error text to parse errors array.
	*/
	private function set_error($text)
	{
		if (is_null($this->errors))
		{
			$this->errors = array();
		}

		array_push($this->errors, $text);
	}

	// --------------------------------------------------------------------
	// Methods for parsing raw parts
	// --------------------------------------------------------------------

	/**
	 * Decodes observation time.
	 * Format is YYYYMMddhhmm where YYYY = year, MM = month, dd = day, hh = hours, mm = minutes in UTC time.
	*/
	/*
	private function get_time($part)
	{
		//if (!preg_match('@^([0-9]{2})([0-9]{2})([0-9]{2})Z$@', $part, $found))
		if (!preg_match('@^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$@', $part, $found))
		{
			return FALSE;
		}

		$year   = intval($found[1]);
		$month  = intval($found[2]);
		$day    = intval($found[3]);
		$hour   = intval($found[4]);
		$minute = intval($found[5]);

		if (is_null($this->result['observed_date']))
		{
			// Get observed time from a Storm part
			//$observed_time = mktime($hour, $minute, 0, date('n'), $day, date('Y'));
			$observed_time = mktime($hour, $minute, 0, 1*$month, $day, $year);

			// Take one month, if the observed day is greater than the current day
			if ($day > date('j'))
			{
				$observed_time = strtotime('-1 month');
			}

			$this->set_observed_date($observed_time);

			$this->set_debug('Observation date is set from the Storm information (presented in format: YYYYMMddhhmm)');
		}

		$this->set_result_value('observed_day', $day);
		$this->set_result_value('observed_time', $hour.':'.$minute.' UTC');

		$this->method++;

		return TRUE;
	}
	*/

	/**
	 * Decodes station type code.
	 * section 0 - MMMM group
	 * A SYNOP report (FM12) from a ﬁxed land station is identiﬁed by the symbolic letters MiMiMjMj = AAXX.
	 * A SHIP report (FM13) from a sea station is identiﬁed by the symbolic letters MiMiMjMj = BBXX.
	 * A SYNOP MOBIL (FM14) report from a mobile land station is identiﬁed by the symbolic letters MiMiMjMj = OOXX.
	 
	 * Parameters
	 * ----------
	 * MMMM: 
	*/
	/*
	private function get_station($part)
	{
		if (!preg_match('@^(AAXX|BBXX|OOXX)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('station_report', $this->STATION_TYPE_CODE[$found[1]]);

		$this->method++;

		return TRUE;
	}
	*/

	/**
	 * Ignore station type if present.
	*/
	/*
	private function get_station_type($part)
	{
		if ($part != 'AUTO' AND $part != 'COR')
		{
			return FALSE;
		}

		$this->method++;

		return TRUE;
	}
	*/
	
	/**
	 * Decodes .
	 * section 0 - YYGGGGi group
	 * 99LLL QLLLL group
	 
	 * Parameters
	 * ----------
	 * YY: 
	 * GG: 
	 * i: 
	*/
	private function get_date($part)
	{
		if (!preg_match('@^([0-9]{2})$@', $part, $found))  // for Ukraine standart
		{
			return FALSE;
		}

		$this->set_result_value('monthdayr', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes .
	 * section 0 - YYGGGGi group
	 
	 * Parameters
	 * ----------
	 * YY: 
	 * GGGG: 
	 * i: 
	*/
	private function get_time($part)
	{
		//if (!preg_match('@^([0-9]{2})([0-9]{4})([0-9]{1})$@', $part, $found))  // for WMO standart
		if (!preg_match('@^([0-9]{2})([0-9]{2})([0-9]{1})$@', $part, $found))  // for Ukraine standart
		{
			return FALSE;
		}

		$wind_unit = $this->WIND_UNIT_CODE[$found[3]];

		$this->set_result_value('hourr', $found[1]);
		$this->set_result_value('minuter', $found[2]);
		$this->set_result_value('wind_unit', $wind_unit);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes station id.
	 * section 0 - IIiii group
	 
	 * Parameters
	 * ----------
	 * II: 
	 * iii: 
	*/
	private function get_station_id($part)
	{
		if (!preg_match('@^([0-9]{5})$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('station_id', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №1.
	 * section 1 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (11, 12, 17, 18, 19, 36, 78)
	*/
	private function get_storm_code_1($part)
	{
		if (!preg_match('@^(11|12|17|18|19|36|78)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_1', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes wind direction, speed and gust information.
	 * section 1 - 1ddfffxfx group
	 
	 * Parameters
	 * ----------
	 * dd: wind direction in dekadegree (10 minute mean)
	 * ff: wind speed (10 minute mean)
	 * fxfx: max gust speed (>=12 mps)
	*/
	private function get_wind($part)
	{
		if (!preg_match('@^1([0-9/]{2})([0-9/]{2})([0-9/]{0,2})$@', $part, $found))
		{
			return FALSE;
		}

		$wind_dir_code = $found[1];
        if ($wind_dir_code == "") {
            $wind_dir = NULL;  //not observed
		}
        else {
			//$wind_dir = $this->wind_dir_degrees[$wind_dir_code]);  // in degrees
			$wind_dir = $this->wind_dir_compass[$wind_dir_code];  // in rhumb
		}

		$wind_speed_code = $found[2];
        if ($wind_speed_code == "" || $wind_speed_code == "//") {
            $wind_speed = NULL;  //not observed
		}
        else {
			$wind_speed = intval($wind_speed_code);  // in mps
		}
		
		$wind_gust_code = $found[3];
        if ($wind_gust_code == "" || $wind_gust_code == "//") {
            $wind_gust = NULL;  //not observed
		}
        else {
			$wind_gust = intval($wind_gust_code);  // in mps
		}

		$this->set_result_value('wind_direction', $wind_dir);
		$this->set_result_value('wind_speed', $wind_speed);
		$this->set_result_value('wind_gust', $wind_gust);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №2.
	 * section 2 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (19, 91)
	*/
	private function get_storm_code_2($part)
	{
		if (!preg_match('@^(19|91)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_2', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes wind direction and current weather information.
	 * section 2 - 2ddww group
	 
	 * Parameters
	 * ----------
	 * dd: wind direction in dekadegree (10 minute mean)
	 * ww: current weather
	*/
	private function get_wind_recent_weather($part)
	{
		if (!preg_match('@^2([0-9/]{2})([0-9]{2})$@', $part, $found))
		{
			return FALSE;
		}

		$wind_dir_code = $found[1];
        if ($wind_dir_code == "") {
            $wind_dir = NULL;  //not observed
		}
        else {
			//$wind_dir = $this->wind_dir_degrees[$wind_dir_code]);  // in degrees
			$wind_dir = $this->wind_dir_compass[$wind_dir_code];  // in rhumb
		}
		
		if (intval($found[2]) >= 80 && intval($found[2]) <= 90) {
			$current_weather = $this->CURRENT_WEATHER_CODE[$found[2]];
		} else {
			$current_weather = NULL;
		}

		$this->set_result_value('wind_direction', $wind_dir);
		$this->set_result_value('current_weather', $current_weather);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №3.
	 * section 3 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (61, 62, 64, 65, 66, 71, 75)
	*/
	private function get_storm_code_3($part)
	{
		if (!preg_match('@^(61|62|64|65|66|71|75)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_3', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes amount of precipitation per perion in hours.
	 * section 3 - 3RRRtt group
	 
	 * Parameters
	 * ----------
	 * RRR: precipitation amount in mm
	 * tt: period in hours
	*/
	private function get_precip_period($part)
	{
		if (!preg_match('@^3([0-9/]{3})([0-9/]{2})$@', $part, $found))
		{
			return FALSE;
		}

        if ($found[1] == "///") {
            $precip = NULL;
		}
        else {
			$precip = intval($found[1]);
			if ($precip >= 990 && $precip <= 999) {
				$precip = ($precip - 990) * 0.1;
				if ($precip == 0) {
					//only traces of precipitation not measurable < 0.05
					$precip = 0;  //0.05
				}
			}
		}
		
		$storm_period = intval($found[2]);
		if ($storm_period == 0 || (strpos($found[2], "/") !== false)) {
			$storm_period = NULL;
		}

		$this->set_result_value('precip', $precip);
		$this->set_result_value('storm_period', $storm_period);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №4.
	 * section 4 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (36, 40, 41, 78)
	*/
	private function get_storm_code_4($part)
	{
		if (!preg_match('@^(36|40|41|78)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_4', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes visibility, current weather and period in hours.
	 * section 4 - 7VVwwtt group
	 
	 * Parameters
	 * ----------
	 * VV: horizontal visibility in km
	 * ww: current weather
	 * tt: storm period in hours
	*/
	private function get_vis_weather_period($part)
	{
		if (!preg_match('@^7([0-9/]{2})([0-9]{2})([0-9/]{2})$@', $part, $found))
		{
			return FALSE;
		}

		$visibility = intval($found[1]) / 10;
		$current_weather = $this->CURRENT_WEATHER_CODE[$found[2]];
		
		$storm_period = intval($found[3]);
		if ($storm_period == 0 || (strpos($found[3], "/") !== false)) {
			$storm_period = NULL;
		}

		$this->set_result_value('visibility', $visibility);
		$this->set_result_value('current_weather', $current_weather);
		$this->set_result_value('storm_period', $storm_period);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №5.
	 * section 5 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (30, 40) 
	*/
	private function get_storm_code_5($part)
	{
		if (!preg_match('@^(30|40)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_5', $found[1]);

		$this->method++;

		return TRUE;
	}

	/**
	 * Decodes report of cloud layers.
	 * section 5 - 8NChh group
	 
	 * Parameters
	 * ----------
	 * N: cloud cover in okta
	 * 	If 9 obscured by fog or other meteorological phenomena.	
	 * 	If / observation not made or not possible due to phenomena other than 9
	 * C: cloud type
	 * hh: height of cloud base in m
	 
	 * Returns
	 * -------
	 * int
	 * 	Cloud height in m
	*/
    private function set_hh($code) {
        // Decode cloud height of Storm report
		// cloud height in meters
		
		//cloud height assessed visually 
		$CLOUD_HEIGHT_CLASSES = array(
			90 => 0,  //<50
			91 => 50,
			92 => 100,
			93 => 200,
			94 => 300,
			95 => 600,
			96 => 1000,
			97 => 1500,
			98 => 2000,
			99 => 2500
		);
		
        if ($code == "" || $code == "//") {
            $code = NULL;  //not observed
		}
        else {
			$code = intval($code);
		}
			
		//not used 51-55
        if ($code <= 50) {
            $c_height = $code * 30;
		}
        elseif ($code >= 56 and $code <= 80) {
            $c_height = 1800 + (code - 56) * 300;
		}
        elseif ($code >= 81 and $code <= 89) {
            $c_height = 10500 + (code - 81) * 1500;
		}
        else {
            $c_height = $CLOUD_HEIGHT_CLASSES[$code];
		}
		
        return $c_height;
	}

	private function get_cloud_report($part)
	{
		if (!preg_match('@^8([0-9]{1})([0-9/]{1})([0-9/]{2})$@', $part, $found))
		{
			return FALSE;
		}
		
		$cloud_cover_lowest = $this->cloud_cover_code[$found[1]];
		$clouds_shape = $this->CLOUD_TYPE_CODE[$found[2]];
		$cld_low_height = $this->set_hh($found[3]);

		$this->set_result_value('amount_cloudiness', $cloud_cover_lowest);
		$this->set_result_value('clouds_shape', $clouds_shape);
		$this->set_result_value('cld_low_height', $cld_low_height);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №6.
	 * section 6 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (90, 92)
	*/
	private function get_storm_code_6($part)
	{
		if (!preg_match('@^(90|92)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_6', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes hail or rime ice diameter in mm.
	 * section 6 or 7 - 932RR or RRsTTk group
	 
	 * Parameters
	 * ----------
	 * RR: hail or rime ice diameter in mm
	 
	 * Returns
	 * -------
	 * float
	 * 	diameter in mm
	*/
    private function set_RR($code) {
        if ($code == "//" || $code == 99) {
            $d_value = NULL;
		}
        else {
			$d_value = intval($code);
			if ($d_value >= 56 && $d_value <= 90) {
				$d_value = ($d_value - 50) * 10;
			}
			elseif ($d_value >= 91 && $d_value <= 96) {
				$d_value = ($d_value - 90) * 0.1;
			}
			elseif ($d_value == 97) {
				$d_value = "<0.1";
			}
			elseif ($d_value == 98) {
				$d_value = ">400";
			}
		}
		
        return $d_value;
	}
	
	/**
	 * Decodes hail size.
	 * section 6 - 932RR group
	 
	 * Parameters
	 * ----------
	 * RR: hail diameter in mm
	*/
	private function get_hail_size($part)
	{
		if (!preg_match('@^932([0-9]{2})$@', $part, $found))
		{
			return FALSE;
		}
		
		$hail_size = $this->set_RR($found[1]);

		$this->set_result_value('hail_size', $hail_size);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №7.
	 * section 7 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (50, 51, 52, 53, 54, 55, 56, 57) 
	*/
	private function get_storm_code_7($part)
	{
		if (!preg_match('@^(50|51|52|53|54|55|56|57)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_7', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes temperature information.
	 * All units are in Celsius. A 's' preceeding the TT indicates a sign of temperature.
	 * section 7 or 8 - RRsTTk or sTT group
	 
	 * Parameters
	 * ----------
	 * code : str
	 * 	Temperature with first charater defining the sign or
	 * 	type of unit (°C)

	 * Returns
	 * -------
	 * int
	 * 	Temperature in degree Celsius
	*/
	private function set_sTT($code) {
        if ($code == "" || (strpos($code, "/") !== false)) {
            return NULL;
		}
        else {
            $sign = intval(substr($code, 0, 1));
            $value = intval(substr($code, 1));

            if ($sign == 0) {
                $sign = 1;
			}
            elseif ($sign == 1) {
                $sign = -1;
			}

			$value = $sign * $value;

            return $value;
		}
	}
	
	/**
	 * Decodes size rime ice deposits, air temperature and their tendency.
	 * section 7 - RRsTTk group
	 
	 * Parameters
	 * ----------
	 * RR: rime ice diameter in mm
	 * s: sign of temperature
	 * TT: air temperature in degree Celsius (°C)
	 * k: trend of phenomena
	*/
	private function get_rime_temp_trend($part)
	{
		if (!preg_match('@^([0-9]{2})([0-9/]{3})([0-9]{1})$@', $part, $found))
		{
			return FALSE;
		}
		
		$rime_ice_size = $this->set_RR($found[1]);
		$temperature = $this->set_sTT($found[2]);
		$trend_phenomena = $this->P_TENDENCY_CODE[$found[3]];

		$this->set_result_value('rime_ice_size', $rime_ice_size);
		$this->set_result_value('temperature', $temperature);
		$this->set_result_value('trend_phenomena', $trend_phenomena);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №8.
	 * section 8 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather (68)
	*/
	private function get_storm_code_8($part)
	{
		if (!preg_match('@^(68)$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_8', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes air temperature in degree Celsius.
	 * section 8 - sTT group
	 
	 * Parameters
	 * ----------
	 * s: sign of temperature
	 * TT: air temperature in degree Celsius (°C)
	*/
	private function get_temperature($part)
	{
		if (!preg_match('@^([0-9/]{3})$@', $part, $found))
		{
			return FALSE;
		}
		
		$temperature = $this->set_sTT($found[1]);

		$this->set_result_value('temperature', $temperature);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes code of storm weather №9.
	 * section 9 - CC group
	 
	 * Parameters
	 * ----------
	 * CC: code of storm weather 
	*/
	private function get_storm_code_9($part)
	{
		if (!preg_match('@^([0-9]{2})$@', $part, $found))
		{
			return FALSE;
		}

		$this->set_result_value('storm_code_9', $found[1]);

		$this->method++;

		return TRUE;
	}
	
	/**
	 * Decodes cloudiness over mountains and passes, cloud evolution.
	 * section 9 - 950Nn group
	 
	 * Parameters
	 * ----------
	 * N: cloudiness over mountains and passes
	 * n: cloud evolution
	*/
	private function get_cloud_mount($part)
	{
		if (!preg_match('@^950([0-9]{1})([0-9]{1})$@', $part, $found))
		{
			return FALSE;
		}
		
		$cloudness_over_mount = $this->CLOUDNESS_OVER_MOUNT_CODE[$found[1]];
		$cloud_evolution = $this->CLOUD_EVOLUTION_CODE[$found[2]];

		$this->set_result_value('cloudness_over_mount', $cloudness_over_mount);
		$this->set_result_value('cloud_evolution', $cloud_evolution);

		$this->method++;

		return TRUE;
	}
}
?>
