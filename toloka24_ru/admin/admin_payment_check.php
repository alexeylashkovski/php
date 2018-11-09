<?php
	require_once('../__functions.php');
	$id_order = filter_input(INPUT_POST, 'id_order');
	if ($id_order) {
		$db_link = @connecDB();
		$mysqli_result = mysqli_query($db_link, "SELECT * FROM subscribe WHERE id_order='" . mysqli_real_escape_string($db_link, $id_order) . "'");
		$banking_token = "";
		while ($tmp_row = mysqli_fetch_assoc($mysqli_result)) {
			$banking_token = $tmp_row["banking_token"];
			break;
		}
		if (!$banking_token) {
			$error_text = "Такого заказа не было!";
		} else {
			if ($config_DomainEnd == 'by') {
				$checking_parameter = 'token';
			} else {
				$checking_parameter = 'orderId';
			}
			$check_url = 'https://www.toloka24.' . $config_DomainEnd . '/finish/?' . $checking_parameter . '=' . $banking_token;
			header('Location: ' . $check_url);
			exit;
		}
	}
	require "../_tpl/_header.tpl";
?>
<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php
					if ($error_text != '') { ?>
						<p class="error_description">Ошибка: <?= $error_text ?></p>
				<?php } ?>
			</div>
		</div>		
		<div class="row">	
			<div class="col-md-12">
				<h1>Проверка платежей</h1>
				<form id="form_check_id_order" action="/admin/admin_payment_check.php" method="post">
					<div class="form-group">
						<label class="col-sm-2" for="name">Номер заказа <span class="required">*</span></label>
						<div class="col-sm-4">
							<input type="text" id="id_order" name="id_order" class="form-control" value="<?= filter_input(INPUT_POST, 'id_order') ?>" required="required" feedback="1" autofocus="" />
							<div class="help-block"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<button type="submit" class="button">Проверить</button>
						</div>
					</div>
				</form>
			</div>		
		</div>
	</div>
</div>
<?php
	require "../_tpl/_footer.tpl";
?>