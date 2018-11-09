<div class="content faq">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php
					$arr_result = set_subscribe_finish();
					if ($arr_result["error_text"] == "") { ?>
						<h1>Ошибка оплаты!</h1>
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
