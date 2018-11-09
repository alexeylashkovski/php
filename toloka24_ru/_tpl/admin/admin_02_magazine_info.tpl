<!--script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script-->
<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
				<?php if ($post_success_info && $error_description == '') echo "<p class=\"post_success_info\">$post_success_info</p>"; ?>
				<?php if ($error_description ) echo "<p class=\"error_description\">$error_description</p>"; ?>
				<?php if (isset($flagNewMagazine)) { ?>
					<h1>Добавить новый журнал</h1>
				<?php } else { ?>
					<h1>Редактировать журнал</h1>
				<?php } ?>
				<?php if (isset($flagNewMagazine) || count($arr_magazine_info)) { ?>
					<form id="form_change_magazine" action="" method="post" enctype="multipart/form-data">
						<?php if (isset($flagNewMagazine)) { ?>
							<input type="hidden" name="act" value="new_magazine" />
						<?php } else { ?>
							<input type="hidden" name="act" value="edit_magazine" />
						<?php } ?>
						<input type="hidden" name="id" value="<?= $arr_magazine_info["id"] ?>" />
						<div class="form-group">
							<label class="col-sm-3" for="name">Название журнала <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="name" name="name" class="form-control" value="<?php if (isset($arr_magazine_info["name"])) echo $arr_magazine_info["name"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="url">Название URL <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="url" name="url" class="form-control" value="<?php if (isset($arr_magazine_info["url"])) echo $arr_magazine_info["url"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="url">Подписной редакционный индекс <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="post_index" name="post_index" class="form-control" value="<?php if (isset($arr_magazine_info["post_index"])) echo $arr_magazine_info["post_index"] ?>" required="required" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_keywords">Обложка <span class="required">*</span></label>
							<div class="col-sm-9">
								<div class="cover_wrapper">
									<?php if (isset($arr_magazine_info["id"]) && check_magazine_cover_exist($arr_magazine_info["id"])) { ?>
										<a href="/_img/magazine/<?= $arr_magazine_info["id"] ?>/cover.jpg" target="_blank">
											<img src="/_img/magazine/<?= $arr_magazine_info["id"] ?>/cover.jpg?rnd=<?php echo rand(1000000, 9999999); ?>" />
										</a>
									<?php } ?>
								</div>
								<input type="file" name="picture" accept="image/*" id="picture" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="subtitle">Слоган</label>
							<div class="col-sm-9">
								<input type="text" id="subtitle" name="subtitle" class="form-control" value="<?php if (isset($arr_magazine_info["subtitle"])) echo $arr_magazine_info["subtitle"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="description">Описание <span class="required">*</span></label>
							<div class="col-sm-9">
								<textarea id="description" name="description" class="form-control" /><?php if (isset($arr_magazine_info["description"])) echo $arr_magazine_info["description"] ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="month_releases_num">Количество номеров в месяц <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="month_releases_num" name="month_releases_num" class="form-control" value="<?php if (isset($arr_magazine_info["month_releases_num"])) echo $arr_magazine_info["month_releases_num"]; else echo "1"; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="page_size">Размер листа журнала <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="page_size" name="page_size" class="form-control" value="<?php if (isset($arr_magazine_info["page_size"])) echo $arr_magazine_info["page_size"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="price_6_monthes">Цена подписки за 6 месяцев <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="price_6_monthes" name="price_6_monthes" class="form-control" value="<?php if (isset($arr_magazine_info["price_6_monthes"])) echo $arr_magazine_info["price_6_monthes"]; else echo "0"; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="price_12_monthes">Цена подписки за 12 месяцев <span class="required">*</span></label>
							<div class="col-sm-9">
								<input type="text" id="price_12_monthes" name="price_12_monthes" class="form-control" value="<?php if (isset($arr_magazine_info["price_12_monthes"])) echo $arr_magazine_info["price_12_monthes"]; else echo "0"; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="stavka_nds">Ставка НДС</label>
							<div class="col-sm-9">
								<!--input type="text" id="stavka_nds" name="stavka_nds" class="form-control" value="<?php if (isset($arr_magazine_info["stavka_nds"])) echo $arr_magazine_info["stavka_nds"]; else echo "0"; ?>" /-->
								<input type="text" id="stavka_nds" name="stavka_nds" class="form-control" value="<?php if (isset($arr_magazine_info["stavka_nds"])) echo $arr_magazine_info["stavka_nds"]; else echo "0"; ?>" disabled />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="price_12_monthes">Категории <span class="required">*</span></label>
							<div class="col-sm-9">
								<div class="checkbox_wrapper">
									<?php foreach ($arr_magazine_info["categories"] as $item) { ?>
											<div><label><input type="checkbox" name="categories[]" value="<?= $item['id'] ?>"<?php if (isset($item['category_id'])) echo ' checked=""'; ?> /><?= $item['name'] ?></label></div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="html_title">Заголовок страницы</label>
							<div class="col-sm-9">
								<input type="text" id="html_title" name="html_title" class="form-control" value="<?php if (isset($arr_magazine_info["html_title"])) echo htmlspecialchars($arr_magazine_info["html_title"]) ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_description">meta description</label>
							<div class="col-sm-9">
								<input type="text" id="meta_description" name="meta_description" class="form-control" value="<?php if (isset($arr_magazine_info["meta_description"])) echo htmlspecialchars($arr_magazine_info["meta_description"]) ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="meta_keywords">meta keywords</label>
							<div class="col-sm-9">
								<input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="<?php if (isset($arr_magazine_info["meta_keywords"])) echo $arr_magazine_info["meta_keywords"] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="not_show_on_popular">Не показывать в Популярных</label>
							<div class="col-sm-9">
								<div class="checkbox_wrapper">
									<label><input type="checkbox" name="not_show_on_popular" id="not_show_on_popular" value="1"<?php if ($arr_magazine_info['not_show_on_popular']) echo ' checked=""'; ?> /></label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3" for="price_6_monthes">Весовой коэффициент в списке<br /> (чем больше, тем первее)</label>
							<div class="col-sm-9">
								<input type="text" id="list_order_value" name="list_order_value" class="form-control" value="<?php if (isset($arr_magazine_info["list_order_value"])) echo $arr_magazine_info["list_order_value"]; else echo "0"; ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3"></div>
							<div class="col-sm-9">
								<button type="submit" class="button">Сохранить</button>
								<?php if (!isset($flagNewMagazine)) { ?>
									<button type="button" class="button red right" id="delete_magazine_button"><span class="close_icon"></span> Удалить журнал</button>
								<?php } ?>
							</div>
						</div>
					</form>
				<?php } else { ?>
					Журнал не найден!
				<?php } ?>
			</div>		
		</div>
	</div>
</div>
<div class="popup_wrapper hidden_my" id="delete_magazine_popup">
	<div class="popup_overlay"></div>
	<div class="popup_confirm">
		<div class="close_icon"></div>
		<p class="question">Вы уверены, что хотите удалить этот журнал?</p>
		<div class="buttons_wrapper">
			<div class="button yes" magazine_id="<?= $arr_magazine_info["id"] ?>">Да</div>
			<div class="button red no">Нет</div>
		</div>
	</div>
</div>