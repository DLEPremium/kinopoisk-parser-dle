<?php

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if ($parse_action == 'parse' && $kp_config['settings']['tabus'] ) {
    
    $tabus = api_request('https://api1579861980.apicollaps.cc/franchise/details?token='.$kp_config['settings']['tabus'].'&kinopoisk_id='.$kp_id, $kp_config['settings']['kinopoiskapiunofficial']);
    
    if ( !$array_data['poster'] && $tabus['poster'] ) $array_data['poster'] = $tabus['poster'];
    if ( $tabus['collection'] ) {
        $collection = implode(', ', $tabus['collection'] );
        $array_data['collections'] = str_replace('\"', '', $collection);
    }
    else $array_data['collections'] = '';

}