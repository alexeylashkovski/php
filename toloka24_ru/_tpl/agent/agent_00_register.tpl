<div class="content magazine_wrapper">
	<div class="container">
		<?php require "../_tpl/agent/breadcrumb.tpl"; ?>
		<h1>Регистрация агента</h1>
		<div class="under_wrapper">
			<form id="form_podpiska" action="/agent/" method="post">
				<input type="hidden" name="act" value="new_agent" />
				<div class="form-wrapper">
					<div class="form-group">
						<label class="col-md-4"></label>
						<div class="col-md-8">
							<?php
								if ($error_description != "") { ?>
								<div class="error_description_reg">
									<ul>
										<?= $error_description ?>
									</ul>
								</div>
							<?php } ?>
						</div>
					</div>
					<!--div class="form-group">
						<label class="col-md-4" for="login">Логин <span class="required">*</span></label>
						<div class="col-md-8" id="login-wrapper">
							<input type="text" id="login" name="login" class="form-control" value="<?php echo filter_input(INPUT_POST, 'login') ?>" required2="required" placeholder="Введите ваш логин" />
							<div class="form_dynamic_comment" id="login-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="pass">Пароль <span class="required">*</span></label>
						<div class="col-md-8" id="pass-wrapper">
							<input type="password" id="pass" name="pass" class="form-control" value="<?php echo filter_input(INPUT_POST, 'pass') ?>" required2="required" placeholder="Введите ваш пароль" />
							<div class="form_dynamic_comment" id="pass-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="pass2">Проверка пароля <span class="required">*</span></label>
						<div class="col-md-8" id="pass2-wrapper">
							<input type="password" id="pass2" name="pass2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'pass2') ?>" required2="required" placeholder="Введите ваш пароль повторно" />
							<div class="form_dynamic_comment" id="pass2-dynamic-comment"></div>
						</div>
					</div-->


					<!--div class="form_divider"></div-->
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<h2>Паспортные данные</h2>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="name_last">Фамилия <span class="required">*</span></label>
						<div class="col-md-8" id="name_last-wrapper">
							<input type="text" id="name_last" name="name_last" class="form-control" value="<?php echo filter_input(INPUT_POST, 'name_last') ?>" required2="required" placeholder="Введите вашу фамилию" />
							<div class="form_dynamic_comment" id="name_last-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="name_first">Имя <span class="required">*</span></label>
						<div class="col-md-8" id="name_first-wrapper">
							<input type="text" id="name_first" name="name_first" class="form-control" value="<?php echo filter_input(INPUT_POST, 'name_first') ?>" required2="required" placeholder="Введите ваше имя" />
							<div class="form_dynamic_comment" id="name_first-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="name_middle">Отчество <span class="required">*</span></label>
						<div class="col-md-8" id="name_middle-wrapper">
							<input type="text" id="name_middle" name="name_middle" class="form-control" value="<?php echo filter_input(INPUT_POST, 'name_middle') ?>" required2="required" placeholder="Введите ваше отчество" />
							<div class="form_dynamic_comment" id="name_middle-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="passport_series">Серия паспорта <span class="required">*</span></label>
						<div class="col-md-8" id="passport_series-wrapper">
							<input type="text" id="passport_series" name="passport_series" class="form-control" value="<?php echo filter_input(INPUT_POST, 'passport_series') ?>" required2="required" placeholder="" maxlength="4" />
							<div class="form_dynamic_comment" id="passport_series-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="passport_num">Номер паспорта <span class="required">*</span></label>
						<div class="col-md-8" id="passport_num-wrapper">
							<input type="text" id="passport_num" name="passport_num" class="form-control" value="<?php echo filter_input(INPUT_POST, 'passport_num') ?>" required2="required" placeholder="" maxlength="6" />
							<div class="form_dynamic_comment" id="passport_num-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="passport_issue_who">Кем выдан паспорт (название органа выдавшего паспорт) <span class="required">*</span></label>
						<div class="col-md-8" id="passport_issue_who-wrapper">
							<input type="text" id="passport_issue_who" name="passport_issue_who" class="form-control" value="<?php echo filter_input(INPUT_POST, 'passport_issue_who') ?>" required2="required" placeholder="" />
							<div class="form_dynamic_comment" id="passport_issue_who-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="passport_issue_code">Кем выдан паспорт (код подразделения) <span class="required">*</span></label>
						<div class="col-md-8" id="passport_issue_code-wrapper">
							<input type="text" id="passport_issue_code" name="passport_issue_code" class="form-control" value="<?php echo filter_input(INPUT_POST, 'passport_issue_code') ?>" required2="required" placeholder="ХХХ-ХХХ" />
							<div class="form_dynamic_comment" id="passport_issue_code-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="passport_issue_date">Кем выдан паспорт (дата) <span class="required">*</span></label>
						<div class="col-md-8" id="passport_issue_date-wrapper">
							<input type="date" id="passport_issue_date" data-date="" data-date-format="YYYY-MMMM-DD" name="passport_issue_date" class="form-control" value="<?php echo filter_input(INPUT_POST, 'passport_issue_date') ?>" required2="required" />
							<div class="form_dynamic_comment" id="passport_issue_date-dynamic-comment"></div>
						</div>
					</div>


					<div class="form_divider"></div>
					<div class="form-group">
						<label class="col-md-4" for="inn">ИНН <span class="required">*</span></label>
						<div class="col-md-8" id="inn-wrapper">
							<input type="text" id="inn" name="inn" class="form-control" value="<?php echo filter_input(INPUT_POST, 'inn') ?>" required2="required" placeholder="" maxlength="12" />
							<div class="form_dynamic_comment" id="inn-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="snils">СНИЛС <span class="required">*</span></label>
						<div class="col-md-8" id="snils-wrapper">
							<input type="text" id="snils" name="snils" class="form-control" value="<?php echo filter_input(INPUT_POST, 'snils') ?>" required2="required" placeholder="ХХХ-ХХХ-ХХХ ХХ" maxlength="14" />
							<div class="form_dynamic_comment" id="snils-dynamic-comment"></div>
						</div>
					</div>


					<div class="form_divider"></div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<h2>Адрес регистрации</h2>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="region">Регион / район: <span class="required">*</span></label>
						<div class="col-md-8" id="region-wrapper">
							<input type="text" id="region" name="region" class="form-control" value="<?php echo filter_input(INPUT_POST, 'region') ?>" required2="required" />
							<div class="form_comment address">Например: Московская обл</div>
							<div class="form_dynamic_comment" id="region-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="city">Город / населенный пункт: <span class="required">*</span></label>
						<div class="col-md-8" id="city-wrapper">
							<input type="text" id="city" name="city" class="form-control" value="<?php echo filter_input(INPUT_POST, 'city') ?>" required2="required" />
							<div class="form_comment address">Например: г Москва</div>
							<div class="form_dynamic_comment" id="city-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="street">Улица: <span class="required">*</span></label>
						<div class="col-md-8" id="street-wrapper">
							<input type="text" id="street" name="street" class="form-control" value="<?php echo filter_input(INPUT_POST, 'street') ?>" required2="required" />
							<div class="form_comment address">Например: Комсомольский пр-кт</div>
							<div class="form_dynamic_comment" id="street-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="house">Дом: <span class="required">*</span></label>
						<div class="col-md-8" id="house-wrapper">
							<input type="text" id="house" name="house" class="form-control" value="<?php echo filter_input(INPUT_POST, 'house') ?>" required2="required" />
							<div class="form_comment address">Например: д 1</div>
							<div class="form_dynamic_comment" id="house-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="flat">Квартира:</label>
						<div class="col-md-8" id="flat-wrapper">
							<input type="text" id="flat" name="flat" class="form-control" value="<?php echo filter_input(INPUT_POST, 'flat') ?>" />
							<div class="form_comment">Например: кв 7</div>
							<div class="form_dynamic_comment" id="flat-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="zip">Почтовый индекс: <span class="required">*</span></label>
						<div class="col-md-8" id="zip-wrapper">
							<input type="text" id="zip" name="zip" class="form-control" value="<?php echo filter_input(INPUT_POST, 'zip') ?>" required2="required" />
							<div class="form_comment">Например: 630096</div>
							<div class="form_dynamic_comment" id="zip-dynamic-comment"></div>
						</div>
					</div>		

					<div class="form_divider"></div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<div class="checkbox_wrapper">
								<div><input type="checkbox" name="address2" id="address2" value="address2" class="landing-checkbox" <?php if(filter_input(INPUT_POST, 'address2')) echo "checked" ?> /><label for="address2" class="noselect"><span class="landing-checkbox-deco"></span></label>Нажмите, если ваш адрес проживания отличается от адреса регистрации</div>
								<div class="form_dynamic_comment" id="address2-dynamic-comment"></div>
							</div>
						</div>
					</div>
					<div id="form_hidden_address2" class="<?php if(!filter_input(INPUT_POST, 'address2')) echo "hidden_my" ?>">
						<div class="form-group">
							<div class="col-md-4"></div>
							<div class="col-md-8">
								<h2>Адрес проживания</h2>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="region2">Регион / район: <span class="required">*</span></label>
							<div class="col-md-8" id="region2-wrapper">
								<input type="text" id="region2" name="region2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'region2') ?>" required2="required" />
								<div class="form_comment address">Например: Московская обл</div>
								<div class="form_dynamic_comment" id="region2-dynamic-comment"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="city2">Город / населенный пункт: <span class="required">*</span></label>
							<div class="col-md-8" id="city2-wrapper">
								<input type="text" id="city2" name="city2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'city2') ?>" required2="required" />
								<div class="form_comment address">Например: г Москва</div>
								<div class="form_dynamic_comment" id="city2-dynamic-comment"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="street2">Улица: <span class="required">*</span></label>
							<div class="col-md-8" id="street2-wrapper">
								<input type="text" id="street2" name="street2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'street2') ?>" required2="required" />
								<div class="form_comment address">Например: Комсомольский пр-кт</div>
								<div class="form_dynamic_comment" id="street2-dynamic-comment"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="house2">Дом: <span class="required">*</span></label>
							<div class="col-md-8" id="house2-wrapper">
								<input type="text" id="house2" name="house2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'house2') ?>" required2="required" />
								<div class="form_comment address">Например: д 1</div>
								<div class="form_dynamic_comment" id="house2-dynamic-comment"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="flat2">Квартира:</label>
							<div class="col-md-8" id="flat2-wrapper">
								<input type="text" id="flat2" name="flat2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'flat2') ?>" />
								<div class="form_comment">Например: кв 7</div>
								<div class="form_dynamic_comment" id="flat2-dynamic-comment"></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4" for="zip2">Почтовый индекс: <span class="required">*</span></label>
							<div class="col-md-8" id="zip2-wrapper">
								<input type="text" id="zip2" name="zip2" class="form-control" value="<?php echo filter_input(INPUT_POST, 'zip2') ?>" required2="required" />
								<div class="form_comment">Например: 630096</div>
								<div class="form_dynamic_comment" id="zip2-dynamic-comment"></div>
							</div>
						</div>
					</div>
					<div class="form_divider"></div>
					
					
					<div class="form_divider"></div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<h2>Банковские реквизиты</h2>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="account_num_rasschetni">Расчетный счет: <span class="required">*</span></label>
						<div class="col-md-8" id="account_num_rasschetni-wrapper">
							<input type="text" id="account_num_rasschetni" name="account_num_rasschetni" class="form-control" value="<?php echo filter_input(INPUT_POST, 'account_num_rasschetni') ?>" required2="required" maxlength="20" />
							<div class="form_dynamic_comment" id="account_num_rasschetni-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="bank_name">Название банка: <span class="required">*</span></label>
						<div class="col-md-8" id="bank_name-wrapper">
							<input type="text" id="bank_name" name="bank_name" class="form-control" value="<?php echo filter_input(INPUT_POST, 'bank_name') ?>" required2="required" />
							<div class="form_dynamic_comment" id="bank_name-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="account_num_correspondent">Корреспондентский счёт: <span class="required">*</span></label>
						<div class="col-md-8" id="account_num_correspondent-wrapper">
							<input type="text" id="account_num_correspondent" name="account_num_correspondent" class="form-control" value="<?php echo filter_input(INPUT_POST, 'account_num_correspondent') ?>" required2="required" maxlength="20" />
							<div class="form_dynamic_comment" id="account_num_correspondent-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="bik">БИК: <span class="required">*</span></label>
						<div class="col-md-8" id="bik-wrapper">
							<input type="text" id="bik" name="bik" class="form-control" value="<?php echo filter_input(INPUT_POST, 'bik') ?>" required2="required" maxlength="9" />
							<div class="form_dynamic_comment" id="bik-dynamic-comment"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-4" for="credit_card_num">Номер кредитной карты:</label>
						<div class="col-md-8" id="credit_card_num-wrapper">
							<input type="text" id="credit_card_num" name="credit_card_num" class="form-control" value="<?php echo filter_input(INPUT_POST, 'credit_card_num') ?>" />
							<div class="form_dynamic_comment" id="credit_card_num-dynamic-comment"></div>
						</div>
					</div>


					<div class="form_divider"></div>
					<div class="form-group">
						<label class="col-md-4" for="job">Место работы</label>
						<div class="col-md-8" id="job-wrapper">
							<input type="text" id="job" name="job" class="form-control" value="<?php echo filter_input(INPUT_POST, 'job') ?>" required2="required" placeholder="Введите ваше место работы" />
							<div class="form_dynamic_comment" id="job-dynamic-comment"></div>
						</div>
					</div>					
					<div class="form-group">
						<label class="col-md-4" for="job_position">Должность</label>
						<div class="col-md-8" id="job_position-wrapper">
							<input type="text" id="job_position" name="job_position" class="form-control" value="<?php echo filter_input(INPUT_POST, 'job_position') ?>" required2="required" placeholder="Введите вашу должность" />
							<div class="form_dynamic_comment" id="job_position-dynamic-comment"></div>
						</div>
					</div>					
					<div class="form-group">
						<label class="col-md-4" for="email">E-mail <span class="required">*</span></label>
						<div class="col-md-8" id="email-wrapper">
							<input type="text" id="email" name="email" class="form-control" value="<?php echo filter_input(INPUT_POST, 'email') ?>" required2="required" placeholder="Например: e-mail@site.ru" />
							<div class="form_comment address">Например: e-mail@site.ru</div>
							<div class="form_dynamic_comment" id="email-dynamic-comment"></div>
						</div>
					</div>
					<input type="hidden" id="normalized_tel_phone" name="normalized_tel_phone" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_phone') ?>" />
					<input type="hidden" id="normalized_tel_type" name="normalized_tel_type" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_type') ?>" />
					<input type="hidden" id="normalized_tel_country_code" name="normalized_tel_country_code" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_country_code') ?>" />
					<input type="hidden" id="normalized_tel_city_code" name="normalized_tel_city_code" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_city_code') ?>" />
					<input type="hidden" id="normalized_tel_number" name="normalized_tel_number" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_number') ?>" />
					<input type="hidden" id="normalized_tel_extension" name="normalized_tel_extension" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_extension') ?>" />
					<input type="hidden" id="normalized_tel_provider" name="normalized_tel_provider" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_provider') ?>" />
					<input type="hidden" id="normalized_tel_region" name="normalized_tel_region" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_region') ?>" />
					<input type="hidden" id="normalized_tel_timezone" name="normalized_tel_timezone" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_timezone') ?>" />
					<input type="hidden" id="normalized_tel_qc_conflict" name="normalized_tel_qc_conflict" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_qc_conflict') ?>" />
					<input type="hidden" id="normalized_tel_qc" name="normalized_tel_qc" value="<?php echo filter_input(INPUT_POST, 'normalized_tel_qc') ?>" />

					<input type="hidden" name="normalized_region" id="normalized_region" value="<?php echo filter_input(INPUT_POST, 'normalized_region') ?>" />
					<input type="hidden" name="normalized_district" id="normalized_district" value="<?php echo filter_input(INPUT_POST, 'normalized_district') ?>" />
					<input type="hidden" name="normalized_city" id="normalized_city" value="<?php echo filter_input(INPUT_POST, 'normalized_city') ?>" />
					<input type="hidden" name="normalized_street" id="normalized_street" value="<?php echo filter_input(INPUT_POST, 'normalized_street') ?>" />
					<input type="hidden" name="normalized_house" id="normalized_house" value="<?php echo filter_input(INPUT_POST, 'normalized_house') ?>" />
					<input type="hidden" name="normalized_flat" id="normalized_flat" value="<?php echo filter_input(INPUT_POST, 'normalized_flat') ?>" />

					<input type="hidden" name="normalized_city_type" id="normalized_city_type" value="<?php echo filter_input(INPUT_POST, 'normalized_city_type') ?>" />
					<input type="hidden" name="normalized_street_type" id="normalized_street_type" value="<?php echo filter_input(INPUT_POST, 'normalized_street_type') ?>" />

					<input type="hidden" name="normalized_street_kladr_id" id="normalized_street_kladr_id" value="<?php echo filter_input(INPUT_POST, 'normalized_street_kladr_id') ?>" />

					<div class="form-group">
						<label class="col-md-4" for="tel">Телефон <span class="required">*</span></label>
						<div class="col-md-8" id="tel-wrapper">
							<input type="text" id="tel" name="tel" class="form-control" value="<?php echo filter_input(INPUT_POST, 'tel') ?>" required2="required" placeholder="Например: +7 495 716-52-19" />
							<div class="form_comment">Например: +7 495 716-52-19</div>
							<div class="form_dynamic_comment" id="tel-dynamic-comment"></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-4" for="comment">Комментарий</label>
						<div class="col-md-8">
							<textarea name="comment" class="form-control" placeholder=""><?php echo filter_input(INPUT_POST, 'comment') ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<div class="checkbox_wrapper">
								<div><input type="checkbox" name="agree" id="agree" value="agree" class="landing-checkbox" <?php if(filter_input(INPUT_POST, 'agree')) echo "checked" ?> /><label for="agree" class="noselect"><span class="landing-checkbox-deco"></span></label>Подтверждаю, что ознакомился с <a href="/public-offer/" target="_blank">Договором оферты</a> и <a href="#">Политикой обработки персональных данных</a> и принимаю их.</div>
								<div class="form_dynamic_comment" id="agree-dynamic-comment"></div>
							</div>
						</div>
					</div>
					<div class="submit_wrapper">
						<button class="button button-3d" type="submit" value="submit" id="submit_button">Зарегистрироваться</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
