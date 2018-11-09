<div class="front_slider">
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<?php
				include "front-slider-cells-all.tpl";
			?>
		</div>
		<div class="swiper-pagination"></div>
	</div>
</div>		
<div class="content">
	<div class="sub_menu">
		<div class="container">
			<div class="row">	
				<div class="col-md-12">
					<ul class="magazine-filter2">
						<li class="activeFilter">
							<a href="#" data-filter="front_magazine_cell">
								<span>Популярные</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="cat-1">
								<span>Кухня</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="cat-2">
								<span>Дача</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="cat-3">
								<span>Здоровье</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="cat-4">
								<span>Семья</span>
							</a>
						</li>
						<?php if ($config_DomainEnd == "by") { ?>
						<li>
							<a href="#" data-filter="cat-5">
								<span>Дети</span>
							</a>
						</li>
						<?php } ?>
						<?php if (false) { //if ($config_DomainEnd == "ru") { ?>
						<li>
							<a href="#" data-filter="cat-groups">
								<span>Комплекты</span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="row">	
			<div class="col-md-12">
				<div class="magazines_wrapper popular">
					<div class="row">
						<?php
							require_once('__functions.php');
							$arr_magazines = get_all_magazine_info();
							if ($arr_magazines) {
								foreach ($arr_magazines as $item) {
									include "front-magazine-cell.tpl";
								}
							}
						?>
					</div>
				</div>		
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$(".magazine-filter2 li.activeFilter a").trigger('click');
});
</script>
