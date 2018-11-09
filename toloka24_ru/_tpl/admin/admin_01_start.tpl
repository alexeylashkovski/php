<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
				<h1>Журналы</h1>
				<form id="form_choose_magazine" action="" method="post">
					<input type="hidden" name="act" value="choose_magazine" />
					<table cellspacing="0" cellpadding="0" border="0" class="admin_main_table">
					<tr>
						<td>
							<select name="magazine" id="magazine">
								<option value="" selected="selected" disabled="disabled">--- Выберите журнал ---</option>
								<?php
									foreach ($arr_magazines as $item) {
										echo '<option value="' . $item['id'] . '">' . $item['name'] . ' (' . $item['post_index'] . '), [' .  $item['list_order_value'] . ']' . '</option>' . "\n";
									}
								?>
							</select>
						</td>
						<td>
							<button type="submit" class="button">Редактировать журнал</button>
						</td>
					</tr>
					<tr>
						<td>
							<a href="/admin/?act=new_magazine"><button type="button" class="button">Добавить новый журнал</button></a>
						</td>
						<td></td>
					</tr>
					</table>
				</form>
				<hr />

				<h1>Комплекты</h1>
				<form id="form_choose_group" action="" method="post">
					<input type="hidden" name="act" value="choose_group" />
					<table cellspacing="0" cellpadding="0" border="0" class="admin_main_table">
					<tr>
						<td>
							<select name="group" id="group">
								<option value="" selected="selected" disabled="disabled">--- Выберите комплект ---</option>
								<?php
									foreach ($arr_groups as $item) {
										echo '<option value="' . $item['id'] . '">' . $item['name'] . ' (' . count($item['magazines']) . '), [' .  $item['list_order_value'] . ']' . '</option>' . "\n";
									}
								?>
							</select>
						</td>
						<td>
							<button type="submit" class="button">Редактировать комплект</button>
						</td>
					</tr>
					<tr>
						<td>
							<a href="/admin/?act=new_group"><button type="button" class="button">Добавить новый комплект</button></a>
						</td>
						<td></td>
					</tr>
					</table>
				</form>
				<hr />

				<h1>Действующие промокоды</h1>
				<?php require "../_tpl/admin/promocodes_actual_table.tpl"; ?>
				<a href="/admin/?act=new_promocode"><button type="button" class="button">Добавить новый промокод</button></a>
				<a href="/admin/?act=all_promocodes"><button type="button" class="button right">Смотреть все промокоды</button></a>
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
			<div class="button yes" promocode_id="">Да</div>
			<div class="button red no">Нет</div>
		</div>
	</div>
</div>