<?php

$action = isset($_GET['action']) ? $_GET['action'] : null;
$title = isset($_GET['title']) ? $_GET['title'] : null;
$kp_id = isset($_GET['kp_id']) ? $_GET['kp_id'] : null;
$id_news = isset($_GET['id_news']) ? $_GET['id_news'] : 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : null;

$is_logged = false;

require_once ENGINE_DIR . '/dlepremium/kinopoisk_parser/functions/module.php';
require_once ENGINE_DIR . '/dlepremium/kinopoisk_parser/data/config.php';

@header('Content-type: text/html; charset=' . $config['charset']);

date_default_timezone_set($config['date_adjust']);

if(!$user_group) $user_group = get_vars( "usergroup" );
if( !$user_group ) {
    $user_group = array ();

    $us = $dle_api->load_table( USERPREFIX . "_usergroups", '*',1,true,0,0, 'id', 'asc');

    foreach ( $us as $row) {

        $user_group[$row['id']] = array ();

        foreach ( $row as $key => $value ) {
            $user_group[$row['id']][$key] = stripslashes($value);
        }

    }
    set_vars( "usergroup", $user_group );
}

if (!$member_id) $member_id = get_vars( "member_id" );
if (!$member_id) {
    if (!isset($_COOKIE['dle_user_id'])) die("Пройдите авторизацию на сайте!");
    $member_id = $dle_api->load_table(USERPREFIX . '_users', '*', "user_id = {$_COOKIE['dle_user_id']}");
    set_vars('member_id', $member_id);
}

if (!$langs || !$langtranslit) {
    $selected_language = $config['langs'];

    if (isset($_COOKIE['selected_language'])) {

        $_COOKIE['selected_language'] = trim(totranslit($_COOKIE['selected_language'], false, false));

        if ($_COOKIE['selected_language'] != "" and @is_dir(ROOT_DIR . '/language/' . $_COOKIE['selected_language'])) {
            $selected_language = $_COOKIE['selected_language'];
        }

    }

    if (file_exists(DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/website.lng'))) {
        include_once(DLEPlugins::Check(ROOT_DIR . '/language/' . $selected_language . '/website.lng'));
    }
}

$cat_type = ['FILM' => 'Фильм', 'TV_SERIES' => 'Сериал', 'MINI_SERIES' => 'Сериал', 'TV_SHOW' => 'ТВ-Шоу'];
$cat_type_en = ['FILM' => 'movie', 'TV_SERIES' => 'tvserial', 'MINI_SERIES' => 'tvserial', 'TV_SHOW' => 'tvshow'];

if ( $action == "parser_search" ) {
    
    if ( ctype_digit($title) ) $kinopoisk_id = $title;
    elseif( preg_match( "#kinopoisk.ru/film/(.+?)/#i", $title, $match ) ) {
		$kinopoisk_id = trim($match[1]);
	}
    elseif( preg_match( "#kinopoisk.ru/series/(.+?)/#i", $title, $match ) ) {
		$kinopoisk_id = trim($match[1]);
	}
    elseif ( isset($title) ) $search_name = urlencode($title);
    
    $parse_action = 'search';
    include_once (DLEPlugins::Check(ENGINE_DIR . '/dlepremium/kinopoisk_parser/donors/kinopoisk.php'));
	
	$responseArray = unique_multidim_array($responseArray,'kp_id');
	
	if ($responseArray) {
		die(json_encode(array(
			'status' => 'results',
			'result' => $responseArray,
		)));
	} else {
		die(json_encode(array(
			'status' => 'error',
			'error' => '#02',
		)));
	}
}
elseif ( $action == "kinopoisk_get" ) {
    
    include_once(DLEPlugins::Check(ENGINE_DIR . '/classes/uploads/upload.class.php'));
	
	$parse_action = 'parse';
	include_once (DLEPlugins::Check(ENGINE_DIR . '/dlepremium/kinopoisk_parser/donors/kinopoisk.php'));
	include_once (DLEPlugins::Check(ENGINE_DIR . '/dlepremium/kinopoisk_parser/donors/tabus.php'));
	
	if ( $mode != 'editnews' && $array_data['poster'] && $kp_config['images']['poster'] == 1 ) $need_poster = true;
    elseif ( $mode == 'editnews' && $array_data['poster'] && $kp_config['images']['poster_edit'] == 1 ) $need_poster = true;
    else $need_poster = false;
    
    if ( $need_poster === true ) {
        $poster_parsed = true;
        if ( $array_data['russian'] ) $poster_file = totranslit_it($array_data['russian'], true, false);
        else $poster_file = totranslit_it($array_data['original'], true, false);
        $poster = setPoster($array_data['poster'], $poster_file, $kp_config['images']['xf_poster'], $id_news);
        $array_data['poster'] = $poster['link'];
        $xf_poster = $poster['xfvalue'];
	    $poster_code = $poster['returnbox'];
    }
	
	if ( $mode != 'editnews' && $kp_config['images']['screens'] == 1 ) $need_screens = true;
	elseif ( $mode == 'editnews' && $kp_config['images']['screens_edit'] == 1 ) $need_screens = true;
	else $need_screens = false;
	
	if ( $need_screens === true ) {
	    
	    $screens_list = api_request('https://kinopoiskapiunofficial.tech/api/v2.1/films/'.$kp_id.'/frames', $kp_config['settings']['kinopoiskapiunofficial'] );
	    if ( $screens_list['frames'] ) {
	        
	        if ( $array_data['russian'] ) $screen_named = totranslit_it($array_data['russian'], true, false);
            else $screen_named = totranslit_it($array_data['original'], true, false);
	        
	        if ( $screens_list['frames'][0]['image'] AND 1 <= $kp_config['images']['screens_count'] ) {
                $screen_1_file = $screen_named.'_kadr_1';
                $screen_1 = setPoster($screens_list['frames'][0]['image'], $screen_1_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_1'] = $screen_1['link'];
                $xf_screen_1 = $screen_1['xfvalue'];
	            $screens_code = $screen_1['returnbox'];
	        }
	        else $array_data['screenshot_1'] = '';
	        
	        if ( $screens_list['frames'][1]['image'] AND 2 <= $kp_config['images']['screens_count'] ) {
                $screen_2_file = $screen_named.'_kadr_2';
                $screen_2 = setPoster($screens_list['frames'][1]['image'], $screen_2_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_2'] = $screen_2['link'];
                $xf_screen_2 = ",".$screen_2['xfvalue'];
	            $screens_code .= $screen_2['returnbox'];
	        }
	        else $array_data['screenshot_2'] = '';
	        
	        if ( $screens_list['frames'][2]['image'] AND 3 <= $kp_config['images']['screens_count'] ) {
                $screen_3_file = $screen_named.'_kadr_3';
                $screen_3 = setPoster($screens_list['frames'][2]['image'], $screen_3_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_3'] = $screen_3['link'];
                $xf_screen_3 = ",".$screen_3['xfvalue'];
	            $screens_code .= $screen_3['returnbox'];
	        }
	        else $array_data['screenshot_3'] = '';
	        
	        if ( $screens_list['frames'][3]['image'] AND 4 <= $kp_config['images']['screens_count'] ) {
                $screen_4_file = $screen_named.'_kadr_4';
                $screen_4 = setPoster($screens_list['frames'][3]['image'], $screen_4_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_4'] = $screen_4['link'];
                $xf_screen_4 = ",".$screen_4['xfvalue'];
	            $screens_code .= $screen_4['returnbox'];
	        }
	        else $array_data['screenshot_4'] = '';
	        
	        if ( $screens_list['frames'][4]['image'] AND 5 <= $kp_config['images']['screens_count'] ) {
                $screen_5_file = $screen_named.'_kadr_5';
                $screen_5 = setPoster($screens_list['frames'][4]['image'], $screen_5_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_5'] = $screen_5['link'];
                $xf_screen_5 = ",".$screen_5['xfvalue'];
	            $screens_code .= $screen_5['returnbox'];
	        }
	        else $array_data['screenshot_5'] = '';
	        
	        if ( $screens_list['frames'][5]['image'] AND 6 <= $kp_config['images']['screens_count'] ) {
                $screen_6_file = $screen_named.'_kadr_6';
                $screen_6 = setPoster($screens_list['frames'][5]['image'], $screen_6_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_6'] = $screen_6['link'];
                $xf_screen_6 = ",".$screen_6['xfvalue'];
	            $screens_code .= $screen_6['returnbox'];
	        }
	        else $array_data['screenshot_6'] = '';
	        
	        if ( $screens_list['frames'][6]['image'] AND 7 <= $kp_config['images']['screens_count'] ) {
                $screen_7_file = $screen_named.'_kadr_7';
                $screen_7 = setPoster($screens_list['frames'][6]['image'], $screen_7_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_7'] = $screen_7['link'];
                $xf_screen_7 = ",".$screen_7['xfvalue'];
	            $screens_code .= $screen_7['returnbox'];
	        }
	        else $array_data['screenshot_7'] = '';
	        
	        if ( $screens_list['frames'][7]['image'] AND 8 <= $kp_config['images']['screens_count'] ) {
                $screen_8_file = $screen_named.'_kadr_8';
                $screen_8 = setPoster($screens_list['frames'][7]['image'], $screen_8_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_8'] = $screen_8['link'];
                $xf_screen_8 = ",".$screen_8['xfvalue'];
	            $screens_code .= $screen_8['returnbox'];
	        }
	        else $array_data['screenshot_8'] = '';
	        
	        if ( $screens_list['frames'][8]['image'] AND 9 <= $kp_config['images']['screens_count'] ) {
                $screen_9_file = $screen_named.'_kadr_9';
                $screen_9 = setPoster($screens_list['frames'][8]['image'], $screen_9_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_9'] = $screen_9['link'];
                $xf_screen_9 = ",".$screen_9['xfvalue'];
	            $screens_code .= $screen_9['returnbox'];
	        }
	        else $array_data['screenshot_9'] = '';
	        
	        if ( $screens_list['frames'][9]['image'] AND 10 <= $kp_config['images']['screens_count'] ) {
                $screen_10_file = $screen_named.'_kadr_10';
                $screen_10 = setPoster($screens_list['frames'][9]['image'], $screen_10_file, $kp_config['images']['xf_screens'], $id_news);
                
                $array_data['screenshot_10'] = $screen_10['link'];
                $xf_screen_10 = ",".$screen_10['xfvalue'];
	            $screens_code .= $screen_10['returnbox'];
	        }
	        else $array_data['screenshot_10'] = '';
	        
	    }
	    else {
	        $array_data['screenshot_1'] = '';
            $array_data['screenshot_2'] = '';
            $array_data['screenshot_3'] = '';
            $array_data['screenshot_4'] = '';
            $array_data['screenshot_5'] = '';
            $array_data['screenshot_6'] = '';
            $array_data['screenshot_7'] = '';
            $array_data['screenshot_8'] = '';
            $array_data['screenshot_9'] = '';
            $array_data['screenshot_10'] = '';
	    }
	}
	
	$tags_array = array();
	if ( $array_data['year'] ) $tags_array[] = $array_data['year'];
	if ( $array_data['type_ru'] ) $tags_array[] = $array_data['type_ru'];
	if ( $array_data['status'] ) $tags_array[] = $array_data['status'];
	if ( $array_data['countries'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $array_data['countries'])));
	if ( $array_data['genres'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $array_data['genres'])));
	if ( $array_data['collections'] ) $tags_array = array_unique(array_merge($tags_array,explode(', ', $array_data['collections'])));
	
	
	$array_data['catalog_ru'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $array_data['russian'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	$array_data['catalog_eng'] = $db->safesql( dle_substr( htmlspecialchars( strip_tags( stripslashes( $array_data['original'] ) ), ENT_QUOTES, $config['charset'] ), 0, 1, $config['charset'] ) );
	
	//Обработка категорий
	
	if ( $kp_config['categories'] AND $tags_array ) {
		
		foreach ( $kp_config['categories'] as $key => $value ) {
		    $finded = true;
		    if ( strpos($value, ',') ) {
		        $value2 = explode(',', $value);
		        foreach ( $value2 as $value3 ) {
		            if ( !in_arrayi($value3, $tags_array) ) {
		                $finded = false;
		                break;
		            }
		        }
		    }
		    elseif( !in_arrayi($value, $tags_array) ) $finded = false;
		    if ( $finded ) $parse_cat_list[] = $key;
		}
		
		$parse_cat_list = implode(",", $parse_cat_list);
	
	}
	
	//Обработка категорий
	
	//Обработка шаблонов доп полей
    
    foreach($kp_config['xfields'] as $named => $zna4enie) {
         $array_data2[$named] = check_if($zna4enie, $array_data);
    }
	
	$array_data2['title'] = check_if($kp_config['xfields']['title'], $array_data);
    $array_data2['short_story'] = check_if($kp_config['xfields']['short_story'], $array_data);
    $array_data2['full_story'] = check_if($kp_config['xfields']['full_story'], $array_data);
    $array_data2['alt_name'] = check_if($kp_config['xfields']['alt_name'], $array_data);
    $array_data2['tags'] = check_if($kp_config['xfields']['tags'], $array_data);
    $array_data2['meta_titles'] = check_if($kp_config['xfields']['meta_title'], $array_data);
    $array_data2['meta_descrs'] = check_if($kp_config['xfields']['meta_description'], $array_data);
    $array_data2['meta_keywords'] = check_if($kp_config['xfields']['meta_keywords'], $array_data);
	$array_data2['catalog'] = check_if($kp_config['xfields']['catalog'], $array_data);
	
	$array_data2[$kp_config['fields']['xf_kinopoisk_id']] = $kp_id;
    
    $array_data2['parse_cat_list'] = $parse_cat_list;
    if ( $poster_code && $kp_config['images']['xf_poster'] ) {
        $array_data2['xf_poster'] = $poster_code;
        $array_data2['xf_poster_name'] = $kp_config['images']['xf_poster'];
        $array_data2['xf_poster_url'] = $xf_poster;
    }
    if ( $screens_code && $kp_config['images']['xf_screens'] ) {
        $array_data2['xf_screens'] = $screens_code;
        $array_data2['xf_screens_name'] = $kp_config['images']['xf_screens'];
        $array_data2['xf_screens_url'] = $xf_screen_1.$xf_screen_2.$xf_screen_3.$xf_screen_4.$xf_screen_5.$xf_screen_6.$xf_screen_7.$xf_screen_8.$xf_screen_9.$xf_screen_10;
    }
    
    
    
    if ($array_data2){

        die(json_encode(array(
            'status' => 'paste',
            'result' => $array_data2,
        ), JSON_UNESCAPED_UNICODE));

    } else {

        die(json_encode(array(
            'status' => 'error',
            'error' => '#02',
        )));

    }

}
else {

    die('Hacking attempt!');

}

?>
