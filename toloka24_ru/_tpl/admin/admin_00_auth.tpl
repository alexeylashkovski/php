<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
				<h1>Авторизация</h1>
				<form id="form_login" action="/admin/" method="post">
					<input type="hidden" name="act" value="login" />
					<div class="form-group form-group_empty">
						<label class="col-sm-1" for="name"></label>
						<div class="col-sm-4">
							<div class="status_error_wrapper">
								<div feedback="1" name="status_error" class="status_error"></div>
								<div class="help-block"></div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-1" for="name">Логин <span class="required">*</span></label>
						<div class="col-sm-4">
							<input type="text" id="login" name="login" class="form-control" value="" required="required" feedback="1" autofocus="" />
							<div class="help-block"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-1" for="name">Пароль <span class="required">*</span></label>
						<div class="col-sm-4">
							<input type="password" id="pass" name="pass" class="form-control" value="" required="required" feedback="1" />
							<div class="help-block"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-1"></div>
						<div class="col-sm-4">
							<button type="submit" class="button">Войти</button>
						</div>
					</div>
				</form>
			</div>		
		</div>
	</div>
</div>
