<div class="content">
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<?php require "../_tpl/admin/breadcrumb.tpl"; ?>
			</div>		
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">	
			<div class="col-md-12">
				<h1>История всех промокодов</h1>
				<?php require "../_tpl/admin/promocodes_all_table.tpl"; ?>
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
			<div class="button yes" promocode_id="<?= $arr_promocode_info["id"] ?>">Да</div>
			<div class="button red no">Нет</div>
		</div>
	</div>
</div>