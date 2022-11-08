<?php

/*
=====================================================
 Copyright (c) 2022 DLEPremium
=====================================================
 This code is protected by copyright
=====================================================
*/
 
if( !defined('DATALIFEENGINE') ) {
	die('Hacking attempt!');
}

if( $config['allow_admin_wysiwyg'] == 0 ) {
	$short_st = "$('#short_story').val(";
	$full_st = "$('#full_story').val(";
}
elseif( $config['allow_admin_wysiwyg'] == 1 ) {
	$short_st = "$('#short_story').froalaEditor('html.set', ";
	$full_st = "$('#full_story').froalaEditor('html.set', ";
}
elseif( $config['allow_admin_wysiwyg'] == 2 ) {
	$short_st = "tinymce.get('short_story').setContent(";
	$full_st = "tinymce.get('full_story').setContent(";
}

include_once ENGINE_DIR . '/dlepremium/kinopoisk_parser/data/config.php';

?>
<style>
ol {
    counter-reset: i;
    list-style: none;
    font: 14px 'trebuchet MS', 'lucida sans';
    padding: 0;
    margin-bottom: 0em;
    text-shadow: 0 1px 0 rgba(255,255,255,.5);
	overflow: auto;
    width: 100%;
    max-height: 200px;
}

ol ol {
    margin: 0 0 0 2em;
}

.rounded-list i{

    position: relative;
    display: block;
    padding: .4em .4em .4em 3em;
    *padding: .4em;
    margin: .5em 0;
    background: #ddd;
    color: #444;
    text-decoration: none;
    border-radius: .3em;
    transition: all .3s ease-out;   
}

.rounded-list i:hover{
    background: #eee;
}

.rectangle-list i{
    position: relative;
    display: block;
    padding: .4em .4em .4em .8em;
    *padding: .4em;
    margin: .5em 0 .5em 2.5em;
    background: #ddd;
    color: #444;
    text-decoration: none;
    transition: all .3s ease-out;   
}

.rectangle-list i:hover{
    background: #eee;
}   

.rectangle-list i:before{
    content: counter(i);
    counter-increment: i;
    position: absolute; 
    left: -2.5em;
    top: 50%;
    margin-top: -1em;
    background: #fa8072;
    height: 2em;
    width: 2em;
    line-height: 2em;
    text-align: center;
    font-weight: bold;
}

.rectangle-list i:after{
    position: absolute; 
    content: '';
    border: .5em solid transparent;
    left: -1em;
    top: 50%;
    margin-top: 5em;
    transition: all .3s ease-out;               
}

.rectangle-list i:hover:after{
    left: -.5em;
    border-left-color: #fa8072;             
}

.btn-rights {
	float: right;
	margin: -5px 0 0 5px;
	
	background: #87ceeb;
}

.kinopoisk_parser_style__dialog {
    position: static;
    max-height: 70vh;
}
.kinopoisk_parser_style__dialogs {
    margin-top: 23px;
}

.movie-card{
  width: 90%;
  margin:auto;
  padding: 20px 30px 20px 15px;
  border-radius: 10px;
  background-blend-mode: multiply;
  position: relative;
  margin-bottom: 20px;
  z-index: 1;
  box-shadow: 0 3px 10px 0 rgba(0,0,0,0.5);
  transition: all 0.5s ease;
}
.card-overlay {
    background: linear-gradient(to right, rgba(42, 159, 255, 0.2) 0%, #212120 60%, #212120 100%);
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: -1;
    border-radius: 10px;
}
.card{
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: flex-end;
}
.movie-card-description{
  width: 400px;
  color: #fff;
}
.movie-title{
  margin: 0;
  font-family: Montserrat;
}
.movie-subtitle{
  margin-top: 5px;
  font-family: Lobster;
}
.movie-shorts{
  font-family: Roboto;
  text-align: justify;
}

.watch-btn{
  width: 200px;
  font-family: Fira Sans;
  margin: auto;
  padding: 10px;
  border: 1px solid #fff;
  background: transparent;
  font-size: 15px;
  cursor: pointer;
  color: #fff;
  transition: all 0.5s ease;
}

.watch-btn:hover{
  color: #f00;
  border: 1px solid #f00;
}

.card-share{
  position: absolute;
  display: flex;
}
.share-link {
    text-decoration: none;
    color: #e3e3e3;
    margin: 5px;
}

@media screen and (max-width: 768px){
  .movie-card{
    width: 90%;
    box-sizing: border-box;
    padding: 20px;
  }
  .card{
    flex-direction: row;
  }
  .card-overlay {
    background: linear-gradient(to bottom, rgba(42, 159, 255, 0.2) 0%, #212120 60%, #212120 100%);
  }
  .card-share{
    position: relative;
  }
}
</style>
<script type="text/javascript">

    function kinopoisk_search(){

            ShowLoading('');
            $.ajax({
                url: "/engine/ajax/controller.php?mod=kinopoisk_parser",
                data:{action: 'parser_search',  title: document.addnews.title.value},
                dataType: "json",
                cache: false,
                success: function(data) {
					if ( data.status == "results" ) {

                        HideLoading('');
                        var results = "<div style=\"width: auto; min-height: 39px; height: auto;\" class=\"kinopoisk_parser_style__dialog ui-dialog-content ui-widget-content\" scrolltop=\"0\" scrollleft=\"0\"><div class=\"kinopoisk_parser_style__dialogs\">";
                        $.each(data.result, function(key, item) {
							
							results += "<div class=\"movie-card\" style=\"background-image: url('" + item.poster + "'); background-position: center; background-size: cover;\">";
							results += "<div class=\"card-overlay\"></div>";
							results += "<div class=\"card-share\">";
							if( item.kp_link ) results += "<a class=\"share-link\" href=\"" + item.kp_link + "\" target=\"_blank\" rel=\"noreferrer\"><p style=\"background: url('https://st.kp.yandex.net/public/img/favicons/favicon-57.png') no-repeat;background-size: 100%;width:17px;height:17px;\"></p></a>";
							if( item.imdb_link ) results += "<a class=\"share-link\" href=\"" + item.imdb_link + "\" target=\"_blank\" rel=\"noreferrer\"><p style=\"background: url('https://m.media-amazon.com/images/G/01/imdb/images-ANDW73HA/favicon_desktop_32x32._CB1582158068_.png') no-repeat;background-size: 100%;width:17px;height:17px;\"></p></a>";
							results += "</div>";
							results += "<div class=\"card\">";
							results += "<div class=\"movie-card-description\">";
							if( item.orig_title ) results += "<h1 class=\"movie-title\">" + item.title + " / " + item.orig_title + "</h1>";
							else results += "<h1 class=\"movie-title\">" + item.title + "</h1>";
							results += "<p class=\"movie-subtitle\">" + item.kind + " " + item.year + " года. " + item.info + "</p>";
							if( item.plot ) results += "<p class=\"movie-shorts\">" + item.plot + "</p>";
							if (item.find_id == 'est') {
							    results += "<button onclick=\"window.open('" + item.edit_link + "')\" type=\"button\" class=\"watch-btn\"><i class=\"fa fa-link\" aria-hidden=\"true\"></i> &emsp; Уже есть на сайте</button><button onclick=\"kinopoisk_get('" + item.kp_id + "')\" type=\"button\" class=\"watch-btn\"><i class=\"fa fa-clone\" aria-hidden=\"true\"></i> &emsp; Парсить</button>";
							}
							else  results += "<button onclick=\"kinopoisk_get('" + item.kp_id + "')\" type=\"button\" class=\"watch-btn\"><i class=\"fa fa-clone\" aria-hidden=\"true\"></i> &emsp; Парсить</button>";
							results += "</div></div></div>";
							
                        });
                        results += "</div></div>";
                        $("#kinopoisk_result").html(results);

                    } else if ( data.status == "paste" ) {

                        kinopoisk_get(data.result);
                        HideLoading('');

                    } else {

                        HideLoading('');
                        alert('Ничего не найдено!');

                    }

                }
            });

    }

    function kinopoisk_get( kp_id ){

        if( !kp_id ){

            alert('Error!');

        } else {

            ShowLoading('');
            $.ajax({
                url: "/engine/ajax/controller.php?mod=kinopoisk_parser",
                data:{action: 'kinopoisk_get', kp_id: kp_id},
                dataType: "json",
                cache: false,
                success: function(data) {

                    if ( data.status == "paste" ) {
                        
                        HideLoading('');
                        kinopoisk_paste(data.result);

                    } else {

                        HideLoading('');
                        alert('Error!');

                    }

                }
            });

        }

    }

    function kinopoisk_paste(data){

        $.each(data, function(name, value) {
            
            if ( name == 'xf_poster' && value != '' ) {
                $('#uploadedfile_'+data.xf_poster_name+'').html(value);
                $('#xf_'+data.xf_poster_name+'').val(data.xf_poster_url);
                $('#xfupload_'+data.xf_poster_name+' .qq-upload-button, #xfupload_'+data.xf_poster_name+' .qq-upload-button input').attr("disabled","disabled");
            } else if ( name == 'xf_logo' && value != '' ) {
                $('#uploadedfile_'+data.xf_logo_name+'').html(value);
                $('#xf_'+data.xf_logo_name+'').val(data.xf_logo_url);
                $('#xfupload_'+data.xf_logo_name+' .qq-upload-button, #xfupload_'+data.xf_logo_name+' .qq-upload-button input').attr("disabled","disabled");
            } else if ( name == 'xf_cover' && value != '' ) {
                $('#uploadedfile_'+data.xf_cover_name+'').html(value);
                $('#xf_'+data.xf_cover_name+'').val(data.xf_cover_url);
                $('#xfupload_'+data.xf_cover_name+' .qq-upload-button, #xfupload_'+data.xf_cover_name+' .qq-upload-button input').attr("disabled","disabled");
            } else if ( name == 'xf_screens' && value != '' ) {
                $('#uploadedfile_'+data.xf_screens_name+'').html(value);
                $('#xf_'+data.xf_screens_name+'').val(data.xf_screens_url);
            } else if ( $('#xf_'+name).attr('data-rel') == 'links' ){
                $('#xf_'+name).tokenfield('setTokens', value);
            } else if ( value != '' ) {
                $('#xf_'+name).val(value);
            }
                
        });

        if( data.title ) $("input[name=title]").val(data.title);
        if( data.alt_name ) $("input[name=alt_name]").val(data.alt_name);
        if( data.tags ) $('[name=tags]').tokenfield('setTokens', data.tags);

        if( data.meta_titles ) $('input[name=meta_title]').val(data.meta_titles);
        if( data.meta_descrs ) $('input[name=descr]').val(data.meta_descrs);
        if( data.meta_keywords ) $('[name=keywords]').tokenfield('setTokens', data.meta_keywords);
		if( data.catalog ) $('input[name=catalog_url]').val(data.catalog);
        
        if( data.short_story ) {
            <?php echo $short_st; ?>data.short_story<?php echo ");\n"; ?>
        }
        if( data.full_story ) {
            <?php echo $full_st; ?>data.full_story<?php echo ");\n"; ?>
        }
		
		if( data.parse_cat_list ) {
		
			$.each(data.parse_cat_list.split(','), function(index, value) {
				$('.categoryselect option[value='+ value +']' ).prop("selected", true);
			});

            $('.categoryselect').trigger('chosen:updated');
            $('#category_custom_sort').val(data.parse_cat_list.split(',').join('::'));
		}

        return;

    }

</script>
