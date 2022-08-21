<?php

/*
=====================================================
 Copyright (c) 2022 DLEPremium
=====================================================
 This code is protected by copyright
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if ($parse_action == 'parse' && $kp_config['settings']['tabus'] ) {
    
    $tabus = api_request('https://api1579861980.apicollaps.cc/franchise/details?token='.$kp_config['settings']['tabus'].'&kinopoisk_id='.$kp_id, $kp_config['settings']['kinopoiskapiunofficial']);
    
    if ( !$array_data['poster'] && $tabus['poster'] ) $array_data['poster'] = $tabus['poster'];
    if ( $tabus['collection'] ) {
        foreach ( $tabus['collection'] as $tnum => $tcollect ) {
            if ( $tabus['collection'][$tnum] == 'Про мафию, банды' ) $tabus['collection'][$tnum] = 'Про мафию и банды';
            elseif ( $tabus['collection'][$tnum] == 'Про ограбления, аферы и мошенников' ) $tabus['collection'][$tnum] = 'Про ограбления и мошенников';
            else continue;
        }
        $collection = implode(', ', $tabus['collection'] );
        $array_data['collections'] = str_replace('\"', '', $collection);
    }
    else $array_data['collections'] = '';

}
