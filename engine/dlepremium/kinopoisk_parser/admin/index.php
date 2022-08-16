<?php

/*
=====================================================
 Copyright (c) 2022 DLEPremium
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if (!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) {
	die('Hacking attempt!');
}

require_once ENGINE_DIR.'/dlepremium/kinopoisk_parser/data/config.php';
require_once ENGINE_DIR.'/dlepremium/kinopoisk_parser/functions/admin.php';

echoheader('<b>DLE Парсер КиноПоиск</b>', 'Настройки модуля DLE Парсер КиноПоиск');

echo <<<HTML
<style>

HTML;
require_once ENGINE_DIR .'/dlepremium/kinopoisk_parser/admin/styles.css';
echo <<<HTML

</style>
<script>

HTML;
require_once ENGINE_DIR .'/dlepremium/kinopoisk_parser/admin/scripts.js';
echo <<<HTML

</script>
<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#option_menu"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="option_menu">
		<ul class="nav navbar-nav">
			<li class="active"><a onclick="ChangeOption(this, 'settings');" class="tip" title="Основные настройки модуля"><i class="fa fa-cog"></i> Основные настройки</a></li>
			<li><a onclick="ChangeOption(this, 'xfields');" class="tip" title="Настройка проставления основных и доп полей"><i class="fa fa-file-text-o"></i> Основные и доп поля</a></li>
			<li><a onclick="ChangeOption(this, 'categories');" class="tip" title="Настройка проставления категорий"><i class="fa fa-tasks"></i> Категории</a></li>
			<li><a onclick="ChangeOption(this, 'images');" class="tip" title="Настройка изображений"><i class="fa fa-image"></i> Изображения</a></li>
		</ul>
	</div>
</div>

<form action="" method="post" class="systemsettings">
	<div id="settings" class="panel panel-flat">
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Общие настройки модуля</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;

showRow('Введите ваш API токен от kinopoiskapiunofficial.tech', 'Зарегистрируйтесь <a href="https://kinopoiskapiunofficial.tech/profile" target="_blank" rel="noreferrer">на сайте</a>, это бесплатно. После регистрации в разделе Profile вы найдете API токен от сервиса', showInput(['settings[kinopoiskapiunofficial]', 'text', $kp_config['settings']['kinopoiskapiunofficial']]));
showRow('Парсить количество сезонов и серий с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[seasons]', $kp_config['settings']['seasons']));
showRow('Парсить факты и ошибки с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[facts]', $kp_config['settings']['facts']));
showRow('Количество фактов', 'Максимальное количество фактов', showInput(['settings[max_facts]', 'number', $kp_config['settings']['max_facts'], '', '', 0, 100]));
showRow('Введите префикс для фактов', 'Введите желаемый префикс для фактов, он будет вставляться в начале каждого факта. Например &lt;li&gt;', showInput(['settings[fact_prefix]', 'text', $kp_config['settings']['fact_prefix']]));
showRow('Введите суфикс для фактов', 'Введите желаемый префикс для фактов, он будет вставляться в конце каждого факта. Например &lt;/li&gt&lt;br /&gt;', showInput(['settings[fact_sufix]', 'text', $kp_config['settings']['fact_sufix']]));
showRow('Количество ошибок', 'Максимальное количество ошибок', showInput(['settings[max_errors]', 'number', $kp_config['settings']['max_errors'], '', '', 0, 100]));
showRow('Введите префикс для ошибок', 'Введите желаемый префикс для ошибок, он будет вставляться в начале каждой ошибки. Например &lt;li&gt;', showInput(['settings[errors_prefix]', 'text', $kp_config['settings']['errors_prefix']]));
showRow('Введите суфикс для ошибок', 'Введите желаемый префикс для ошибок, он будет вставляться в конце каждой ошибки. Например &lt;/li&gt&lt;br /&gt;', showInput(['settings[errors_sufix]', 'text', $kp_config['settings']['errors_sufix']]));
showRow('Парсить дату выхода в разных странах с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[distributions]', $kp_config['settings']['distributions']));
showRow('Формат вывода дат', 'Выберите из списка желаемый формат вывода дат', makeDropDown( $choose_date, "settings[date_format]", $kp_config['settings']['date_format']));
showRow('Парсить данные о бюджете и сборах с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[box_office]', $kp_config['settings']['box_office']));
showRow('Парсить данные о наградах и премиях фильма с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[awards]', $kp_config['settings']['awards']));
showRow('Парсить трейлеры с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[videos]', $kp_config['settings']['videos']));
showRow('Парсить данные об актерах, режиссерах и т.д с КиноПоиск?', 'Отключение уменьшит на один запрос к апи и ускорит работу', makeCheckBox('settings[staff]', $kp_config['settings']['staff']));
showRow('Количество режиссёров', 'Максимальное количество режиссёров', showInput(['settings[max_directors]', 'number', $kp_config['settings']['max_directors'], '', '', 0, 100]));
showRow('Количество актёров', 'Максимальное количество актёров', showInput(['settings[max_actors]', 'number', $kp_config['settings']['max_actors'], '', '', 0, 100]));
showRow('Количество продюсеров', 'Максимальное количество продюсеров', showInput(['settings[max_producers]', 'number', $kp_config['settings']['max_producers'], '', '', 0, 100]));
showRow('Количество сценаристов', 'Максимальное количество сценаристов', showInput(['settings[max_screenwriters]', 'number', $kp_config['settings']['max_screenwriters'], '', '', 0, 100]));
showRow('Количество операторов', 'Максимальное количество операторов', showInput(['settings[max_operators]', 'number', $kp_config['settings']['max_operators'], '', '', 0, 100]));
showRow('Количество композиторов', 'Максимальное количество композиторов', showInput(['settings[max_composers]', 'number', $kp_config['settings']['max_composers'], '', '', 0, 100]));
showRow('Количество художников', 'Максимальное количество художников', showInput(['settings[max_design]', 'number', $kp_config['settings']['max_design'], '', '', 0, 100]));
showRow('Количество монтажеров', 'Максимальное количество монтажеров', showInput(['settings[max_editors]', 'number', $kp_config['settings']['max_editors'], '', '', 0, 100]));
showRow('Введите ваш API токен от Tabus/Collaps', 'Если у вас есть апи токен от балансера Tabus, введите его в данное поле. Модуль будет дополнительно парсить коллекции. Оставьте поле пустым, если они вам не нужны или у вас нет апи токена', showInput(['settings[tabus]', 'text', $kp_config['settings']['tabus']]));

echo <<<HTML
			</table>
		</div>
	</div>
	<div id="xfields" class="panel panel-flat" style='display:none'>
	    <div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Дополнительное поле с ID Kinopoisk', 'Выберите дополнительное поле, в котором содержится id с КиноПоиска', makeDropDown( $xfields_list, "fields[xf_kinopoisk_id]", $kp_config['fields']['xf_kinopoisk_id']));
echo <<<HTML
            </table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка шаблона заполнения данных</div>
			<br><br>
			<div class="rcol-2col">
			<div class="rcol-2col-header">
			    <span>Доступные теги</span>
			    <div class="show-hide">Show</div>
			</div>
			    <div class="rcol-2col-body" style="display: none;">
			        <table width="100%" border="0" cellspacing="0" cellpadding="0">
			            <td width="100%">Для каждого тега доступны теги-условия: [if_x]...[/if_x] - выведет содержимое, в случае если тег x имеет информацию. Обратный тег: [ifnot_x]...[/ifnot_x] - выведет содержимое, в случае если тег x пустой. Таким образом вы можете комбинировать шаблон заполнения данных. Пример: [if_russian]{russian}[/if_russian][ifnot_russian]{original}[/ifnot_russian]</td>
			        </table>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					    <tr class="rcol-2col-body-tr-even">
                            <td width="50%">Теги для поля</td>
                            <td width="50%">Описание тега для поля</td>
                        </tr>
HTML;
			$field_list = "";
			foreach ( $data_list as $fnum => $field ) {
				$field_list .= "<tr class=\"rcol-2col-body-tr-even\">
									<td width=\"50%\"><b>{".$field."}</b></td>
									<td width=\"50%\">".$fields_descr[$fnum]."</td>
								</tr>";
			}
echo <<<HTML
						{$field_list}
					</table>
				</div>
			</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
foreach ($main_fields as $key => $value) {
	if ($key == 'title') {
		showTrInline('Заголовок', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'short_story') {
		showTrInline('Краткое описание', '', 'textarea', ['xfields['.$key.']', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'full_story') {
		showTrInline('Полное описание', '', 'textarea', ['xfields['.$key.']', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'alt_name') {
		showTrInline('ЧПУ URL статьи', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'tags') {
		showTrInline('Ключевые слова для облака тегов', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_title') {
		showTrInline('Метатег Title', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_description') {
		showTrInline('Метатег Description', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'meta_keywords') {
		showTrInline('Метатег Keywords', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	elseif ($key == 'catalog') {
		showTrInline('Буквенный каталог', '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
	else {
		showTrInline('Доп поле '.$value, '', 'input', ['xfields['.$key.']', 'text', $kp_config['xfields'][$key]]);
	}
}
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="categories" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="font-size:20px; font-weight:bold;">Настройка проставления ваших категорий</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
foreach ($cat_info as $cat) {
    $cat_id = $cat["parentid"];
    $name = $cat["name"];
    while ($cat_id) {
        $name = $cat_info[$cat_id]["name"] . " / " . $name;
        $cat_id = $cat_info[$cat_id]["parentid"];
    }
    showRow($name.' (id '.$cat["id"].')', '', makeSelect( $category_values, "categories[{$cat["id"]}]", $kp_config['categories'][$cat["id"]], 'Выберите тег - тип, жанр, страну, озвучку', 1));
}
echo <<<HTML
			</table>
		</div>
	</div>
	<div id="images" class="panel panel-flat" style='display:none'>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка постера</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать постер к вам на сайт при создании новостей?', 'Если включено, то при создании новостей будет загружаться постер к вам на сервер. Если выключено, то ссылка на постер будет браться с КиноПоиск', makeCheckBox('images[poster]', $kp_config['images']['poster']));
showRow('Загружать постер к вам на сайт при редактировании новостей?', 'Если включено, то при редактировании новостей будет принудительно загружаться постер к вам на сервер. Не рекомендую включать, ведь он может заменить уже загруженный раннее постер', makeCheckBox('images[poster_edit]', $kp_config['images']['poster_edit']));
showRow('Дополнительное поле "загружаемое изображение"', 'Выберите для постера дополнительное поле типа "загружаемое изображение", если у вас такое поле есть. Настройки сжатия будут взяты с него. Если у вас доп поле под постер текстовое, то на вкладке настроек доп полей используйте тег [if_poster]{poster}[/if_poster]', makeDropDown( $xfield_image, "images[xf_poster]", $kp_config['images']['xf_poster']));
showRow('Максимально допустимые размеры постера для сжатия (если у вас нет доп поля "загружаемое изображение")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[poster_max_up_side]', 'text', $kp_config['images']['poster_max_up_side']]));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка логотипа</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать лого к вам на сайт при создании новостей?', 'Если включено, то при создании новостей будет загружаться лого к вам на сервер. Если выключено, то ссылка на лого будет браться с КиноПоиск', makeCheckBox('images[logo]', $kp_config['images']['logo']));
showRow('Загружать лого к вам на сайт при редактировании новостей?', 'Если включено, то при редактировании новостей будет принудительно загружаться лого к вам на сервер. Не рекомендую включать, ведь он может заменить уже загруженный раннее лого', makeCheckBox('images[logo_edit]', $kp_config['images']['logo_edit']));
showRow('Дополнительное поле "загружаемое изображение"', 'Выберите для лого дополнительное поле типа "загружаемое изображение", если у вас такое поле есть. Настройки сжатия будут взяты с него. Если у вас доп поле под постер текстовое, то на вкладке настроек доп полей используйте тег [if_logo]{logo}[/if_logo]', makeDropDown( $xfield_image, "images[xf_logo]", $kp_config['images']['xf_logo']));
showRow('Максимально допустимые размеры лого для сжатия (если у вас нет доп поля "загружаемое изображение")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[logo_max_up_side]', 'text', $kp_config['images']['logo_max_up_side']]));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка бекграунда</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать бекграунд к вам на сайт при создании новостей?', 'Если включено, то при создании новостей будет загружаться бекграунд к вам на сервер. Если выключено, то ссылка на бекграунд будет браться с КиноПоиск', makeCheckBox('images[cover]', $kp_config['images']['cover']));
showRow('Загружать бекграунд к вам на сайт при редактировании новостей?', 'Если включено, то при редактировании новостей будет принудительно загружаться бекграунд к вам на сервер. Не рекомендую включать, ведь он может заменить уже загруженный раннее бекграунд', makeCheckBox('images[cover_edit]', $kp_config['images']['cover_edit']));
showRow('Дополнительное поле "загружаемое изображение"', 'Выберите для бекграунда дополнительное поле типа "загружаемое изображение", если у вас такое поле есть. Настройки сжатия будут взяты с него. Если у вас доп поле под бекграунд текстовое, то на вкладке настроек доп полей используйте тег [if_cover]{cover}[/if_cover]', makeDropDown( $xfield_image, "images[xf_cover]", $kp_config['images']['xf_cover']));
showRow('Максимально допустимые размеры бекграунда для сжатия (если у вас нет доп поля "загружаемое изображение")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[cover_max_up_side]', 'text', $kp_config['images']['cover_max_up_side']]));
echo <<<HTML
			</table>
		</div>
		<div class="panel-body" style="padding: 20px;font-size:20px; font-weight:bold;">Настройка скриншотов</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
showRow('Загружать скриншоты/кадры к вам на сайт при создании новостей?', 'Если включено, то модуль будет парсить и заливать к вам кадры в момент создания новости', makeCheckBox('images[screens]', $kp_config['images']['screens']));
showRow('Загружать скриншоты к вам на сайт при редактировании новостей?', 'Если включено, то модуль будет парсить и заливать к вам кадры в момент редактирования новости. Внимание, если у вас уже были загружены ранее кадры то модуль их принудительно заменит', makeCheckBox('images[screens_edit]', $kp_config['images']['screens_edit']));
showRow('Количество скриншотов', 'Выберите желаемое количество скриншотов для загрузки, от 1 до 10. Рекомендуемое количество - 5', makeDropDown( $screens_count, "images[screens_count]", $kp_config['images']['screens_count']));
showRow('Дополнительное поле "загружаемая галерея изображений"', 'Выберите для скриншотов дополнительное поле типа "загружаемая галерея изображений", если у вас такое поле есть. Настройки сжатия будут взяты с него. Если у вас доп поля под кадры текстовые, то на вкладке настроек доп полей используйте теги [if_screenshot_x]{screenshot_x}[/if_screenshot_x]', makeDropDown( $xfield_gallery, "images[xf_screens]", $kp_config['images']['xf_screens']));
showRow('Максимально допустимые размеры кадров для сжатия (если у вас нет доп поля "загружаемая галерея изображений")', 'Вы можете задать размер только одной стороны, например: 200, либо можете задать размеры сразу двух сторон, например: 150x100. Если выставить 0 то сжаимать не будет', showInput(['images[screens_max_up_side]', 'text', $kp_config['images']['screens_max_up_side']]));
echo <<<HTML
			</table>
		</div>
	</div>
    <button type="submit" class="btn bg-teal btn-raised position-left"><i class="fa fa-floppy-o position-left"></i>{$lang['user_save']}</button>
</form>
<div class="panel" style="margin-top: 20px;">
	<div class="panel-content">
		<div class="panel-body">
			© 2022 <a href="https://github.com/DLEPremium/kinopoisk-parser-dle" target="_blank">DLEPremium</a>
		</div>
	</div>
</div>
<script>
$(function() {
		
		$('.valueselect').chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено', max_selected_options: 1});
		$('.valuesselect').chosen({allow_single_deselect:true, no_results_text: 'Ничего не найдено'});
		
		function ajax_save_option() {
			var data_form = $('form').serialize();
			$.post('/engine/ajax/controller.php?mod=kinopoisk_save', {data_form: data_form, action: 'options', user_hash: '{$dle_login_hash}'}, function(data) {
				data = jQuery.parseJSON(data);
				if (!data.success) {
					Growl.error({
						title: 'Ошибка сохранения!',
						text: 'Проверьте права доступа к файлу настроек'
					});
				} else {
					Growl.info({
						title: 'Настройки применены!',
						text: 'Настройки модуля были успешно сохранены',
						icon: 'success'
					});
				}
			});
			return false;
		}
		
		$('body').on('submit', 'form', function(e) {
			e.preventDefault();
			ajax_save_option();
			return false;
		});
});
</script>
HTML;
echofooter();
?>
