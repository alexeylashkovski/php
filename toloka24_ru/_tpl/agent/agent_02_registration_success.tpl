<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/agent/breadcrumb.tpl"; ?>
				<?php if ($post_success_info && $error_description == '') echo "<p class=\"post_success_info\">$post_success_info</p>"; ?>
				<?php if ($error_description) echo "<p class=\"error_description\">$error_description</p>"; ?>
				<?php if ($error_description == '') { ?>
					<h1>Вы успешно зарегистрировались!</h1>
					<h3>На ваш e-mail отправлено письмо с подтверждением регистрации</h3>
					<p class="success_small_text">В большинстве случаев письма приходят в течение одной минуты, но иногда для этого требуется до 10 минут.
					Если письмо с подтверждением долго не приходит, проверьте папку Спам (папку для нежелательной почты).
					Если письмо случайно попало в эту папку, откройте его и нажмите кнопку «Это не спам».</p>
				<?php } else { ?>
					<h1>Ошибка регистрации!</h1>
				<?php } ?>
			</div>		
		</div>
	</div>
</div>
