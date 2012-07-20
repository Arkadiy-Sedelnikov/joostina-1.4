<?php

if(isset($_SERVER['HTTP_REFERER'])){
	// Установка флага родительского файла
	define('_JLINDEX', 1);

	define('DS', DIRECTORY_SEPARATOR);

	// подключение конфигурации
	require_once($_SERVER['DOCUMENT_ROOT'] . DS . 'configuration.php');

	// подключение языкового файла
	$path = __DIR__ . DS . 'lang';

	if(file_exists($path . DS . $mosConfig_lang . '.php')){
		require_once($path . DS . $mosConfig_lang . '.php');
	} else{
		require_once($path . DS . 'russian.php');
	}

	// балл
	$value = isset($_REQUEST['value']) ? intval($_REQUEST['value']) : 0;
	// ID контента
	$content_id = isset($_REQUEST['content_id']) ? intval($_REQUEST['content_id']) : 0;
	// IP
	$ip = isset($_REQUEST['ip']) ? intval($_REQUEST['ip']) : 0;
	// ID пользователя
	$user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
	// Каталог
	$directory = isset($_REQUEST['directory']) ? intval($_REQUEST['directory']) : 0;

	if($value == 0 OR $content_id == 0 OR ($ip == 0 AND $user_id == 0)){
		echo _GDRATING_MES08;
	} else{
		// количество звёздочек
		$units = isset($_REQUEST['units']) ? intval($_REQUEST['units']) : 10;
		// ширина звёздочки
		$width = isset($_REQUEST['width']) ? intval($_REQUEST['width']) : 30;

		// подключение класса для работы с базой
		require_once($_SERVER['DOCUMENT_ROOT'] . DS . 'includes' . DS . 'libraries' . DS . 'database' . DS . 'database.php');
		$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
		$sql = "INSERT INTO #__boss_" . $directory . "_rating (id, contentid, userid, value, ip, date) VALUES
				('', " . $content_id . ", " . $user_id . " , " . $value . ", " . $ip . ", " . time() . ");";
		$database->setQuery($sql);
		$database->query();

		$sql = "SELECT COUNT(*) AS count, SUM(value) AS sum
				FROM #__boss_" . $directory . "_rating
				WHERE `contentid` =" . $content_id;
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		require_once($_SERVER['DOCUMENT_ROOT'] . DS . 'includes' . DS . 'libraries' . DS . 'text' . DS . 'text.php');

		if(count($rows)){
			$row = $rows[0];
			$rating_width = @number_format($row->sum / $row->count, 2) * $width;

			$rating1 = ($row->count) ? number_format($row->sum / $row->count, 1) : "0.0";
			$rating2 = ($row->count) ? number_format($row->sum / $row->count, 2) : "0.00";

			$tense = Text::declension($row->count, array(_GDRATING_MES01, _GDRATING_MES02, _GDRATING_MES03));

			$static_rater = array();
			$static_rater[] .= '<div id="unit_long' . $content_id . '">';
			$static_rater[] .= '<ul id="unit_ul' . $content_id . '" class="unit-rating" style="width:' . $width * $units . 'px;">';
			$static_rater[] .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $units . '</li>';
			$static_rater[] .= '</ul>';
			$static_rater[] .= '<p class="static">' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $units . ' (' . $row->count . ' ' . $tense . ')';
			$static_rater[] .= '<br /><span class="thanks">' . _GDRATING_MES09 . '.</span></p>';
			$static_rater[] .= '</div>';
			echo join("", $static_rater);
		}
	}
} else{
	echo 'Нарушение безопасности: попытка прямого доступа';
}















