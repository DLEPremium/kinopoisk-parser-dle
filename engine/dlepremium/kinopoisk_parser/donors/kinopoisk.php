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

if ($parse_action == 'search') {
    
    if ($kinopoisk_id) $kp_api = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kinopoisk_id, $kp_config['settings']['kinopoiskapiunofficial']);
    elseif ($search_name) $kp_api = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films?order=YEAR&type=ALL&ratingFrom=0&ratingTo=10&yearFrom=1000&yearTo=3000&keyword='.$search_name.'&page=1', $kp_config['settings']['kinopoiskapiunofficial']);

    if ( $kp_api['items'] AND $search_name ) {
        foreach ( $kp_api['items'] as $result ) {
            $info = '';
            $countries = $genres = [];
            if ( $result['countries'] ) {
                foreach ( $result['countries'] as $country ) {
                    $countries[] = $country['country'];
                }
                $info = 'Страна - '.implode(', ', $countries).'. ';
            }
            if ( $result['genres'] ) {
                foreach ( $result['genres'] as $country ) {
                    $genres[] = $country['genre'];
                }
                $info .= 'Жанры - '.implode(', ', $genres);
            }

            if ( $result['kinopoiskId'] ) $kp_link = 'https://www.kinopoisk.ru/film/'.$result['kinopoiskId'].'/';
            else $kp_link = "";
            if ( $result['imdbId'] ) $imdb_link = 'https://www.imdb.com/title/'.$result['imdbId'].'/';
            else $imdb_link = "";
            
            $where = "xfields LIKE '%".$kp_config['fields']['xf_kinopoisk_id']."|".$result['kinopoiskId']."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
	    	if ($proverka) {
	    	    $find_id = "est";
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else $find_id = "net";
            
            $responseArray[] = array(
				'kp_id' => $result['kinopoiskId'],
				'title' => $result['nameRu'],
				'orig_title' => $result['nameOriginal'],
				'poster' => $result['posterUrl'],
				'year' => $result['year'],
				'kind' => $cat_type[$result['type']],
				'info' => $info,
				'plot' => '',
				'kp_link' => $kp_link,
				'imdb_link' => $imdb_link,
				'find_id' => $find_id,
				'edit_link' => $edit_link
			);
        }
    }
    elseif ( $kp_api['kinopoiskId'] ) {
			$info = '';
            $countries = $genres = [];
            if ( $kp_api['countries'] ) {
                foreach ( $kp_api['countries'] as $country ) {
                    $countries[] = $country['country'];
                }
                $info = 'Страна - '.implode(', ', $countries).'. ';
            }
            if ( $kp_api['genres'] ) {
                foreach ( $kp_api['genres'] as $country ) {
                    $genres[] = $country['genre'];
                }
                $info .= 'Жанры - '.implode(', ', $genres);
            }
            
            if ( $kp_api['kp_id'] ) $kp_link = 'https://www.kinopoisk.ru/film/'.$kp_api['kp_id'].'/';
            else $kp_link = "";
            if ( $kp_api['imdb_id'] ) $imdb_link = 'https://www.imdb.com/title/'.$kp_api['imdb_id'].'/';
            else $imdb_link = "";
            
            $where = "xfields LIKE '%".$kp_config['fields']['xf_kinopoisk_id']."|".$kp_api['kinopoiskId']."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
	    	if ($proverka) {
	    	    $find_id = "est";
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else $find_id = "net";
            
            $responseArray[] = array(
				'kp_id' => $kp_api['kinopoiskId'],
				'title' => $kp_api['nameRu'],
				'orig_title' => $kp_api['nameOriginal'],
				'poster' => $kp_api['posterUrl'],
				'year' => $kp_api['year'],
				'kind' => $cat_type[$kp_api['type']],
				'info' => $info,
				'plot' => $kp_api['description'],
				'kp_link' => $kp_link,
				'imdb_link' => $imdb_link,
				'find_id' => $find_id,
				'edit_link' => $edit_link
			);
    }
    
}
elseif ($parse_action == 'parse') {
    
    $array_data = array();
    
    $kp_api = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id, $kp_config['settings']['kinopoiskapiunofficial']);
    
    foreach ( $kp_api as $api_name => $api_value ) {
        if ( $api_value === NULL || $api_value === false ) $kp_api[$api_name] = '';
    }
    
    //print_r($kp_api);
	
	$kp_api_fields = ['russian', 'original', 'english', 'poster', 'cover', 'logo', 'reviews_count', 'rating_good_review', 'rating_good_review_vote_count', 'rating_kinopoisk', 'votes_kinopoisk', 'rating_imdb', 'votes_imdb', 'rating_film_critics', 'rating_film_critics_vote_count', 'rating_await', 'rating_await_count', 'rating_rf_critics', 'rating_rf_critics_vote_count', 'kinopoisk_url', 'year', 'duration', 'slogan', 'plot', 'short_plot', 'editor_annotation', 'production_status', 'type_en', 'type_ru', 'rating_mpaa', 'rating_age_limits', 'countries', 'genres', 'start_year', 'end_year', 'season', 'episode', 'facts', 'errors', 'world_premier', 'usa_premier', 'russia_premier', 'budget', 'marketing', 'fees_usa', 'fees_world', 'awards', 'youtube_trailer', 'directors', 'actors', 'producers', 'screenwriters', 'operators', 'composers', 'design', 'editors'];
	
	if ( $kp_api['nameRu'] ) $array_data['russian'] = $kp_api['nameRu'];
	if ( $kp_api['nameOriginal'] ) $array_data['original'] = $kp_api['nameOriginal'];
	if ( $kp_api['nameEn'] ) $array_data['english'] = $kp_api['nameEn'];
	if ( $kp_api['posterUrl'] ) $array_data['poster'] = $kp_api['posterUrl'];
	if ( $kp_api['coverUrl'] ) $array_data['cover'] = $kp_api['coverUrl'];
	if ( $kp_api['logoUrl'] ) $array_data['logo'] = $kp_api['logoUrl'];
	if ( $kp_api['reviewsCount'] ) $array_data['reviews_count'] = $kp_api['reviewsCount'];
	if ( $kp_api['ratingGoodReview'] ) $array_data['rating_good_review'] = $kp_api['ratingGoodReview'];
	if ( $kp_api['ratingGoodReviewVoteCount'] ) $array_data['rating_good_review_vote_count'] = $kp_api['ratingGoodReviewVoteCount'];
	if ( $kp_api['ratingKinopoisk'] ) $array_data['rating_kinopoisk'] = $kp_api['ratingKinopoisk'];
	if ( $kp_api['ratingKinopoiskVoteCount'] ) $array_data['votes_kinopoisk'] = $kp_api['ratingKinopoiskVoteCount'];
	if ( $kp_api['ratingImdb'] ) $array_data['rating_imdb'] = $kp_api['ratingImdb'];
	if ( $kp_api['ratingImdbVoteCount'] ) $array_data['votes_imdb'] = $kp_api['ratingImdbVoteCount'];
	if ( $kp_api['ratingFilmCritics'] ) $array_data['rating_film_critics'] = $kp_api['ratingFilmCritics'];
	if ( $kp_api['ratingFilmCriticsVoteCount'] ) $array_data['rating_film_critics_vote_count'] = $kp_api['ratingFilmCriticsVoteCount'];
	if ( $kp_api['ratingAwait'] ) $array_data['rating_await'] = $kp_api['ratingAwait'];
	if ( $kp_api['ratingAwaitCount'] ) $array_data['rating_await_count'] = $kp_api['ratingAwaitCount'];
	if ( $kp_api['ratingRfCritics'] ) $array_data['rating_rf_critics'] = $kp_api['ratingRfCritics'];
	if ( $kp_api['ratingRfCriticsVoteCount'] ) $array_data['rating_rf_critics_vote_count'] = $kp_api['ratingRfCriticsVoteCount'];
	if ( $kp_api['webUrl'] ) $array_data['kinopoisk_url'] = $kp_api['webUrl'];
	if ( $kp_api['year'] ) $array_data['year'] = $kp_api['year'];
	if ( $kp_api['filmLength'] ) $array_data['duration'] = $kp_api['filmLength'];
	if ( $kp_api['slogan'] ) $array_data['slogan'] = $kp_api['slogan'];
	if ( $kp_api['description'] ) $array_data['plot'] = $kp_api['description'];
	if ( $kp_api['shortDescription'] ) $array_data['short_plot'] = $kp_api['shortDescription'];
	if ( $kp_api['editorAnnotation'] ) $array_data['editor_annotation'] = $kp_api['editorAnnotation'];
	if ( $kp_api['ratingMpaa'] ) $array_data['rating_mpaa'] = $kp_api['ratingMpaa'];
	if ( $kp_api['ratingAgeLimits'] ) $array_data['rating_age_limits'] = str_replace('age', '', $kp_api['ratingAgeLimits']);
	if ( $kp_api['countries'] ) {
	    $countries = [];
	    foreach ( $kp_api['countries'] as $country ) {
	        $countries[] = $country['country'];
	    }
	    $array_data['countries'] = implode(', ', $countries);
	}
	else $array_data['countries'] = "";
	if ( $kp_api['genres'] ) {
	    $genres = [];
	    foreach ( $kp_api['genres'] as $genre ) {
	        $genres[] = $genre['genre'];
	    }
	    $array_data['genres'] = implode(', ', $genres);
	}
	else $array_data['genres'] = "";
	if ( $kp_api['type'] ) {
        $array_data['type_en'] = $cat_type_en[$kp_api['type']];
	    $array_data['type_ru'] = $cat_type[$kp_api['type']];
	}
	if ( $kp_api['startYear'] ) $array_data['start_year'] = $kp_api['startYear'];
	if ( $kp_api['endYear'] ) $array_data['end_year'] = $kp_api['endYear'];
	
	if ( $kp_api['serial'] === true && $kp_config['settings']['seasons'] == 1  ) {
	    $kp_api_seasons = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/seasons', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_seasons['items'] ) {
	        $last_season = $kp_api_seasons['total'];
	        $last_episode = 0;
	        foreach ( $kp_api_seasons['items'] as $season ) {
	            if ( $season['number'] == $last_season ) {
	                foreach ( $season['episodes'] as $episode ) {
	                    if ( $episode['episodeNumber'] > $last_episode ) $last_episode = $episode['episodeNumber'];
	                }
	            }
	        }
	        $array_data['season'] = $last_season;
	        $array_data['episode'] = $last_episode;
	    }
	}
	
	if ( $kp_config['settings']['facts'] == 1 ) {
	    $kp_api_facts = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/facts', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_facts['items'] ) {
	        $errors_arr = $facts_arr = [];
	        foreach ( $kp_api_facts['items'] as $facts_errors ) {
	            if ( $facts_errors['type'] == 'FACT' ) $facts_arr[] = $kp_config['settings']['fact_prefix'].strip_tags($facts_errors['text']).$kp_config['settings']['fact_sufix'];
	            elseif ( $facts_errors['type'] == 'BLOOPER' ) $errors_arr[] = $kp_config['settings']['errors_prefix'].strip_tags($facts_errors['text']).$kp_config['settings']['errors_sufix'];
	        }
	        if ( $kp_config['settings']['max_facts'] ) {
	            $facts_arr = array_slice($facts_arr, 0, $kp_config['settings']['max_facts']);
	            $array_data['facts'] = implode('', $facts_arr);
	        }
	        
	        if ( $kp_config['settings']['max_errors'] ) {
	            $errors_arr = array_slice($errors_arr, 0, $kp_config['settings']['max_errors']);
	            $array_data['errors'] = implode('', $errors_arr);
	        }
	    }
	}
	
	if ( $kp_config['settings']['distributions'] == 1 ) {
	    $kp_api_distributions = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/distributions', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_distributions['items'] ) {
	        foreach ( $kp_api_distributions['items'] as $distributions ) {
	            if ( $distributions['type'] == 'WORLD_PREMIER' ) {
	                $world_premier = $distributions['date'];
	                $array_data['world_premier'] = convert_date($distributions['date'], $kp_config['settings']['date_format']);
	            }
	            elseif ( $distributions['type'] == 'PREMIERE' && $distributions['country'][0]['country'] == 'США' ) {
	                $array_data['usa_premier'] = convert_date($distributions['date'], $kp_config['settings']['date_format']);
	            }
	            elseif ( $distributions['type'] == 'LOCAL' && $distributions['country'][0]['country'] == 'Россия' ) {
	                $russia_premier = $distributions['date'];
	                $array_data['russia_premier'] = convert_date($distributions['date'], $kp_config['settings']['date_format']);
	            }
	        }
	    }
	}
	
	if ( $kp_config['settings']['box_office'] == 1 ) {
	    $kp_api_box_office = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/box_office', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_box_office['items'] ) {
	        foreach ( $kp_api_box_office['items'] as $box_office ) {
	            if ( $box_office['type'] == 'BUDGET' ) $array_data['budget'] = $box_office['amount'];
	            elseif ( $box_office['type'] == 'MARKETING' ) $array_data['marketing'] = $box_office['amount'];
	            elseif ( $box_office['type'] == 'USA' ) $array_data['fees_usa'] = $box_office['amount'];
	            elseif ( $box_office['type'] == 'WORLD' ) $array_data['fees_world'] = $box_office['amount'];
	        }
	    }
	}
	
	if ( $kp_config['settings']['awards'] == 1 ) {
	    $kp_api_awards = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/awards', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_awards['items'] ) {
	        $awards_arr = [];
	        foreach ( $kp_api_awards['items'] as $award ) {
	            $awards_arr[] = $award['name'];
	        }
	        $awards_arr = array_unique($awards_arr);
	        $array_data['awards'] = implode(', ', $awards_arr);
	    }
	}
	
	if ( $kp_config['settings']['videos'] == 1 ) {
	    $kp_api_videos = api_request('https://kinopoiskapiunofficial.tech/api/v2.2/films/'.$kp_id.'/videos', $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_videos['items'] ) {
	        foreach ( $kp_api_videos['items'] as $videos ) {
	            if ( $videos['site'] == 'YOUTUBE' && stripos($videos['name'], 'Трейлер') !== false ) {
	                if( preg_match( "#youtu.be/(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	                elseif( preg_match( "#youtube.com/v/(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	                elseif( preg_match( "#youtube.com/watch?v=(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	            }
	        }
	    }
	}
	
	if ( $kp_config['settings']['staff'] == 1 ) {
	    $kp_api_staff = api_request('https://kinopoiskapiunofficial.tech/api/v1/staff?filmId='.$kp_id, $kp_config['settings']['kinopoiskapiunofficial']);
	    if ( $kp_api_staff ) {
	        $directors = $actors = $producers = $screenwriters = $operators = $composers = $design = $editors = [];
	        foreach ( $kp_api_staff as $staff ) {
	            if ( $staff['professionText'] == 'Режиссеры' ) $directors[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Актеры' ) $actors[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Продюсеры' ) $producers[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Сценаристы' ) $screenwriters[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Операторы' ) $operators[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Композиторы' ) $composers[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Художники' ) $design[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	            elseif ( $staff['professionText'] == 'Монтажеры' ) $editors[] = $staff['nameRu'] ? $staff['nameRu'] : $staff['nameEn'];
	        }
	    }
	    if ( $kp_config['settings']['max_directors'] && $directors ) {
	        $directors = array_slice($directors, 0, $kp_config['settings']['max_directors']);
	        $array_data['directors'] = implode(', ', $directors);
	    }
	    if ( $kp_config['settings']['max_actors'] && $actors ) {
	        $actors = array_slice($actors, 0, $kp_config['settings']['max_actors']);
	        $array_data['actors'] = implode(', ', $actors);
	    }
	    if ( $kp_config['settings']['max_producers'] && $producers ) {
	        $producers = array_slice($producers, 0, $kp_config['settings']['max_producers']);
	        $array_data['producers'] = implode(', ', $producers);
	    }
	    if ( $kp_config['settings']['max_screenwriters'] && $screenwriters ) {
	        $screenwriters = array_slice($screenwriters, 0, $kp_config['settings']['max_screenwriters']);
	        $array_data['screenwriters'] = implode(', ', $screenwriters);
	    }
	    if ( $kp_config['settings']['max_operators'] && $operators ) {
	        $operators = array_slice($operators, 0, $kp_config['settings']['max_operators']);
	        $array_data['operators'] = implode(', ', $operators);
	    }
	    if ( $kp_config['settings']['max_composers'] && $composers ) {
	        $composers = array_slice($composers, 0, $kp_config['settings']['max_composers']);
	        $array_data['composers'] = implode(', ', $composers);
	    }
	    if ( $kp_config['settings']['max_design'] && $design ) {
	        $design = array_slice($design, 0, $kp_config['settings']['max_design']);
	        $array_data['design'] = implode(', ', $design);
	    }
	    if ( $kp_config['settings']['max_editors'] && $editors ) {
	        $editors = array_slice($editors, 0, $kp_config['settings']['max_editors']);
	        $array_data['editors'] = implode(', ', $editors);
	    }
	}
	
	foreach ( $kp_api_fields as $kp_field ) {
		if ( !$array_data[$kp_field] ) $array_data[$kp_field] = '';
	}
	
	$kp_data = file_get_contents('https://rating.kinopoisk.ru/'.$kp_id.'.xml');
    preg_match_all("|<kp_rating num_vote=\"(.*)\">(.*)</kp_rating><imdb_rating num_vote=\"(.*)\">(.*)</imdb_rating>|U", $kp_data, $ratng);
    if ( $ratng[1][0] ) $array_data['votes_kinopoisk'] = $ratng[1][0];
    if ( $ratng[2][0] ) $array_data['rating_kinopoisk'] = $ratng[2][0];
    if ( $ratng[3][0] ) $array_data['votes_imdb'] = $ratng[3][0];
    if ( $ratng[4][0] ) $array_data['rating_imdb'] = $ratng[4][0];
    
    if ( isset($russia_premier) ) $world_premier = $russia_premier;
    if ( isset($world_premier) ) {
		$time_today = date( "Y-m-d", time() );
        if ( strtotime($world_premier) > strtotime($time_today) ) $array_data['status'] = 'анонсировано';
        else $array_data['status'] = 'вышло';
    }
    else $array_data['status'] = '';
    
}
