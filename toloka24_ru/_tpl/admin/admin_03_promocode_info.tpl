<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
				<?php if ($post_success_info && $error_description == '') echo "<p class=\"post_success_info\">$post_success_info</p>"; ?>
				<?php if ($error_description ) echo "<p class=\"error_description\">$error_description</p>"; ?>
				<?php if (isset($flagNewPromocode)) { ?>
					<h1>Добавить новый промокод</h1>
				<?php } else { ?>
					<h1>Редактировать промокод</h1>
				<?php } ?>
				<?php if (isset($flagNewPromocode) || count($arr_promocode_info)) { ?>
					<form id="form_change_promocode" action="/admin/" method="post">
						<?php if (isset($flagNewPromocode)) { ?>
							<input type="hidden" name="act" value="new_promocode" />
						<?php } else { ?>
							<input type="hidden" name="act" value="edit_promocode" />
						<?php } ?>
						<input type="hidden" name="id" value="<?= $arr_promocode_info["id"] ?>" />
						<div class="form-group">
							<label class="col-sm-3" for="code_text">Код<span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="code_text" name="code_text" class="form-control" value="<?php if (isset($arr_promocode_info["code_text"]) ) echo $arr_promocode_info["code_text"]; ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="discount_percent">Скидка<span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="discount_percent" name="discount_percent" class="form-control" value="<?php if (isset($arr_promocode_info["discount_percent"]) ) echo $arr_promocode_info["discount_percent"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="code_name">Подробное описание кода</label>
							<div class="col-sm-9">
								<input type="text" id="code_name" name="code_name" class="form-control" value="<?php if (isset($arr_promocode_info["code_name"]) ) echo $arr_promocode_info["code_name"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="datetime_start">Дата начала<span class="required">*</span></label>
							<div class="col-sm-9">
								<!--input type="text" id="datetime_start" name="datetime_start" class="form-control" value="<?php if (isset($arr_promocode_info["datetime_start"]) ) echo $arr_promocode_info["datetime_start"] ?>" required="required" placeholder="формат даты 2018-01-01 00:00:00" /-->
								<input type="date" id="datetime_start" data-date="" data-date-format="YYYY-MMMM-DD" name="datetime_start" class="form-control" value="<?php if (isset($arr_promocode_info["datetime_start"]) ) echo substr($arr_promocode_info["datetime_start"], 0, strpos($arr_promocode_info["datetime_start"], ' ')) ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="datetime_end">Дата окончания<span class="required">*</span></label>
							<div class="col-sm-9">
								<!--input type="text" id="datetime_end" name="datetime_end" class="form-control" value="<?php if (isset($arr_promocode_info["datetime_end"]) ) echo $arr_promocode_info["datetime_end"] ?>" required="required" placeholder="формат даты 2018-12-31 24:59:59" /-->
								<input type="date" id="datetime_end" data-date="" data-date-format="YYYY-MMMM-DD" name="datetime_end" class="form-control" value="<?php if (isset($arr_promocode_info["datetime_end"]) ) echo substr($arr_promocode_info["datetime_end"], 0, strpos($arr_promocode_info["datetime_end"], ' ')) ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="datetime_end">К журналам<span class="required">*</span></label>
							<div class="col-sm-9 radio_wrapper">
								<input type="radio" name="radio_promocode_type" id="radio_promocode_type1" value="0" <?php if (!isset($flagNewPromocode) && !$arr_promocode_info['for_all_magazines']) echo 'checked="checked"'; ?> />
								<label for="radio_promocode_type1"> Выборочно</label>
								<input type="radio" name="radio_promocode_type" id="radio_promocode_type2" value="1" <?php if (isset($flagNewPromocode) || $arr_promocode_info['for_all_magazines']) echo 'checked="checked"'; ?> />
								<label for="radio_promocode_type2">Ко всем</label>
								<div class="checkbox_wrapper nowrap <?php if (isset($flagNewPromocode) || $arr_promocode_info['for_all_magazines']) echo ' hidden'; ?>" id="radio_wrapper_for_all_magazines_checkboxes">
									<?php foreach ($arr_promocode_info["magazines"] as $item) { ?>
										<div><label><input type="checkbox" name="magazines[]" value="<?= $item['magazine_id'] ?>"<?php if ($item['magazine_checked']) echo ' checked=""'; ?> /><?= $item['magazine_name'] ?></label></div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3"></div>
							<div class="col-sm-9">
								<button type="submit" class="button">Сохранить</button>
								<?php if (!isset($flagNewPromocode)) { ?>
									<button type="button" class="button red right" id="delete_promocode_button" promocode_code_text="<?= $arr_promocode_info["code_text"] ?>"><span class="close_icon"></span> Удалить промокод</button>
								<?php } ?>
							</div>
						</div>
					</form>
				<?php } else { ?>
					Промокод не найден!
				<?php } ?>
			</div>		
		</div>
	</div>
</div>
<div class="popup_wrapper hidden_my" id="delete_promocode_popup">
	<div class="popup_overlay"></div>
	<div class="popup_confirm">
		<div class="close_icon"></div>
		<p class="question">Вы уверены, что хотите удалить промокод '<b id="delete_promocode_popup_code_text"></b>'?</p>
		<div class="buttons_wrapper">
			<div class="button yes" promocode_id="<?= $arr_promocode_info["id"] ?>">Да</div>
			<div class="button red no">Нет</div>
		</div>
	</div>
</div>