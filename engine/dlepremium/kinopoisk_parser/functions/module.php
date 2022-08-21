<?php

/*
=====================================================
 Copyright (c) 2022 DLEPremium
=====================================================
 This code is protected by copyright
=====================================================
*/

if (!function_exists('api_request')) {
    function api_request($url, $x_api_key) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
    		'Content-Type: application/json',
    		'X-API-KEY: '.$x_api_key
		];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$kp_api = curl_exec ($ch);
		curl_close ($ch);
  
  		return json_decode($kp_api, true);
    }
}

if (!function_exists('check_if')) {
    function check_if($check_value, $dataArray) {
        $tags_array = array();
        foreach($dataArray as $named => $zna4enie) {
            if (strpos($check_value, '[if_'.$named.']') !== false) {
                if ($zna4enie) $check_value = preg_replace(';\[if_'.$named.'\](.*?)\[\/if_'.$named.'\];is', '$1', $check_value);
                else $check_value = preg_replace(';\[if_'.$named.'\](.*?)\[\/if_'.$named.'\];is', '', $check_value);
            }
            if (strpos($check_value, '[ifnot_'.$named.']') !== false) {
                if ($zna4enie) $check_value = preg_replace(';\[ifnot_'.$named.'\](.*?)\[\/ifnot_'.$named.'\];is', '', $check_value);
                else $check_value = preg_replace(';\[ifnot_'.$named.'\](.*?)\[\/ifnot_'.$named.'\];is', '$1', $check_value);
            }
            $tags_array[] = '{'.$named.'}';
        }
        $check_value = str_ireplace( $tags_array, $dataArray, $check_value);
    	return $check_value;
    }
}

if (!function_exists('xfieldsdatasaved')) {
    function xfieldsdatasaved($xfields) {
        $filecontents = [];
        foreach ($xfields as $xfielddataname => $xfielddatavalue) {
            if ($xfielddatavalue === '') continue;
            $xfielddataname = str_replace( "|", "&#124;", $xfielddataname);
            $xfielddataname = str_replace( "\r\n", "__NEWL__", $xfielddataname);
            $xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue);
            $xfielddatavalue = str_replace( "\r\n", "__NEWL__", $xfielddatavalue);
            $filecontents[] = $xfielddataname."|".$xfielddatavalue;
        }
        $filecontents = join('||', $filecontents );
        return $filecontents;
    }
}

if (!function_exists('xfparamload')) {
    function xfparamload( $xfname ) {
        $path = ENGINE_DIR . '/data/xfields.txt';
        $filecontents = file( $path );
        
        foreach ( $filecontents as $name => $value ) {
            $filecontents[$name] = explode( "|", trim( $value ) );
            if($filecontents[$name][0] == $xfname ) return $filecontents[$name];
        }
        return false;
    }    
}

if (!function_exists('setPoster')) {
    function setPoster($poster_url, $poster_title, $image_kind, $poster_name = false, $news_id = 0) {
	
	    global $config, $kp_config, $db, $member_id, $user_group;
	
	    $area = 'xfieldsimage';
	
	    if ( $poster_name ) {
	    	$xfparam = xfparamload($poster_name);
	    }
	    else $xfparam = [];
	
	    $xfname = $xfparam[0];
	    $t_seite = (int)$config['t_seite'];
	    $m_seite = $t_seite;
	    $t_size = $xfparam[13];
	    $m_size = 0;
	    if (isset($xfparam[9])) $config['max_up_side'] = $xfparam[9];
	    elseif ( $image_kind == 'poster' ) $config['max_up_side'] = $kp_config['images']['poster_max_up_side'];
	    elseif ( $image_kind == 'kadr' ) $config['max_up_side'] = $kp_config['images']['screens_max_up_side'];
	    elseif ( $image_kind == 'logo' ) $config['max_up_side'] = $kp_config['images']['logo_max_up_side'];
	    elseif ( $image_kind == 'cover' ) $config['max_up_side'] = $kp_config['images']['cover_max_up_side'];
	    $config['max_up_size'] = 2048;
	    $config['min_up_side'] = 0;
	    $make_watermark = (bool)$xfparam[11];
	    $make_thumb = (bool)$xfparam[12];
	    $make_medium = false;

	    $t_size = explode("x", $t_size);
	    if (count($t_size) == 2) {
	    	$t_size = (int)$t_size[0] . "x" . (int)$t_size[1];
	    } else $t_size = (int)$t_size[0];

	    $m_size = explode("x", $m_size);
	    if (count($m_size) == 2) {
	    	$m_size = (int)$m_size[0] . "x" . (int)$m_size[1];
	    } else $m_size = (int)$m_size[0];

        $author = $db->safesql($member_id['name']);
            
        $poster_data = file_get_contents($poster_url);
            
        $poster_title = totranslit(stripslashes( $poster_title ), true, false) . '.jpg';
            
        $new_poster = ROOT_DIR . '/uploads/files/' . $poster_title;
            
        file_put_contents($new_poster, $poster_data);
            
        $exif = exif_read_data($new_poster);

            
        $_FILES['qqfile'] = [
            'type' => $exif['MimeType'],
            'name' => $exif['FileName'],
            'tmp_name' => $new_poster,
            'error' => 0,
            'size' => $exif['FileSize']
        ];

            
        $uploader = new FileUploader($area, $news_id, $author, $t_size, $t_seite, $make_thumb, $make_watermark, $m_size, $m_seite, $make_medium);
        $result = json_decode($uploader->FileUpload(), true);

        @unlink($new_poster);
        return $result;
    }
}

if (!function_exists('totranslit_it')) {
    function totranslit_it($var, $lower = true, $punkt = true) {
	
$langtranslit = array(
	'а' => 'a', 'б' => 'b', 'в' => 'v',
	'г' => 'g', 'д' => 'd', 'е' => 'e',
	'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
	'и' => 'i', 'й' => 'j', 'к' => 'k',
	'л' => 'l', 'м' => 'm', 'н' => 'n',
	'о' => 'o', 'п' => 'p', 'р' => 'r',
	'с' => 's', 'т' => 't', 'у' => 'u',
	'ф' => 'f', 'х' => 'h', 'ц' => 'c',
	'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
	'ь' => '', 'ы' => 'y', 'ъ' => '',
	'э' => 'je', 'ю' => 'ju', 'я' => 'ja',
	"ї" => "ji", "є" => "ye", "ґ" => "g",
	
	'А' => 'A', 'Б' => 'B', 'В' => 'V',
	'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
	'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
	'И' => 'I', 'Й' => 'J', 'К' => 'K',
	'Л' => 'L', 'М' => 'M', 'Н' => 'N',
	'О' => 'O', 'П' => 'P', 'Р' => 'R',
	'С' => 'S', 'Т' => 'T', 'У' => 'U',
	'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
	'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
	'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
	'Э' => 'Je', 'Ю' => 'Ju', 'Я' => 'Ja',
	"Ї" => "Ji", "Є" => "ye", "Ґ" => "G",
	"À"=>"A", "à"=>"a", "Á"=>"A", "á"=>"a", 
	"Â"=>"A", "â"=>"a", "Ä"=>"A", "ä"=>"a", 
	"Ã"=>"A", "ã"=>"a", "Å"=>"A", "å"=>"a", 
	"Æ"=>"AE", "æ"=>"ae", "Ç"=>"C", "ç"=>"c", 
	"Ð"=>"D", "È"=>"E", "è"=>"e", "É"=>"E", 
	"é"=>"e", "Ê"=>"E", "ê"=>"e", "Ì"=>"I", 
	"ì"=>"i", "Í"=>"I", "í"=>"i", "Î"=>"I", 
	"î"=>"i", "Ï"=>"I", "ï"=>"i", "Ñ"=>"N", 
	"ñ"=>"n", "Ò"=>"O", "ò"=>"o", "Ó"=>"O", 
	"ó"=>"o", "Ô"=>"O", "ô"=>"o", "Ö"=>"O", 
	"ö"=>"o", "Õ"=>"O", "õ"=>"o", "Ø"=>"O", 
	"ø"=>"o", "Œ"=>"OE", "œ"=>"oe", "Š"=>"S", 
	"š"=>"s", "Ù"=>"U", "ù"=>"u", "Û"=>"U", 
	"û"=>"u", "Ú"=>"U", "ú"=>"u", "Ü"=>"U", 
	"ü"=>"u", "Ý"=>"Y", "ý"=>"y", "Ÿ"=>"Y", 
	"ÿ"=>"y", "Ž"=>"Z", "ž"=>"z", "Þ"=>"B", 
	"þ"=>"b", "ß"=>"ss", "£"=>"pf", "¥"=>"ien", 
	"І"=>"I", "і"=>"i", "ð"=>"eth", "ѓ"=>"r"
);
	
	    if ( is_array($var) ) return "";

	    $var = str_replace(chr(0), '', $var);
	
	    $var = trim( strip_tags( $var ) );
	    $var = preg_replace( "/\s+/u", "-", $var );
	    $var = str_replace( "/", "-", $var );
	
	    if (is_array($langtranslit) AND count($langtranslit) ) {
	    	$var = strtr($var, $langtranslit);
	    }

	    if ( $punkt ) $var = preg_replace( "/[^a-z0-9\_\-.]+/mi", "", $var );
	    else $var = preg_replace( "/[^a-z0-9\_\-]+/mi", "", $var );

	    $var = preg_replace( '#[\-]+#i', '-', $var );
	    $var = preg_replace( '#[.]+#i', '.', $var );

	    if ( $lower ) $var = strtolower( $var );

	    $var = str_ireplace( ".php", "", $var );
	    $var = str_ireplace( ".php", ".ppp", $var );

	    if( strlen( $var ) > 200 ) {
		
	    	$var = substr( $var, 0, 200 );
		
	    	if( ($temp_max = strrpos( $var, '-' )) ) $var = substr( $var, 0, $temp_max );
	
	    }
	
	    return $var;
    }
}

function convert_date($date, $type) {
    if ( $type == 0 ) return $date;
    elseif ( $type == 1 ) {
        $date_mas = explode("-", $date);
        return $date_mas[2].".".$date_mas[1].".".$date_mas[0];
    }
    elseif ( $type == 2 ) {
        $date_mas = explode("-", $date);
        $month_mas = [
            "01" => " января ",
            "02" => " февраля ",
            "03" => " марта ",
            "04" => " апреля ",
            "05" => " мая ",
            "06" => " июня ",
            "07" => " июля ",
            "08" => " августа ",
            "09" => " сентября ",
            "10" => " октября ",
            "11" => " ноября ",
            "12" => " декабря ",
        ];
        return intval($date_mas[2]).$month_mas[$date_mas[1]].$date_mas[0];
    }
}

function in_arrayi($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}
