<div class="content faq">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php
					$arr_result = set_subscribe_finish();
					if ($arr_result["error_text"] == "") { ?>
						<h1>Спасибо!</h1>
						<h2><img src="/_img/input_check.svg" /> Ваша подписка на журнал «<?= $arr_result["magazine_name"] ?>» оформлена и оплачена.</h2>
						<h3>Письмо с подтверждением оплаты отправлено на <?= $arr_result["email"] ?></h3>
						<p class="success_small_text">В большинстве случаев письма приходят в течение одной минуты, но иногда для этого требуется до 10 минут.
						Если письмо с подтверждением долго не приходит, проверьте папку Спам (папку для нежелательной почты).
						Если письмо случайно попало в эту папку, откройте его и нажмите кнопку «Это не спам».</p>
			</div>
		</div>		
		<div class="row">	
			<div class="col-md-3">
				<img class="magazine_cover" src="/_img/magazine/<?= $arr_result["magazine_id"] ?>/cover.jpg" />
			</div>
			<div class="col-md-9">
						<table class="order_success_table">
						<tr>
							<td>Номер заказа</td>
							<td><?= $arr_result["success_info"]["id_order"] ?></td>
						</tr>
						<tr>
							<td>Дата доставки</td>
							<td><?= $arr_result["success_info"]["month_start_text"] ?></td>
						</tr>
						<tr>
							<td>Количество месяцев</td>
							<td><?= $arr_result["success_info"]["monthes"] ?></td>
						</tr>
						<tr>
							<td>Количество номеров</td>
							<td><?= $arr_result["success_info"]["monthes"]*$arr_result["success_info"]["month_releases_num"] ?></td>
						</tr>
						<tr>
							<td>Адрес доставки</td>
							<td><?= $arr_result["success_info"]["address_text"] ?></td>
						</tr>
						<tr>
							<td>Получатель</td>
							<td><?= $arr_result["success_info"]["fio"] ?></td>
						</tr>
						<tr>
							<td>Состав заказа</td>
							<td>Журнал «<?= $arr_result["success_info"]["magazine_name"] ?>»</td>
						</tr>
						<tr>
							<td>Комментарий к заказу</td>
							<td><?= $arr_result["success_info"]["comment"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td><b>Оплачено</b></td>
							<td><b><?= $arr_result["success_info"]["approvedAmount"] ?> рублей</b></td>
						</tr>
						</table>
						<br /><br /><br />
						<a class="button button_centred" href="/">Подписаться ещё</a>
				<?php } else { ?>
					<p class="error_description">Ошибка: <?= $arr_result["error_text"] ?></p>
					<?php if ($arr_result["pay_again_url"] != "") { ?>
						<br /><h2 class="centred">Вы можете попробовать <a href="<?= $arr_result["pay_again_url"] ?>">оплатить</a> подписку еще раз.</h2>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
