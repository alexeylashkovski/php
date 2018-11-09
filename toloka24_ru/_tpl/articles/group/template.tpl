<div class="content magazine_wrapper">
	<div class="container">
		<h1><?= remove_br($arr_group_info["name"]) ?> - Оформить подписку на журнал</h1>

		<div class="under_wrapper">
			<div class="row">	
				<div class="col-md-3">
					<img class="magazine_cover" src="/_img/group/<?= $arr_group_info["id"] ?>/cover.jpg" />
				</div>
				<div class="col-md-9">
					<?php if ($arr_group_info["subtitle"]) { ?><h2><?= $arr_group_info["subtitle"] ?></h2><?php } ?>
					<div class="magazine_properties_wrapper">
						<?php if (false) {
//if ($arr_group_info["page_size"]) { ?>
							<div class="eItemProperties_line">
								<div class="eItemProperties_name">
									<div class="eItemProperties_nameBackground">Формат издания</div>
								</div>	
								<div class="eItemProperties_text">
									<span class="eItemProperties_textinner"><?= $arr_group_info["page_size"] ?></span>
								</div>
							</div>						
						<?php } ?>
						<?php if (false) {
//if ($arr_group_info["month_releases_num"]) { ?>
							<div class="eItemProperties_line">
								<div class="eItemProperties_name">
									<div class="eItemProperties_nameBackground">Количество номеров в году</div>
								</div>	
								<div class="eItemProperties_text">
									<span class="eItemProperties_textinner"><?= $arr_group_info["month_releases_num"]*12 ?></span>
								</div>
							</div>						
						<?php } ?>
					</div>
					<?php if ($arr_group_info["description"]) { ?>
						<div class="row">	
							<div class="col-md-12">
								<div class="magazine__description">
									<?= $arr_group_info["description"] ?>
								</div>
							</div>		
						</div>
					<?php } ?>
				</div>
			</div>

			<form id="form_podpiska" action="/subscribe/" method="post">
				<input type="hidden" name="act" value="subscribe" />
				<input type="hidden" name="group_id" value="<?= $arr_group_info["id"] ?>" />
				<input type="hidden" name="monthes" id="form_monthes" value="12" />
				<input type="hidden" name="post_index" value="<?= $arr_group_info["post_index"] ?>" />
				<input type="hidden" name="promocode_id" id="form_promocode_id" value="" />
				<input type="hidden" name="promocode_code_text" id="form_promocode_code_text" value="" />
				<input type="hidden" name="promocode_discount_percent" id="form_promocode_discount_percent" value="" />
				<input type="hidden" name="price" id="form_price" value="<?= $arr_group_info["price_12_monthes"] ?>" />
				<input type="hidden" name="price_discounted" id="form_price_discounted" value="" />				

				<input type="hidden" name="price_6_monthes" id="form_price_6_monthes" value="<?= $arr_group_info["price_6_monthes"] ?>" />
				<input type="hidden" name="price_12_monthes" id="form_price_12_monthes" value="<?= $arr_group_info["price_12_monthes"] ?>" />
				<input type="hidden" name="price_6_monthes_discounted" id="form_price_6_monthes_discounted" value="" />
				<input type="hidden" name="price_12_monthes_discounted" id="form_price_12_monthes_discounted" value="" />
<?php if (false) { ?>
<?php } ?>

				<div class="subscribe_type_wrapper">
					<p>На срок</p>
					<div class="row radio_wrapper">
						<div class="col-xs-6">
							<input type="radio" name="monthes" id="radio_monthes6" value="6" class="landing-radio" price="<?= $arr_group_info["price_6_monthes"] ?>" price_discounted="" monthes="6" />
							<label for="radio_monthes6">
								<span class="landing-radio-deco"></span>
								<span class="landing-radio-text">
									6 месяцев<br />
<?php if (false) { ?>
									(<?= 6*$arr_group_info["month_releases_num"] ?> номеров)<br />
<?php } ?>
								</span>
							</label>
						</div>
						<div class="col-xs-6">
							<input type="radio" name="monthes" id="radio_monthes12" value="12" class="landing-radio" checked="checked" price="<?= $arr_group_info["price_12_monthes"] ?>" price_discounted="" monthes="12" />
							<label for="radio_monthes12">
								<span class="landing-radio-deco"></span>
								<span class="landing-radio-text">
									12 месяцев<br />
<?php if (false) { ?>
									(<?= 12*$arr_group_info["month_releases_num"] ?> номеров)<br />
<?php } ?>
								</span>
							</label>
						</div>
					</div>
				</div>

				<div class="final_price_wrapper">
					<p>Итого к оплате:</p>
					<p class="price">
						<span id="price">
							<span class="sum"><?= $arr_group_info["price_12_monthes"] ?></span>
							<span class="icon-rouble-big">i</span>
						</span>
						<span id="price_discounted" class="hidden">
							<span class="sum"></span>
							<span class="icon-rouble-big">i</span>
						</span>
					</p>
					<p>Доставка включена в стоимость подписки</p>
				</div>
		
				<div class="form-wrapper">
					<p>Данные подписчика</p>
					<p class="comment">Заказ можно оформить только для физических лиц.</p>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<div class="checkbox_wrapper">
								<div><input type="checkbox" name="change_subscribe_date" id="change_subscribe_date" value="change_subscribe_date" class="landing-checkbox" /><label for="change_subscribe_date" class="noselect"><span class="landing-checkbox-deco"></span>Изменить дату начала подписки</label></div>
								<div class="form_dynamic_comment" id="change_subscribe_date-dynamic-comment"></div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="subscribe-date">Подписаться с: <span class="required">*</span></label>
						<div class="col-md-8" id="subscribe-date-wrapper">
							<select name="subscribe-date" id="subscribe-date" required="required" disabled="disabled">
								<?php echo generate_possible_dates_of_subscribe_for_select() ?>
							</select>
							<div class="form_dynamic_comment" id="subscribe-date-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="fio">ФИО <span class="required">*</span></label>
						<div class="col-md-8" id="fio-wrapper">
							<input type="text" id="fio" name="fio" class="form-control" value="" required="required" />
							<div class="form_comment">Например: Григорьев Алексей Владимирович</div>
							<div class="form_dynamic_comment" id="fio-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="email">E-mail <span class="required">*</span></label>
						<div class="col-md-8" id="email-wrapper">
							<input type="text" id="email" name="email" class="form-control" value="" required="required" />
							<div class="form_comment address">Например: e-mail@site.ru</div>
							<div class="form_dynamic_comment" id="email-dynamic-comment"></div>
						</div>
					</div>
					<input type="hidden" id="normalized_tel_phone" name="normalized_tel_phone" value="" />
					<input type="hidden" id="normalized_tel_type" name="normalized_tel_type" value="" />
					<input type="hidden" id="normalized_tel_country_code" name="normalized_tel_country_code" value="" />
					<input type="hidden" id="normalized_tel_city_code" name="normalized_tel_city_code" value="" />
					<input type="hidden" id="normalized_tel_number" name="normalized_tel_number" value="" />
					<input type="hidden" id="normalized_tel_extension" name="normalized_tel_extension" value="" />
					<input type="hidden" id="normalized_tel_provider" name="normalized_tel_provider" value="" />
					<input type="hidden" id="normalized_tel_region" name="normalized_tel_region" value="" />
					<input type="hidden" id="normalized_tel_timezone" name="normalized_tel_timezone" value="" />
					<input type="hidden" id="normalized_tel_qc_conflict" name="normalized_tel_qc_conflict" value="" />
					<input type="hidden" id="normalized_tel_qc" name="normalized_tel_qc" value="" />
					<div class="form-group">
						<label class="col-md-4" for="tel">Телефон <span class="required">*</span></label>
						<div class="col-md-8" id="tel-wrapper">
							<input type="text" id="tel" name="tel" class="form-control" value="" required="required" />
							<div class="form_comment">Например: +7 495 716-52-19</div>
							<div class="form_dynamic_comment" id="tel-dynamic-comment"></div>
						</div>
					</div>
					<!--div class="form-group">
						<label class="col-md-2" for="address">Адрес <span class="required">*</span></label>
						<div class="col-md-10" id="address-wrapper">
							<input type="text" id="address" name="address" class="form-control" value="121248, г Москва, Кутузовский пр-кт, д 1/7, кв 10" size="100" required2="required" addr_zip="" addr_city="" addr_street="" addr_house="" addr_flat="" />
							<div class="form_comment address">Например: 121248, г Москва, Кутузовский пр-кт, д 1/7, кв 10</div>
							<div class="form_dynamic_comment" id="address-dynamic-comment"></div>
						</div>
					</div-->
		
					<div class="form-group">
						<label class="col-md-4" for="region">Регион / район: <span class="required">*</span></label>
						<div class="col-md-8" id="region-wrapper">
							<input type="text" id="region" name="region" class="form-control" value="" required="required" />
							<div class="form_comment address">Например: Московская обл</div>
							<div class="form_dynamic_comment" id="region-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="city">Город / населенный пункт: <span class="required">*</span></label>
						<div class="col-md-8" id="city-wrapper">
							<input type="text" id="city" name="city" class="form-control" value="" required="required" />
							<div class="form_comment address">Например: г Москва</div>
							<div class="form_dynamic_comment" id="city-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="street">Улица: <span class="required">*</span></label>
						<div class="col-md-8" id="street-wrapper">
							<input type="text" id="street" name="street" class="form-control" value="" required="required" />
							<div class="form_comment address">Например: Комсомольский пр-кт</div>
							<div class="form_dynamic_comment" id="street-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="house">Дом: <span class="required">*</span></label>
						<div class="col-md-8" id="house-wrapper">
							<input type="text" id="house" name="house" class="form-control" value="" required="required" />
							<div class="form_comment address">Например: д 1</div>
							<div class="form_dynamic_comment" id="house-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="flat">Квартира:</label>
						<div class="col-md-8" id="flat-wrapper">
							<input type="text" id="flat" name="flat" class="form-control" value="" />
							<div class="form_comment">Например: кв 7</div>
							<div class="form_dynamic_comment" id="flat-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="zip">Почтовый индекс:</label>
						<div class="col-md-8" id="zip-wrapper">
							<input type="text" id="zip" name="zip" class="form-control" value="" required="required" />
							<div class="form_comment">Например: 630096</div>
							<div class="form_dynamic_comment" id="zip-dynamic-comment"></div>
						</div>
					</div>
		
					<div class="form-group">
						<label class="col-md-4" for="tel">Комментарий к заказу</label>
						<div class="col-md-8">
							<textarea name="comment" class="form-control" placeholder=""></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<div class="checkbox_wrapper">
								<div><input type="checkbox" name="agree" id="agree" value="agree" class="landing-checkbox" /><label for="agree" class="noselect"><span class="landing-checkbox-deco"></span></label>Подтверждаю, что ознакомился с <a href="/public-offer/" target="_blank">Договором оферты</a> и <a href="#">Политикой обработки персональных данных</a> и принимаю их.</div>
								<div class="form_dynamic_comment" id="agree-dynamic-comment"></div>
							</div>
						</div>
					</div>
					<div class="submit_wrapper">
						<button class="button button-3d" type="submit" value="submit">Оплатить</button>
					</div>
				</div>
			</form>

			<div class="bank_info">
				Для оплаты (ввода реквизитов Вашей карты) Вы будете перенаправлены на платежный шлюз ПАО СБЕРБАНК. Соединение с платежным шлюзом и передача информации осуществляется в защищенном режиме с использованием протокола шифрования SSL. В случае если Ваш банк поддерживает технологию безопасного проведения интернет-платежей Verified By Visa или MasterCard SecureCode для проведения платежа также может потребоваться ввод специального пароля.<br />
				Настоящий сайт поддерживает 256-битное шифрование. Конфиденциальность сообщаемой персональной информации обеспечивается ПАО СБЕРБАНК. Введенная информация не будет предоставлена третьим лицам за исключением случаев, предусмотренных законодательством РФ. Проведение платежей по банковским картам осуществляется в строгом соответствии с требованиями платежных систем МИР, Visa Int. и MasterCard Europe Sprl.
			</div>

		</div>
	</div>
</div>
<div class="bottom_line_subscribe">
	Подписаться на год - <?= $arr_group_info["price_12_monthes"] ?>  руб.<!--span class="icon-rouble-big">i</span-->
</div>
<script>
$(document).ready(function(){
	var allVars = $.getUrlVars();	
	var m = $.getUrlVar('m');
	if (m == '6') {
		$("#radio_monthes6").trigger('click');
	}
	var promo = $.getUrlVar('promo');
	if (promo != undefined && promo != '') {
		$("#promocode").val(promo);
		$("#discount_button").trigger('click');
	}
});
</script>