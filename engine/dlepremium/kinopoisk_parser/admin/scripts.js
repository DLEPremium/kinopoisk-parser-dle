function ChangeOption(obj, selectedOption) {
	$('#option_menu li').removeClass('active');
	$(obj).parent().addClass('active');
	document.getElementById('settings').style.display = 'none';
	document.getElementById('xfields').style.display = 'none';
	document.getElementById('categories').style.display = 'none';
	document.getElementById('images').style.display = 'none';
	document.getElementById(selectedOption).style.display = '';

	return false;
}

$(document).ready(function(){
$(".rcol-2col-header").click (function(){

    $(this).next(".rcol-2col-body").stop().slideToggle(300);
    if ($(this).children('.show-hide').text() == 'Show') {
      $(this).children('.show-hide').text('Hide');
    }
    else {
      $(this).children('.show-hide').text('Show');
    }
  });
});