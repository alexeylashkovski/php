$(document).ready(function(){
	/*************************************************************/
	/***************** Address Suggestions begin *****************/
	/*************************************************************/
	(function($) {  
	  var $last_suggestion;

	  var 
		token = "bdbac244fcf67a23fd6f27a525ca5b77681b520d",
		type  = "ADDRESS",
		$address = $('#address'),
		$region = $("#region"),
		$city   = $("#city"),
		$street = $("#street"),
		$house  = $("#house");

		$("#email").suggestions({
			token: token,
			type: "EMAIL",
			onSelect: function(suggestion) {
//				console.log(suggestion);
			}
		});


		// адрес одной строкой 
		$address.suggestions({
			token: token,
			type: type,
			hint: false,
			onSelect: function (suggestion) {
				$region.suggestions('setSuggestion', suggestion);
				$city.suggestions('setSuggestion', suggestion);
				$street.suggestions('setSuggestion', suggestion);
				$house.suggestions('setSuggestion', suggestion);
			}
		});

		// регион и район
		$region.suggestions({
			token: token,
			type: type,
			hint: false,
			bounds: "region",
			onSelect: function (suggestion) {
				$('#region-wrapper').addClass('input-success');
				setZip(suggestion);
				checkRegion();
			}		
		});

		// город и населенный пункт
		$city.suggestions({
			token: token,
			type: type,
			hint: false,
			bounds: "city-settlement",
			constraints: $region,
			onSelect: function (suggestion) {
				$('#city-wrapper').addClass('input-success');
				setZip(suggestion);
				checkCity();
				
				$('#region-wrapper').addClass('input-success');
				checkRegion();
			},
			onSelectNothing: function() {
				checkCity();
			}
		});
  
		// улица
		$street.suggestions({
			token: token,
			type: type,
			hint: false,
			bounds: "street",
			constraints: $city,
			onSelect: function (suggestion) {
				$('#street-wrapper').addClass('input-success');
				setZip(suggestion);
				checkStreet();
			},
			onSelectNothing: function() {
				checkStreet();
			}
		});
	
		// дом
		$house.suggestions({
			token: token,
			type: type,
			hint: false,
			bounds: "house",
			constraints: $street,
			onSelect: function (suggestion) {
//				setAdress($house.suggestions().selection);
				$('#house-wrapper').addClass('input-success');
				setZip(suggestion);
				checkHouse();
			},
			onSelectNothing: function() {
				checkHouse();
			}
		});

	function setZip(suggestion) {
		if (suggestion.data.postal_code) {
			$('#zip').val(suggestion.data.postal_code);
			checkZip();
		}
	}
})(jQuery);
	/*************************************************************/
	/***************** Address Suggestions end *******************/
	/*************************************************************/		

	//--блокируем ненужные символы введенные в поле 'Почтовый индекс'
    $("#zip").keydown(function(event) {
		input_value = $(this).val();
        // Разрешаем: backspace, delete, tab и escape
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
             // Разрешаем: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Разрешаем: home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // Ничего не делаем
                 return;
        }
        else {
			// Если цифр меньше 6-ти или
            // Убеждаемся, что это цифра, и останавливаем событие keypress
            if (input_value.length >= 6 || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
			}
        }
    });






	/**************************************************************/
	/***************** Address Suggestions2 begin *****************/
	/**************************************************************/
	(function($) {  
	  var $last_suggestion2;

	  var 
		token2 = "bdbac244fcf67a23fd6f27a525ca5b77681b520d",
		type2  = "ADDRESS",
		$address2 = $('#address2'),
		$region2 = $("#region2"),
		$city2   = $("#city2"),
		$street2 = $("#street2"),
		$house2  = $("#house2");



		$("#email2").suggestions({
			token: token2,
			type: "EMAIL",
			onSelect: function(suggestion) {
//				console.log(suggestion);
			}
		});


		// адрес одной строкой 
		$address2.suggestions({
			token: token2,
			type: type2,
			hint: false,
			onSelect: function (suggestion) {
				$region2.suggestions('setSuggestion', suggestion);
				$city2.suggestions('setSuggestion', suggestion);
				$street2.suggestions('setSuggestion', suggestion);
				$house2.suggestions('setSuggestion', suggestion);
			}
		});

		// регион и район
		$region2.suggestions({
			token: token2,
			type: type2,
			hint: false,
			bounds: "region",
			onSelect: function (suggestion) {
				$('#region2-wrapper').addClass('input-success');
				setZip2(suggestion);
				checkRegion2();
			}		
		});

		// город и населенный пункт
		$city2.suggestions({
			token: token2,
			type: type2,
			hint: false,
			bounds: "city-settlement",
			constraints: $region2,
			onSelect: function (suggestion) {
				$('#city2-wrapper').addClass('input-success');
				setZip2(suggestion);
				checkCity2();
				
				$('#region2-wrapper').addClass('input-success');
				checkRegion2();
			},
			onSelectNothing: function() {
				checkCity2();
			}
		});
  
		// улица
		$street2.suggestions({
			token: token2,
			type: type2,
			hint: false,
			bounds: "street",
			constraints: $city2,
			onSelect: function (suggestion) {
				$('#street2-wrapper').addClass('input-success');
				setZip2(suggestion);
				checkStreet2();
			},
			onSelectNothing: function() {
				checkStreet2();
			}
		});
	
		// дом
		$house2.suggestions({
			token: token2,
			type: type2,
			hint: false,
			bounds: "house",
			constraints: $street2,
			onSelect: function (suggestion) {
//				setAdress($house.suggestions().selection);
				$('#house2-wrapper').addClass('input-success');
				setZip2(suggestion);
				checkHouse2();
			},
			onSelectNothing: function() {
				checkHouse2();
			}
		});

	function setZip2(suggestion) {
		if (suggestion.data.postal_code) {
			$('#zip2').val(suggestion.data.postal_code);
			checkZip2();
		}
	}


})(jQuery);
	/**************************************************************/
	/***************** Address Suggestions2 end *******************/
	/**************************************************************/		

	//--блокируем ненужные символы введенные в поле 'Почтовый индекс'
    $("#zip2").keydown(function(event) {
		input_value = $(this).val();
        // Разрешаем: backspace, delete, tab и escape
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || 
             // Разрешаем: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Разрешаем: home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // Ничего не делаем
                 return;
        }
        else {
			// Если цифр меньше 6-ти или
            // Убеждаемся, что это цифра, и останавливаем событие keypress
            if (input_value.length >= 6 || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
			}
        }
    });


});


	/***************************************************************/
	/***************** Address Normalization begin *****************/
	/***************************************************************/
	function normalizeAddress(full_adress_string) {
		if (!full_adress_string) {
			full_adress_string = '';
			if ($('#region').val()) {
				full_adress_string += $('#region').val();
			}
			if ($('#city').val() && $('#city').val() != full_adress_string) {
				if (full_adress_string != '') {
					full_adress_string += ', ';
				}
				full_adress_string += $('#city').val();
			}
			if ($('#street').val()) {
				if (full_adress_string != '') {
					full_adress_string += ', ';
				}
				full_adress_string += $('#street').val();
			}
			if ($('#house').val()) {
				if (full_adress_string != '') {
					full_adress_string += ', ';
				}
				full_adress_string += $('#house').val();
			}
			if ($('#flat').val()) {
				if (full_adress_string != '') {
					full_adress_string += ', ';
				}
				full_adress_string += $('#flat').val();
			}
		}
		var url = '/ajax/';
		var data = 'act=check_address&full_adress_string=' + full_adress_string;
		$.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function (data) {
				var obj = JSON.parse ( data );
				var flagNormalisationIsOk = true;
				if (obj.detail == "Zero balance") {
					console.log('!!! DaData zero balance !!!');
				} else {
					$('#normalized_region').val(obj[0].region + ' ' + obj[0].region_type);
					if (obj[0].city_district != null) {
						$('#normalized_district').val(obj[0].city_district + ' ' + obj[0].city_district_type);
					} else {
						if (obj[0].area != null) {
							if (obj[0].area) {
								$('#normalized_district').val(obj[0].area + ' ' + obj[0].area_type);
							}
						}
					}					
					if (obj[0].settlement != null) {
						if (obj[0].settlement) {
							$('#normalized_city').val(obj[0].settlement + ' ' + obj[0].settlement_type);
							$('#city_type').val(obj[0].settlement_type);
						}
					} else {
						if (obj[0].city) {
							$('#normalized_city').val(obj[0].city + ' ' + obj[0].city_type);
							$('#city_type').val(obj[0].city_type);
						} else {
							$('#normalized_city').val(obj[0].region + ' ' + obj[0].region_type);
						}
					}
					$('#normalized_street').val(obj[0].street + ' ' + obj[0].street_type);
					$('#normalized_house').val(obj[0].house);
					if (obj[0].flat == null && obj[0].flat_type == null) {
						$('#normalized_flat').val('');
					} else {
						$('#normalized_flat').val(obj[0].flat);
					}
					if (obj[0].settlement_type != null) {
						$('#normalized_city_type').val(obj[0].settlement_type);
					} else {
						if (obj[0].city_type != null) {
							$('#normalized_city_type').val(obj[0].city_type);
						} else {
							$('#normalized_city_type').val(obj[0].region_type);
						}
					}
					if (obj[0].street_type != null) {
						$('#normalized_street_type').val(obj[0].street_type);
					}
					if (obj[0].house_type != null) {
						$('#normalized_house_type').val(obj[0].house_type);
					}
					if (obj[0].block_type != null) {
						$('#normalized_korpus_type').val(obj[0].block_type);
						$('#korpus').val(obj[0].block);
					} else {
						$('#korpus').val('');
					}
					if (typeof $('#normalized_street_kladr_id') != 'undefined') {
						$('#normalized_street_kladr_id').val(obj[0].street_kladr_id);
					}
					if (obj[0].street == null || obj[0].house == null) {
						flagNormalisationIsOk = false;
						console.log('!!! Address normalization is failed !!!');
						alert.log('!!! Address normalization is failed !!!');
					}
				}
				if (flagNormalisationIsOk) {
					$("#form_podpiska").submit();
				}
			}
		});
		return false;
	}
	/***************************************************************/
	/***************** Address Normalization end *******************/
	/***************************************************************/



	/*****************************************************************/
	/***************** Telephone Normalization begin *****************/
	/*****************************************************************/
	function normalizeTel(tel_num) {
		var url = '/ajax/';
		var data = 'act=check_telephone&tel_num=' + tel_num;
		$.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function (data) {
				var obj = JSON.parse ( data );
				if (obj.detail == "Zero balance") {
					console.log('!!! DaData zero balance !!!');
				} else {
					$('#normalized_tel_phone').val(obj[0].phone);
					$('#normalized_tel_type').val(obj[0].type);
					$('#normalized_tel_country_code').val(obj[0].country_code);
					$('#normalized_tel_city_code').val(obj[0].city_code);
					$('#normalized_tel_number').val(obj[0].number);
					$('#normalized_tel_extension').val(obj[0].extension);
					$('#normalized_tel_provider').val(obj[0].provider);
					$('#normalized_tel_region').val(obj[0].region);
					$('#normalized_tel_timezone').val(obj[0].timezone);
					$('#normalized_tel_qc_conflict').val(obj[0].qc_conflict);
					$('#normalized_tel_qc').val(obj[0].qc);

					if (obj[0].qc) {
					 	$('#tel-wrapper').removeClass('input-success');
					 	$('#tel').addClass('form_element_warning');
						$('#tel-dynamic-comment').html("<span style='color:orange;'>Возможно номер телефона введен неправильно</span>");
					} else if (obj[0].qc_conflict) {
					 	$('#tel-wrapper').removeClass('input-success');
					 	$('#tel').addClass('form_element_warning');
						$('#tel-dynamic-comment').html("<span style='color:orange;'>Номер телефона конфликтует с адресом</span>");
					} else {
					 	$('#tel-wrapper').addClass('input-success');
					 	$('#tel').removeClass('form_element_error');
					 	$('#tel').removeClass('form_element_warning');
						$('#tel-dynamic-comment').html("");
					}
				}
			}
		});
		return false;
	}
	/*****************************************************************/
	/***************** Telephone Normalization end *******************/
	/*****************************************************************/

	function checkTel(focus_on_error = false) {
		var text = $('#tel').val();
		if (text.length >= 5) {
			normalizeTel(text);
			return true;
		} else {
		 	$('#tel-wrapper').removeClass('input-success');
		 	$('#tel').addClass('form_element_error');
			$('#tel-dynamic-comment').html("Введен некорректный номер телефона");
		}
		if (focus_on_error) $('#tel').focus();
		return false;
	}

	function checkRegion(focus_on_error = false) {
		if (!$('#region-wrapper').hasClass('input-success')) {
		 	$('#region').addClass('form_element_error');
			$('#region-dynamic-comment').html("Необходимо ввести регион");
		} else {
		 	$('#region').removeClass('form_element_error');
			$('#region-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#city').focus();
		return false;
	}

	function checkCity(focus_on_error = false) {
		if (!$('#city-wrapper').hasClass('input-success')) {
		 	$('#city').addClass('form_element_error');
			$('#city-dynamic-comment').html("Необходимо ввести город");
		} else {
		 	$('#city').removeClass('form_element_error');
			$('#city-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#city').focus();
		return false;
	}

	function checkStreet(focus_on_error = false) {
		if (!$('#street-wrapper').hasClass('input-success')) {
		 	$('#street').addClass('form_element_error');
			$('#street-dynamic-comment').html("Необходимо ввести улицу");
		} else {
		 	$('#street').removeClass('form_element_error');
			$('#street-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#street').focus();
		return false;
	}
	
	function checkHouse(focus_on_error = false) {
		if (!$('#house-wrapper').hasClass('input-success')) {
		 	$('#house').addClass('form_element_error');
			$('#house-dynamic-comment').html("Необходимо ввести дом");
		} else {
		 	$('#house').removeClass('form_element_error');
			$('#house-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#house').focus();
		return false;
	}

	function checkZip(focus_on_error = false) {
		var zip = $('#zip').val();
		if (!$.isNumeric( zip )) {
		 	$('#zip').addClass('form_element_error');
			if (zip.length) {
				$('#zip-dynamic-comment').html("Почтовый индекс должен быть числом");
			} else {
				$('#zip-dynamic-comment').html("Необходимо ввести почтовый индекс");
			}
		} else {
			if (zip.length != 6) {
				$('#zip-dynamic-comment').html("В почтовом индексе должно быть 6 цифр");
			} else {
			 	$('#zip').removeClass('form_element_error');
				$('#zip-dynamic-comment').html("");
				return true;
			}
		}
		if (focus_on_error) $('#zip').focus();
		return false;
	}

	function checkFlat(focus_on_error = false) {
		if (!$('#flat-wrapper').hasClass('input-success')) {
		 	$('#flat').addClass('form_element_error');
			$('#flat-dynamic-comment').html("Необходимо ввести квартиру");
		} else {
		 	$('#flat').removeClass('form_element_error');
			$('#flat-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#flat').focus();
		return false;
	}


	function checkRegion2(focus_on_error = false) {
		if (!$('#region2-wrapper').hasClass('input-success')) {
		 	$('#region2').addClass('form_element_error');
			$('#region2-dynamic-comment').html("Необходимо ввести регион");
		} else {
		 	$('#region2').removeClass('form_element_error');
			$('#region2-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#city2').focus();
		return false;
	}

	function checkCity2(focus_on_error = false) {
		if (!$('#city2-wrapper').hasClass('input-success')) {
		 	$('#city2').addClass('form_element_error');
			$('#city2-dynamic-comment').html("Необходимо ввести город");
		} else {
		 	$('#city2').removeClass('form_element_error');
			$('#city2-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#city2').focus();
		return false;
	}

	function checkStreet2(focus_on_error = false) {
		if (!$('#street2-wrapper').hasClass('input-success')) {
		 	$('#street2').addClass('form_element_error');
			$('#street2-dynamic-comment').html("Необходимо ввести улицу");
		} else {
		 	$('#street2').removeClass('form_element_error');
			$('#street2-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#street2').focus();
		return false;
	}
	
	function checkHouse2(focus_on_error = false) {
		if (!$('#house2-wrapper').hasClass('input-success')) {
		 	$('#house2').addClass('form_element_error');
			$('#house2-dynamic-comment').html("Необходимо ввести дом");
		} else {
		 	$('#house2').removeClass('form_element_error');
			$('#house2-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#house2').focus();
		return false;
	}

	function checkZip2(focus_on_error = false) {
		var zip = $('#zip2').val();
		if (!$.isNumeric( zip )) {
		 	$('#zip2').addClass('form_element_error');
			if (zip.length) {
				$('#zip2-dynamic-comment').html("Почтовый индекс должен быть числом");
			} else {
				$('#zip2-dynamic-comment').html("Необходимо ввести почтовый индекс");
			}
		} else {
			if (zip.length != 6) {
				$('#zip2-dynamic-comment').html("В почтовом индексе должно быть 6 цифр");
			} else {
			 	$('#zip2').removeClass('form_element_error');
				$('#zip2-dynamic-comment').html("");
				return true;
			}
		}
		if (focus_on_error) $('#zip2').focus();
		return false;
	}

	function checkFlat2(focus_on_error = false) {
		if (!$('#flat2-wrapper').hasClass('input-success')) {
		 	$('#flat2').addClass('form_element_error');
			$('#flat2-dynamic-comment').html("Необходимо ввести квартиру");
		} else {
		 	$('#flat2').removeClass('form_element_error');
			$('#flat2-dynamic-comment').html("");
			return true;
		}
		if (focus_on_error) $('#flat2').focus();
		return false;
	}
