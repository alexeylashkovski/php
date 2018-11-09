<div class="content faq">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php
					$arr_result = set_subscribe_info();
					if ($arr_result["error_text"] != '') { ?>
						<p class="error_description">Ошибка: <?= $arr_result["error_text"] ?></p>
				<?php } else {				
						if ($arr_result["banking_pay_url"] == '') { ?>
							<h2>Ошибка!!!</h2>
						<?php } else { ?>
							<h2>Следующий шаг, <a href="<?= $arr_result["banking_pay_url"] ?>">оплатить</a>.</h2>
							<script>window.location = "<?= $arr_result["banking_pay_url"] ?>";</script>
						<?php } ?>						
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="">
</div>
