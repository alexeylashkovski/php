$.extend({
	getUrlVars: function(){
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	},
	getUrlVar: function(name){
		return $.getUrlVars()[name];
	}
});

$(document).ready(function(){
	$(".preloader").fadeOut();

	var mySwiper = new Swiper ('.front_slider .swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		grabCursor:true,
		loop: true,
		spaceBetween: 50,
		autoplay: {
			delay: 5000,
		},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
			clickable: true,
		},
		on: {
			init: function () {
				$('.front_slider_wrapper').removeClass('invisible');
			},
		},		
	})


	//++++++++++++++ Popups Global Closings ++++++++++++++
	$(".popup_overlay").on('click', function(e){		
		$(".popup_wrapper").fadeOut( "fast", function(){});
	});
    $(".popup_wrapper .close_icon, .popup_wrapper .button.no").click(function () {
        $(".popup_wrapper").fadeOut( "fast", function(){});
    });
	$("body").keydown(function(e) {
		if( e.keyCode === 27 ) {
	        $(".popup_wrapper").fadeOut( "fast", function(){});
		}
	});
	//++++++++++++++++++++++++++++++++++++++++++++++++++++

    $("#delete_magazine_button").click(function (e) {
		e.preventDefault();
       	$("#delete_magazine_popup").fadeIn( "fast", function(){});
    });
    $("#delete_magazine_popup .button.yes").click(function (e) {
		e.preventDefault();
		var magazine_id = $(this).attr( "magazine_id" );
		$.ajax({
			type: "POST",
			url: '/admin/',
			data: 'act=delete_magazine&magazine_id=' + magazine_id,
			success: function (data)
			{
				var obj = JSON.parse ( data );
				if (obj.status == "ok") {
			        $('.popup_wrapper ').fadeOut( "fast", function(){});
					alertify.success("Журнал успешно удален.");
					setTimeout(goToMainAdminPage, 1500);
				}
			}
		});
    });

    $("#delete_group_button").click(function (e) {
		e.preventDefault();
       	$("#delete_group_popup").fadeIn( "fast", function(){});
    });
    $("#delete_group_popup .button.yes").click(function (e) {
		e.preventDefault();
		var group_id = $(this).attr( "group_id" );
		$.ajax({
			type: "POST",
			url: '/admin/',
			data: 'act=delete_group&group_id=' + group_id,
			success: function (data)
			{
				var obj = JSON.parse ( data );
				if (obj.status == "ok") {
			        $('.popup_wrapper ').fadeOut( "fast", function(){});
					alertify.success("Комплект успешно удален.");
					setTimeout(goToMainAdminPage, 1500);
				}
			}
		});
    });

    $(".delete_promocode_link, #delete_promocode_button").click(function (e) {
		e.preventDefault();
		var promocode_id = $(this).attr( "promocode_id" );
		var promocode_code_text = $(this).attr( "promocode_code_text" );
		$('#delete_promocode_popup_code_text').html(promocode_code_text);
       	$("#delete_promocode_popup").fadeIn( "fast", function(){});
       	$("#delete_promocode_popup .button.yes").attr('promocode_id', promocode_id);
    });
    $("#delete_promocode_popup .button.yes").click(function (e) {
		e.preventDefault();
		var promocode_id = $(this).attr( "promocode_id" );
		$.ajax({
			type: "POST",
			url: '/admin/',
			data: 'act=delete_promocode&promocode_id=' + promocode_id,
			success: function (data)
			{
				var obj = JSON.parse ( data );
				if (obj.status == "ok") {
			        $('.popup_wrapper ').fadeOut( "fast", function(){});
					alertify.success("Промокод успешно удален.");
					setTimeout(goToMainAdminPage, 1500);
				}
			}
		});
    });    
    
	function goToMainAdminPage() {
		window.location = '/admin/';
	}
	
	$("#form_choose_magazine").submit(function (e) {
		if ($("#form_choose_magazine #magazine").val()) {
			$("#form_choose_magazine").submit();
		} else {
			e.preventDefault();
			$("#form_choose_magazine #magazine").addClass('form_element_error');
		}
    });
	$("#form_choose_group").submit(function (e) {
		if ($("#form_choose_group #group").val()) {
			$("#form_choose_group").submit();
		} else {
			e.preventDefault();
			$("#form_choose_group #group").addClass('form_element_error');
		}
    });


	$("#submit_button").on('click', function(e){
		e.preventDefault();
		if (!checkForm()) {
//			e.preventDefault();
		} else {
			$("#city").prop('disabled', false);
			$("#city").prop('readonly', false);
			$(".preloader").fadeIn();
		}
		return false;
    });	


	$("#form_choose_magazine #magazine").change(function() {
		$("#form_choose_magazine #magazine").removeClass('form_element_error');
	});
	$("#form_choose_group #group").change(function() {
		$("#form_choose_group #group").removeClass('form_element_error');
	});

	$(".mob_burger").on('click', function(e){		
		e.preventDefault();
		$(this).toggleClass('is-active');
	});
	
	$("#radio_promocode_type1, #radio_promocode_type2").on('click', function(e){		
		$("#radio_wrapper_for_all_magazines_checkboxes").toggleClass('hidden');
	});	
	
	var promocode_discount = 0;
	$(".subscribe_type_wrapper .landing-radio").on('click', function(e){		
		var monthes = $(this).attr("monthes");
		$("#form_monthes").attr("value", monthes);
		var price = $(this).attr("price");
		var price_discounted = $(this).attr("price_discounted");
		$(".final_price_wrapper #price .sum").html(price);
		$("#form_price").attr("value", price);
		if (promocode_discount) {
			$(".final_price_wrapper #price_discounted .sum").html(price_discounted);
			$("#form_price_discounted").attr("value", price_discounted);
		}
	});
	$("#promocode").keydown(function(e) {
		if( e.keyCode === 13 ) {
			e.preventDefault();
			$("#discount_button").trigger('click');
		}
	});
	$("#discount_button").on('click', function(e){		
		e.preventDefault();
		var url = $( this ).attr('action');
		var magazine_id = $( this ).attr('magazine_id');
		var promocode = $("#promocode").val();
		var error_container = $( this ).attr('error_container');
		var success_container = $( this ).attr('success_container');
		$("#" + error_container).html('');		
		if (promocode == '') {
			$("#" + error_container).html('Введите промокод!');
		} else {
			var data = 'act=check_promocode&promocode=' + promocode + '&magazine_id=' + magazine_id;
			$.ajax({
				type: "POST",
				url: url,
				data: data,
				success: function (data) {
					var obj = JSON.parse ( data );
					if (obj.hide_id) {
						$hide_id = $('#' + obj.hide_id);
						if ( $hide_id.length !== 0 ) {
							$hide_id.hide();
						}
					}
					if (obj.disable_id) {
						$disable_id = $('#' + obj.disable_id);
						if ( $disable_id.length !== 0 ) {
							$disable_id.prop('disabled', true);
						}
					}
					if (obj.status == 'ok_promocode') {
						promocode_discount = obj.discount_percent;
						$("#" + success_container).html("Применена скидка " + promocode_discount + "%");
						$("#form_promocode_id").attr("value", obj.promocode_id);
						$("#form_promocode_code_text").attr("value", obj.promocode_code_text);
						$("#form_promocode_discount_percent").attr("value", obj.discount_percent);

						$("#radio_monthes6").attr("price_discounted", obj.price_6_monthes_discounted);
						$("#radio_monthes12").attr("price_discounted", obj.price_12_monthes_discounted);
						if ($("input:radio:checked").val() == 12) {
							$("#form_price_discounted").attr("value", obj.price_12_monthes_discounted);
							$(".final_price_wrapper #price_discounted .sum").html(obj.price_12_monthes_discounted);
						} else {
							$("#form_price_discounted").attr("value", obj.price_6_monthes_discounted);
							$(".final_price_wrapper #price_discounted .sum").html(obj.price_6_monthes_discounted);
						}
						
						$("#form_price_6_monthes_discounted").attr("value", obj.price_6_monthes_discounted);
						$("#form_price_12_monthes_discounted").attr("value", obj.price_12_monthes_discounted);						
						
						$("#promocode").attr("disabled", true);
						$("#discount_button").attr("disabled", true);
						$(".final_price_wrapper #price .sum").addClass('landing-cover');
						$(".final_price_wrapper #price").addClass('old_price');
						$(".final_price_wrapper #price_discounted").removeClass('hidden');
					}
					if (obj.status == 'error') {
						$("#" + error_container).html(obj.error_text);
					}
				}
			});
			return false;
		}
	});


	var $all_magazines;
	$(".magazine-filter2 a").on('click', function(e){		
		e.preventDefault();
		var filter = $(this).attr( "data-filter" );
		if (filter == "front_magazine_cell") {
			$( ".magazines_wrapper" ).addClass('popular');
		} else {
			$( ".magazines_wrapper" ).removeClass('popular');
		}
		
		$(".magazine-filter2 li").removeClass('activeFilter');
		$(this).parents(".magazine-filter2 li").addClass('activeFilter');

		if (!$all_magazines) {	
			$all_magazines = $(".magazines_wrapper .front_magazine_cell").detach();
		}
     	$all_magazines.appendTo($(".magazines_wrapper .row"));
		$( ".magazines_wrapper .row .front_magazine_cell" ).each( function() {
			if (!$(this).hasClass(filter)) {
				$(this).remove();
			}
			if (filter == "front_magazine_cell") {
				if ($(this).hasClass('not_show_on_popular')) {
					$(this).remove();
				}
			}
		});
	});
	

	function ClearFormErrors(data, form_id) {
		var form_selector = '#' + form_id;
		$(form_selector + ' [feedback=1]').each(function (i, elem) {
			if ((data.errors == undefined) || (data.errors[$(this).attr('name')] == undefined))
			{
				$(this).removeClass('error');
				$(this).siblings(".help-block").text('');
			} else {
				$(this).addClass('error');
				$(this).siblings(".help-block").html(data.errors[$(this).attr('name')]);
			}
		});
	}


	$("#form_login").submit(function () {
		var url = $( this ).attr('action');
		var id = $( this ).attr('id');
		$.ajax({
			type: "POST",
			url: url,
			data: $( this ).serialize(),
			success: function (data) {
				var obj = JSON.parse ( data );
				ClearFormErrors(obj, id);
				if (obj.show_hidden_id) {
					$hidden_id = $('#' + obj.show_hidden_id);
					if ( $hidden_id.length !== 0 ) {
//						$hidden_id.show();
						$hidden_id.fadeIn();
					}
				}
				if (obj.hide_id) {
					$hide_id = $('#' + obj.hide_id);
					if ( $hide_id.length !== 0 ) {
						$hide_id.hide();
					}
				}
				if (obj.disable_id) {
					$disable_id = $('#' + obj.disable_id);
					if ( $disable_id.length !== 0 ) {
						$disable_id.prop('disabled', true);
					}
				}
				if (obj.not_logged_refresh == "1") {
					location.reload();
					return false;
				}
				if (obj.status == 'ok_login') {
					location.reload();
				}
			}
		});
		return false;
	});

	$(".front_magazine_cell img").on('error', function() {
		$(this).attr("src", "/_img/no_pic.png");
	});



	function checkCapitalized(text) {
		var regExp = /^[А-ЯЁ][а-яё]{2,}$/	//и для условия когда не менее 3 букв в Фамилии и Имени и Отчестве:
		if (regExp.test(text)) {
			return true;
		}
		return false;
	}
	
	function checkFIO(focus_on_error = false) {
		var text = $('#fio').val();
	    if (typeof text == 'undefined') {
			return true;
		} else {
//			var regExp = /^([А-ЯA-Z]|[А-ЯA-Z][\x27а-яa-z]{1,}|[А-ЯA-Z][\x27а-яa-z]{1,}\-([А-ЯA-Z][\x27а-яa-z]{1,}|(оглы)|(кызы)))\040[А-ЯA-Z][\x27а-яa-z]{1,}(\040[А-ЯA-Z][\x27а-яa-z]{1,})?$/
//			var regExp = /^[А-ЯЁ][а-яё]*([-][А-ЯЁ][а-яё]*)?\s[А-ЯЁ][а-яё]*\s[А-ЯЁ][а-яё]*$/		//Проверка полных ФИО с двойной Фамилией через тире (с учётом буквы Ё ё):
			var regExp = /^[А-ЯЁ][а-яё]{2,}([-][А-ЯЁ][а-яё]{2,})?\s[А-ЯЁ][а-яё]{2,}\s[А-ЯЁ][а-яё]{2,}$/;	//и для условия когда не менее 3 букв в Фамилии и Имени и Отчестве:
			if (regExp.test(text)) {
			 	$('#fio-wrapper').addClass('input-success');
			 	$('#fio').removeClass('form_element_error');
				$('#fio-dynamic-comment').html("");
				return true;
			} else {
			 	$('#fio-wrapper').removeClass('input-success');
			 	$('#fio').addClass('form_element_error');

				var arr_fio = $('#fio').val().split(" ");			
				var flagNotCapitalized = false;
				arr_fio.forEach(function(item, i, arr_fio) {
					if (!checkCapitalized(item)) {
						flagNotCapitalized = true;
					}
				});
				if (flagNotCapitalized) {
					$('#fio-dynamic-comment').html("<b>Фамилия Имя Отчество</b> должны начинаться с заглавной буквы");
				}
				if (arr_fio.length != 3) {
					$('#fio-dynamic-comment').html("Необходимо ввести <b>Фамилию Имя Отчество</b>, через пробел");
				}
			}
			if (focus_on_error) $('#fio').focus();
			return false;
		}
	}	

	function checkEmail(focus_on_error = false) {
		var text = $('#email').val();
	    if (typeof text == 'undefined') {
			return true;
		} else {
			var regExp = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
			if (regExp.test(text)) {
			 	$('#email-wrapper').addClass('input-success');
			 	$('#email').removeClass('form_element_error');
				$('#email-dynamic-comment').html("");
				return true;
			} else {
			 	$('#email-wrapper').removeClass('input-success');
			 	$('#email').addClass('form_element_error');
				$('#email-dynamic-comment').html("Введен некорректный e-mail");
			}
			if (focus_on_error) $('#email').focus();
			return false;
		}
	}
	
	function checkAddress(focus_on_error = false) {
		var el = $('#address');
		var error_text = '';
		var errors_num = 0;

		var addr_zip = el.attr("addr_zip");
		if (!addr_zip) {
			if (errors_num > 0) error_text += ", ";
			error_text += "почтовый индекс";
			errors_num++;
		}

		var addr_city = el.attr("addr_city");
		if (!addr_city) {
			if (errors_num > 0) error_text += ", ";
			error_text += "город";
			errors_num++;
		}

		var addr_street = el.attr("addr_street");
		if (!addr_street) {
			if (errors_num > 0) error_text += ", ";
			error_text += "улицу";
			errors_num++;
		}

		var addr_house = el.attr("addr_house");
		if (!addr_house) {
			if (errors_num > 0) error_text += ", ";
			error_text += "номер дома";
			errors_num++;
		}

		var addr_flat = el.attr("addr_flat");
		if (!addr_flat) {
			if (errors_num > 0) error_text += ", ";
			error_text += "номер квартиры";
			errors_num++;
		}

		if (error_text) {
			$('#address-dynamic-comment').html('Введите ' + error_text);
		 	$('#address-wrapper').removeClass('input-success');
		 	$('#address').addClass('form_element_error');
		} else {		
			$('#address-dynamic-comment').html('');
		 	$('#address-wrapper').addClass('input-success');
		 	$('#address').removeClass('form_element_error');
			return true;
		}
		if (focus_on_error) $('#address').focus();
		return false;
	}


	function checkAgree(focus_on_error = false) {
		if (!$('#agree').prop('checked')) {
		 	$('#agree').addClass('form_element_error');
			$('#agree-dynamic-comment').html("Необходимо ознакомиться и подтвердить");
		} else {
		 	$('#agree').removeClass('form_element_error');
			$('#agree-dynamic-comment').html('');
			return true;
		}
		if (focus_on_error) $('#agree').focus();
		return false;
	}

	$("#agree").on('click', function(e){
		checkAgree();
	});


	$('#fio').blur(function(){
		checkFIO();
	});
	$('#email').blur(function(){
		checkEmail();
	});	
	$('#tel').blur(function(){
		checkTel();
	});
	$('#address').blur(function(){
		checkAddress();
	});
	$('#region').blur(function(){
		checkRegion();
	});
	$('#city').blur(function(){
		checkCity();
	});
	$('#street').blur(function(){
		checkStreet();
	});
	$('#house').blur(function(){
		checkHouse();
	});
	$('#zip').blur(function(){
		checkZip();
	});
	
	function checkForm() {
		var flag_error = false;
		if (!checkAgree(true)) flag_error = true;
		if (!checkZip(true)) flag_error = true;
		if (!checkHouse(true)) flag_error = true;
		if (!checkStreet(true)) flag_error = true;
		if (!checkCity(true)) flag_error = true;
		if (!checkRegion(true)) flag_error = true;
		if (!checkTel(true)) flag_error = true;
		if (!checkEmail(true)) flag_error = true;
		if (!checkFIO(true)) flag_error = true;
		if (flag_error) {
			return false;
		} else {
			normalizeAddress();
		}
		return false;
	}


    $(".hamburger-menu .close_icon").click(function () {
		$(".mob_burger").removeClass('is-active');
    });
    
    $(".bottom_line_subscribe").click(function () {
		$("#radio_monthes12").trigger('click');
		$(this).fadeOut("slow");
		$('html , body').stop().animate({
			scrollTop: $(".subscribe_type_wrapper").offset().top - 180
		}, 500);
    });

    $("#address2").click(function (e) {
		$("#form_hidden_address2").toggleClass("hidden_my");
    });

    $("#change_subscribe_date").click(function (e) {
		$("#subscribe-date").prop('disabled', !$("#change_subscribe_date").prop('checked'));
	});

	$("#form_change_magazine").submit(function (e) {
		var flagAllCategoriesEmpty = true;
		$( ".checkbox_wrapper div input" ).each( function() {
			if ($(this).prop('checked')) {
				flagAllCategoriesEmpty = false;
			}
		});    	
		if (flagAllCategoriesEmpty) {
			e.preventDefault();
			alert('У журнала не выбрана ни одна категория!');
		} else {
			$("#form_change_magazine").submit();
		}
    });
});
