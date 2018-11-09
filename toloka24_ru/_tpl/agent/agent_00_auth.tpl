<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/agent/breadcrumb.tpl"; ?>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-6">
				<div class="row">	
					<div class="col-sm-2"></div>
					<div class="col-sm-10">
						<h2>Отчетность</h2>
						<?php
							if ($post_request_stats_success_info) {
								echo "<p class=\"request_success_info \">$post_request_stats_success_info</p>";
							}
							if ($post_request_stats_error_info) {
								echo "<p class=\"request_error_info \">$post_request_stats_error_info</p>";
							}
						?>
					</div>
					<form id="form_stats" action="/agent/" method="post">
						<input type="hidden" name="act" value="request_stats" />
						<div class="form-group form-group_empty">
							<label class="col-sm-2" for="name"></label>
							<div class="col-sm-10">
								<div class="status_error_wrapper">
									<div feedback="1" name="status_error" class="status_error"></div>
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2" for="name">Код <span class="required">*</span></label>
							<div class="col-sm-10">
								<input type="text" id="promocode" name="promocode" class="form-control" value="" required="required" feedback="1" placeholder="Введите ваш промокод" />
								<div class="help-block"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2"></div>
							<div class="col-sm-10">
								<button type="submit" class="button right">Сделать запрос</button>
							</div>
						</div>
					</form>
				</div>			
			</div>			
			<div class="col-md-6">
				<div class="row">	
					<div class="col-sm-2"></div>
					<div class="col-sm-10">
						<h2>Расчет</h2>
						<?php
							if ($post_request_calculation_success_info) {
								echo "<p class=\"request_success_info \">$post_request_calculation_success_info</p>";
							}
							if ($post_request_calculation_error_info) {
								echo "<p class=\"request_error_info \">$post_request_calculation_error_info</p>";
							}
						?>
					</div>
					<form id="form_calculation" action="/agent/" method="post">
						<input type="hidden" name="act" value="request_calculation" />
						<div class="form-group form-group_empty">
							<label class="col-sm-2" for="name"></label>
							<div class="col-sm-10">
								<div class="status_error_wrapper">
									<div feedback="1" name="status_error" class="status_error"></div>
									<div class="help-block"></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2" for="name">Код <span class="required">*</span></label>
							<div class="col-sm-10">
								<input type="text" id="promocode" name="promocode" class="form-control" value="" required="required" feedback="1" placeholder="Введите ваш промокод" />
								<div class="help-block"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2"></div>
							<div class="col-sm-10">
								<button type="submit" class="button right">Сделать запрос</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="row">	
			<div class="col-md-12 centred">
				<hr><br>
				<a href="/agent/?act=new_agent" class="button same_size_button">Зарегистрироваться</a>
				<a href="/agent/useful_info/" class="button same_size_button">Полезная информация</a>
			
				<!--h1>Авторизация</h1>
				<form id="form_login" action="/agent/" method="post">
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
							<a href="/agent/?act=new_agent" class="button right">Зарегистрироваться</a>
						</div>
					</div>
				</form-->
			</div>
		</div>
	</div>
</div>
