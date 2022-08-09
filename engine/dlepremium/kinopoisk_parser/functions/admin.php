<?php
 
if (!defined('DATALIFEENGINE') || !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

function showRow($title = "", $description = "", $field = "", $class = "") {
	echo "<tr>
       <td class=\"col-xs-10 col-sm-6 col-md-7 {$class}\"><h6><b>{$title}:</b></h6><span class=\"note large\">{$description}</span></td>
       <td class=\"col-xs-2 col-md-5 settingstd {$class}\">{$field}</td>
       </tr>";
}

function showInput($data)
{
    $input_elemet = '';
	$input_elemet .= $data[3] ? " placeholder=\"{$data[3]}\"" : '';
	$input_elemet .= $data[4] ? ' disabled' : '';
	if ($data[1] == 'range') {
		$class = ' custom-range';
		$input_elemet .= $data[5] ? " step=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " min=\"{$data[6]}\"" : '';
		$input_elemet .= $data[7] ? " max=\"{$data[7]}\"" : '';
	} elseif ($data[1] == 'number') {
		$class = ' w-9';
		$input_elemet .= isset($data[5]) ? " min=\"{$data[5]}\"" : '';
		$input_elemet .= $data[6] ? " max=\"{$data[6]}\"" : '';
	}
return <<<HTML
	<input type="{$data[1]}" autocomplete="off" style="float: right;" value="{$data[2]}" class="form-control{$class}" name="{$data[0]}"{$input_elemet}>
HTML;
}

function makeCheckBox($name, $selected)
{
		$selected = $selected ? "checked" : "";
		return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";
}

function showSelect($name, $value, $check = false)
{
	if(!$check) $multiple = "multiple";
	return "<select data-placeholder=\""."".$phrases_settings['category_chose']."\" name=\"{$name}\" id=\"category\" class=\"valueselect\" {$multiple} style=\"width:100%;max-width:350px;\">{$value}</select>";
}

function makeDropDown($options, $name, $selected) {
        $output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\">\r\n";
        foreach ( $options as $value => $description ) {
            $output .= "<option value=\"$value\"";
            if( $selected == $value ) {
                $output .= " selected ";
            }
            $output .= ">$description</option>\n";
        }
        $output .= "</select>";
        return $output;
    }

function showTrInline($name, $description, $type, $data)
{
echo <<<HTML
<tr>
	<td>
		<label style="float:left;" class="form-label"><b>{$name}</b></label>
HTML;
	switch ($type) {
		case 'input':
			echo showInput($data);
		break;
		case 'textarea':
			echo textareaForm($data);
		break;
		default:
			echo $data;
		break;
	}
echo <<<HTML
</tr>
HTML;
}
	
function textareaForm($data)
{
	$input_elemet = $data[2] ? " placeholder=\"{$data[2]}\"" : '';
	$input_elemet .= $data[3] ? ' disabled' : '';
return <<<HTML
	<textarea style="min-height:150px;max-height:150px;min-width:333px;max-width:100%;border: 1px solid #ddd;padding: 5px;" autocomplete="off" class="form-control" name="{$data[0]}"{$input_elemet}>{$data[1]}</textarea>
HTML;
}

function ShowSelected($data)
{
	foreach ($data[1] as $key => $val) {
		if ($data[2]) {
			$output .= "<option value=\"{$key}\"";
		} else {
			$output .= "<option value=\"{$val}\"";
		}
		if (is_array($data[3])) {
			foreach ($data[3] as $element) {
				if ($data[2] && $element == $key) {
					$output .= " selected ";
				} elseif (!$data[2] && $element == $val) {
					$output .= " selected ";
				}
			}
		} elseif ($data[2] && $data[3] == $key) {
			$output .= " selected ";
		} elseif (!$data[2] && $data[3] == $val) {
			$output .= " selected ";
		}
		$output .= ">{$val}</option>\n";
	}
	$input_elemet = $data[5] ? ' disabled' : '';
	$input_elemet .= $data[4] ? ' multiple' : '';
	$input_elemet .= $data[6] ? " data-placeholder=\"{$data[6]}\"" : '';
return <<<HTML
<select name="{$data[0]}" class="form-control custom-select" {$input_elemet}>
	{$output}
</select>
HTML;
}

function makeSelect($array, $name, $data, $placeholder, $mode)
{
    $ar_ray = explode(',', $data);
    foreach ($array as $key => $value) {
        if ( $mode == 1 ) $key = $value;
	    if (in_array($key, $ar_ray)) {
	    	$options[] = '<option value="'.$key.'" selected>'.$value.'</option>';
	    }
	    else {
	    	$options[] = '<option value="'.$key.'">'.$value.'</option>';
	    }
    }
    return '<select data-placeholder="'.$placeholder.'" name="'.$name.'[]" id="'.$name.'" class="valuesselect" multiple style="width:100%;max-width:350px;">'.implode('', $options).'</select>';
}

$data_list = ['russian', 'original', 'english', 'poster', 'cover', 'logo', 'reviews_count', 'rating_good_review', 'rating_good_review_vote_count', 'rating_kinopoisk', 'votes_kinopoisk', 'rating_imdb', 'votes_imdb', 'rating_film_critics', 'rating_film_critics_vote_count', 'rating_await', 'rating_await_count', 'rating_rf_critics', 'rating_rf_critics_vote_count', 'kinopoisk_url', 'year', 'duration', 'slogan', 'plot', 'short_plot', 'editor_annotation', 'production_status', 'type_en', 'type_ru', 'rating_mpaa', 'rating_age_limits', 'countries', 'genres', 'start_year', 'end_year', 'season', 'episode', 'facts', 'errors', 'world_premier', 'usa_premier', 'russia_premier', 'status', 'budget', 'marketing', 'fees_usa', 'fees_world', 'awards', 'youtube_trailer', 'directors', 'actors', 'producers', 'screenwriters', 'operators', 'composers', 'design', 'editors', "collections", "screenshot_1", "screenshot_2", "screenshot_3", "screenshot_4", "screenshot_5", "screenshot_6", "screenshot_7", "screenshot_8", "screenshot_9", "screenshot_10", "catalog_ru", "catalog_eng" ];

$xfield_list = xfieldsload();

$xfields_list = ['-' => '-'];
$xfield_image = ['-' => '-'];
$xfield_gallery = ['-' => '-'];
$xfield_yesorno = ['-' => '-'];
$xfield_select = ['-' => '-'];
$main_fields = ['title' => '', 
'short_story' => '',
'full_story' => '',
'alt_name' => '',
'meta_title' => '',
'meta_description' => '',
'meta_keywords' => '',
'tags' => '',
'catalog' => ''];

for ($i = 0; $i < count($xfield_list); $i++) {
	if ( $xfield_list[$i][3] == "text" OR $xfield_list[$i][3] == "textarea" OR $xfield_list[$i][3] == "htmljs" ) {
	    $main_fields[$xfield_list[$i][0]] = $xfield_list[$i][1];
	    $xfields_list[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "image" ) {
	    $xfield_image[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "imagegalery" ) {
	    $xfield_gallery[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "yesorno" ) {
	    $xfield_yesorno[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
	elseif ( $xfield_list[$i][3] == "select" ) {
	    $xfield_select[$xfield_list[$i][0]] = $xfield_list[$i][1];
	}
}

$screens_count = array("1" => "1 скриншот","2" => "2 скриншота","3" => "3 скриншота","4" => "4 скриншота","5" => "5 скриншотов","6" => "6 скриншотов","7" => "7 скриншотов","8" => "8 скриншотов","9" => "9 скриншотов","10" => "10 скриншотов");
$choose_date = [
    0 => '2022-12-31',
    1 => '31.12.2022',
    2 => '31 декабря 2022',
];

$year_array = array();
$years_array = array();
for ($i = 1910; $i <= 2025; $i++) {
    $year_array[$i] = $i.' год';
    $years_array[] = $i;
}
krsort($year_array);
krsort($years_array);

$country_array = ["Австралия", "Австрия", "Азербайджан", "Албания", "Алжир", "Американское Самоа", "Ангилья", "Англия", "Ангола", "Андорра", "Антигуа и Барбуда", "Аргентина", "Армения", "Аруба", "Афганистан", "Багамы", "Бангладеш", "Барбадос", "Бахрейн", "Бейкер", "Белиз", "Белоруссия", "Бельгия", "Бенилюкс", "Бенин", "Болгария", "Боливия", "Бонэйр", "Бопутатсвана", "Босния и Герцеговина", "Ботсвана", "Бразилия", "Бруней", "Буркина-Фасо", "Бурунди", "Бутан", "Вануату", "Ватикан", "Великобритания", "Венгрия", "Венда", "Венесуэла", "Вьетнам", "Габон", "Гаити", "Гайана", "Гамбия", "Гана", "Гватемала", "Гвинея", "Гвинея-Бисау", "Германия", "Гернси", "Гибралтар", "Гондурас", "Гонконг", "Сомали", "Гренада", "Греция", "Грузия", "Гуам", "Дания", "Конго", "Косово", "Джибути", "Джонстон", "Джубаленд", "Доминика", "Доминикана", "Египет", "Замбия", "Зимбабве", "Израиль", "Имамат Оман", "Индия", "Индонезия", "Иордания", "Ирак", "Иран", "Ирландия", "Исландия", "Испания", "Италия", "Йемен", "Султанат Касири", "Кабо-Верде", "Казахстан", "Камбоджа", "Камерун", "Канада", "Катар", "Кашубия", "Кенедугу", "Кения", "Киргизия", "Кирибати", "Китай", "Колумбия", "Коморы", "Конго", "Корея Северная", "Корея Южная", "Нидерланды", "Конго", "Коста-Рика", "Куба", "Кувейт", "Кюрасао", "Лаос", "Латвия", "Лесото", "Либерия", "Ливан", "Ливия", "Литва", "Лихтенштейн", "Люксембург", "Маврикий", "Мавритания", "Мадагаскар", "Малави", "Малайзия", "Мали", "Мальдивы", "Мальта", "Марокко", "Мартиазо", "Мексика", "Мидуэй", "Мозамбик", "Молдавия", "Молдова", "Монако", "Монголия", "Монтсеррат", "Мьянма", "Намибия", "Науру", "Непал", "Нигер", "Нигерия", "Нидерланды", "Никарагуа", "Ниуэ", "Новая Зеландия", "Новая Каледония", "Норвегия", "Остров Норфолк", "ОАЭ", "Оман", "Пакистан", "Палау", "Панама", "Парагвай", "Перу", "Польша", "Португалия", "Пуэрто Рико", "Ангилья", "Закистан", "Кипр", "Логон", "Россия", "Руанда", "Румыния", "Сальвадор", "Самоа", "Сан-Марино", "Саудовская Аравия", "Северная Ирландия", "Северная Македония", "Сейшельские Острова ", "Сенегал", "Сент-Люсия", "Сербия", "Силенд", "Сингапур", "Синт-Мартен", "Синт-Эстатиус", "Сирия", "Сискей", "Словакия", "Словения", "Соломоновы Острова", "Сомали", "Сомалиленд", "Судан", "Суринам", "СССР", "США", "Сьерра-Леоне", "Таджикистан", "Таиланд", "Тайвань", "Танзания", "Того", "Токелау", "Тонга", "Торо", "Транскей", "Тринидад", "Тобаго", "Тувалу", "Тунис", "Туркмения", "Турция", "Уганда", "Узбекистан", "Украина", "Уругвай", "Уэйк", "Уэльс", "ФШМ", "Фиджи", "Филиппины", "Финляндия", "Фландренсис", "Фолклендские острова", "Франция", "Французская Полинезия", "Хауленд", "Хиршабелле", "Хорватия", "Центральноафриканская Республика", "Чад", "Черногория", "Чехия", "Чили", "Швейцария", "Швеция", "Шотландия", "Шри-Ланка", "Эквадор", "Экваториальная Гвинея", "Эритрея", "Эсватини", "Эстония", "Эфиопия", "Южная Георгия", "ЮАР", "Южный Судан", "Ямайка", "Япония"];
$type_array = ["Фильм", "Сериал", "ТВ-Шоу", "анонсировано", "вышло"];
$genres_array = ["аниме", "биография", "боевик", "вестерн", "военный", "детектив", "детский", "для взрослых", "документальный", "драма", "игра", "история", "комедия", "концерт", "короткометражка", "криминал", "мелодрама", "музыка", "мультфильм", "мюзикл", "новости", "приключения", "реальное ТВ", "семейный", "спорт", "ток-шоу", "триллер", "ужасы", "фантастика", "фильм-нуар", "фэнтези", "церемония"];
$collections_array = ["2х2","Amazon","Apple TV+","BBC","DC","Discovery","Disney","DreamWorks","Fox","HBO","Hulu","KION","Marvel","National Geographic","Netflix","YouTube Premium","Антиутопии","Биографии","Для взрослых","Для женщин","Для молодёжи","Для мужчин","Дорамы","Канал Пятница","Канал Супер","Лаурет премии Оскар","Лучшие фильмы 20 века","Молодежные комедии","Мотивирующие","На реальных событиях","Про агентов","Про акул","Про апокалипсис","Про боевые искусства","Про бывших","Про вампиров","Про ведьм","Про войну 1941-1945","Про гонки","Про девушек","Про детей","Про динозавров","Про докторов","Про драконов","Про животных","Про жизнь","Про звезд","Про зомби","Про инопланетян","Про космос","Про любовь","Про маньяков","Про мафию, банды","Про монстров","Про оборотней","Про ограбления, аферы и мошенников","Про острова","Про подростков","Про полицию","Про призраков","Про путешествия","Про путешествия во времени","Про роботов","Про снайперов","Про спорт","Про средневековье","Про супергероев","Про танки","Про тюрьму","Про футбол","Про школу","Психологические","Рождественские","Романтические комедии","С наградами","С неожиданным концом","СТС","Самые кассовые","Ситкомы","Советские","ТВ3","ТНТ","ТНТ4","Фильмы на Хэллоуин","Фильмы-катастрофы","Экранизация книг"];

$category_values = array_unique(array_merge($type_array,$genres_array));
$category_values = array_unique(array_merge($category_values,$collections_array));
$category_values = array_unique(array_merge($category_values,$country_array));
$category_values = array_unique(array_merge($category_values,$years_array));

$fields_descr = [
'Русское название',
'Оригинальное название',
'Английское название',
'Ссылка на постер',
'Ссылка на обложку',
'Ссылка на логотип',
'Количество рецензий на КиноПоиск',
'Рейтинг хороших отзывов на КиноПоиск',
'Количество хороших отзывов на КиноПоиск',
'Рейтинг на КиноПоиск',
'Количество голосов на КиноПоиск',
'Рейтинг на IMDB',
'Количество голосов на IMDB',
'Рейтинг критиков на КиноПоиск',
'Количество проголосовавших критиков на КиноПоиск',
'Рейтинг ожиданий на КиноПоиск',
'Количество ожидающих на КиноПоиск',
'Рейтинг критиков РФ на КиноПоиск',
'Количество проголосовавших критиков РФ на КиноПоиск',
'Ссылка на КиноПоиск',
'Год выхода',
'Длительность',
'Слоган',
'Сюжет',
'Краткий сюжет',
'Заметка редактора',
'Статус производства',
'Тип на английском (movie, tvserial, tvshow)',
'Тип на русском (Фильм, Сериал, ТВ-Шоу)',
'Рейтинг MPAA',
'Возрастное ограничение',
'Страны',
'Жанры',
'Год начала показа (для сериалов)',
'Год окончания показа (для сериалов)',
'Последний сезон',
'Последняя серия',
'Факты',
'Ошибки',
'Премьера в мире',
'Премьера в США',
'Премьера в РФ',
'Статус (анонсировано или вышло)',
'Бюджет',
'Траты на рекламу',
'Сборы в США',
'Сборы в мире',
'Награды',
'Трейлер с YouTube',
'Режиссёры',
'Актёры',
'Продюсеры',
'Сценаристы',
'Операторы',
'Композиторы',
'Художники',
'Монтажеры',
'Список коллекций',
'Ссылка на первый скриншот',
'Ссылка на второй скриншот',
'Ссылка на третий скриншот',
'Ссылка на четвёртый скриншот',
'Ссылка на пятый скриншот',
'Ссылка на шестой скриншот',
'Ссылка на седьмой скриншот',
'Ссылка на восьмой скриншот',
'Ссылка на девятый скриншот',
'Ссылка на десятый скриншот',
'Первая буква русского названия',
'Первая буква оригинального названия'
];
