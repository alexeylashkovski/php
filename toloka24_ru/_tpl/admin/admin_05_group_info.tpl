<!--script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script-->
<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
				<?php if ($post_success_info && $error_description == '') echo "<p class=\"post_success_info\">$post_success_info</p>"; ?>
				<?php if ($error_description ) echo "<p class=\"error_description\">$error_description</p>"; ?>
				<?php if (isset($flagNewGroup)) { ?>
					<h1>Добавить новый комплект</h1>
				<?php } else { ?>
					<h1>Редактировать комплект</h1>
				<?php } ?>
				<?php if (isset($flagNewGroup) || count($arr_group_info)) { ?>
					<form id="form_change_group" action="" method="post" enctype="multipart/form-data">
						<?php if (isset($flagNewGroup)) { ?>
							<input type="hidden" name="act" value="new_group" />
						<?php } else { ?>
							<input type="hidden" name="act" value="edit_group" />
						<?php } ?>
						<input type="hidden" name="id" value="<?= $arr_group_info["id"] ?>" />
						<div class="form-group">
							<label class="col-sm-3" for="name">Название комплекта <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="name" name="name" class="form-control" value="<?php if (isset($arr_group_info["name"])) echo $arr_group_info["name"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="url">Название URL <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="url" name="url" class="form-control" value="<?php if (isset($arr_group_info["url"])) echo $arr_group_info["url"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_keywords">Обложка <span class="required">*</span></label>
							<div class="col-sm-9">
								<div class="cover_wrapper">
									<?php if (isset($arr_group_info["id"]) && check_group_cover_exist($arr_group_info["id"])) { ?>
										<a href="/_img/group/<?= $arr_group_info["id"] ?>/cover.jpg" target="_blank">
											<img src="/_img/group/<?= $arr_group_info["id"] ?>/cover.jpg?rnd=<?php echo rand(1000000, 9999999); ?>" />
										</a>
									<?php } ?>
								</div>
								<input type="file" name="picture" accept="image/*" id="picture" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="subtitle">Слоган</label>
							<div class="col-sm-9">
								<input type="text" id="subtitle" name="subtitle" class="form-control" value="<?php if (isset($arr_group_info["subtitle"])) echo $arr_group_info["subtitle"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="description">Описание <span class="required">*</span></label>
							<div class="col-sm-9">
								<textarea id="description" name="description" class="form-control" /><?php if (isset($arr_group_info["description"])) echo $arr_group_info["description"] ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3">Журналы <span class="required">*</span></label>
							<div class="col-sm-9">
								<div class="checkbox_wrapper nowrap">
									<table cellspacing="0" cellpadding="0" border="0" class="admin_promocodes_table">
										<tr>
											<th>Название</th>
											<th>Цена (6 месяцев)</th>
											<th>Цена (12 месяцев)</th>
											<th>Цена (6 месяцев, в комлекте)</th>
											<th>Цена (12 месяцев, в комлекте)</th>
										</tr>
									<?php foreach ($arr_group_info["magazines"] as $item) { ?>
										<tr <?php if (isset($item['magazine_group_id'])) echo "class=\"selected\"" ?>>
											<td>
											<div><label><input type="checkbox" name="magazines[]" value="<?= $item['id'] ?>"<?php $flagChecked = false; if (isset($item['magazine_group_id'])) {echo ' checked=""'; $flagChecked = true;} ?> /><?= $item['name'] ?></label></div>
											</td>
											<td>
												<?= $item['price_6_monthes'] ?>
											</td>
											<td>
												<?= $item['price_12_monthes'] ?>
											</td>
											<td>
												<input type="text" name="group_price_6_monthes[]" class="form-control" value="<?php if ($item["group_price_6_monthes"]) echo $item["group_price_6_monthes"] ?>" />
											</td>
											<td>
												<input type="text" name="group_price_12_monthes[]" class="form-control" value="<?php if ($item["group_price_12_monthes"]) echo $item["group_price_12_monthes"] ?>" />
											</td>
										</tr>
									<?php } ?>
										<tr class="results">
											<td>Итого</td>
											<td><?= $arr_group_info["sum_6_monthes"] ?> р</td>
											<td><?= $arr_group_info["sum_12_monthes"] ?> р</td>
											<td><?= $arr_group_info["group_sum_6_monthes"] ?> р <?php if ($arr_group_info["discount_group_sum_6_monthes"]) echo " <span class=\"discount_group\"> (-" . $arr_group_info["discount_group_sum_6_monthes"] . '%)</span>'; ?></td>
											<td><?= $arr_group_info["group_sum_12_monthes"] ?> р <?php if ($arr_group_info["discount_group_sum_12_monthes"]) echo " <span class=\"discount_group\"> (-" . $arr_group_info["discount_group_sum_12_monthes"] . '%)</span>'; ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<!--div class="form-group">
							<label class="col-sm-3" for="price_12_monthes">Категории <span class="required">*</span></label>
							<div class="col-sm-9">
								<div class="checkbox_wrapper">
									<?php foreach ($arr_magazine_info["categories"] as $item) { ?>
											<div><label><input type="checkbox" name="categories[]" value="<?= $item['id'] ?>"<?php if (isset($item['category_id'])) echo ' checked=""'; ?> /><?= $item['name'] ?></label></div>
									<?php } ?>
								</div>
							</div>
						</div-->


						<div class="form-group">
							<label class="col-sm-3" for="html_title">Заголовок страницы</label>
							<div class="col-sm-9">
								<input type="text" id="html_title" name="html_title" class="form-control" value="<?php if (isset($arr_group_info["html_title"])) echo htmlspecialchars($arr_group_info["html_title"]) ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_description">meta description</label>
							<div class="col-sm-9">
								<input type="text" id="meta_description" name="meta_description" class="form-control" value="<?php if (isset($arr_group_info["meta_description"])) echo htmlspecialchars($arr_group_info["meta_description"]) ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_keywords">meta keywords</label>
							<div class="col-sm-9">
								<input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="<?php if (isset($arr_group_info["meta_keywords"])) echo $arr_group_info["meta_keywords"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="list_order_value">Весовой коэффициент в списке<br /> (чем больше, тем первее)</label>
							<div class="col-sm-9">
								<input type="text" id="list_order_value" name="list_order_value" class="form-control" value="<?php if (isset($arr_group_info["list_order_value"])) echo $arr_group_info["list_order_value"]; else echo "0"; ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3"></div>
							<div class="col-sm-9">
								<button type="submit" class="button">Сохранить</button>
								<?php if (!isset($flagNewGroup)) { ?>
									<button type="button" class="button red right" id="delete_group_button"><span class="close_icon"></span> Удалить комплект</button>
								<?php } ?>
							</div>
						</div>
					</form>
				<?php } else { ?>
					Комплект не найден!
				<?php } ?>
			</div>		
		</div>
	</div>
</div>
<div class="popup_wrapper hidden_my" id="delete_group_popup">
	<div class="popup_overlay"></div>
	<div class="popup_confirm">
		<div class="close_icon"></div>
		<p class="question">Вы уверены, что хотите удалить этот комплект?</p>
		<div class="buttons_wrapper">
			<div class="button yes" group_id="<?= $arr_group_info["id"] ?>">Да</div>
			<div class="button red no">Нет</div>
		</div>
	</div>
</div>