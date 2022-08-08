<?php

dle_session();

require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

$_POST['user_hash'] = trim($_POST['user_hash']);
if ($_POST['user_hash'] == '' OR $_POST['user_hash'] != $dle_login_hash) {
	die('error');
}

if (!$is_logged && $member_id['user_group'] != 1) {
	die();
}

$action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : false;

if ($action == 'options') {
	$data_form = isset($_POST['data_form']) ? $_POST['data_form'] : false;
	if ($data_form) {
		parse_str($data_form, $array_post);
	}
	$new_array = [];
	foreach ($array_post as $index => $item) {
		foreach ($item as $key => $value) {
			if ($value != '' && $value != '-') {
				if (is_numeric($value)) {
					$value = intval($value);
				}
				elseif (is_array($value)) {
				    $value = implode(',', $value);
				}
				else {
					$value = strip_tags(stripslashes($value), '<li><br>');
				}
				$new_array[$index][$key] = $value;
			}
		}
	}
	$handler = fopen(ENGINE_DIR . '/dlepremium/kinopoisk_parser/data/config.php', "w");
	fwrite($handler, "<?PHP \n\n//Настройки \n\n\$kp_config = ");
	fwrite($handler, var_export($new_array, true));
	fwrite($handler, ";\n\n?>");
	fclose($handler);	
	echo json_encode(['success' => 'Ok']);
}
?>