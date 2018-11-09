<div class="col-xs-6 col-sm-6 col-md-3 front_magazine_cell visible<?php foreach ($item["categories"] as $categories) { echo ' cat-' . $categories['id']; } ?><?php if ($item["not_show_on_popular"]) echo " not_show_on_popular";?>">
	<?php if ($item["categories"][0]["id"] == "groups") { ?>
	<a href="../group/<?= $item['url'] ?>/">
	<?php } else { ?>		
	<a href="../magazine/<?= $item['url'] ?>/">
	<?php } ?>		
		<?php if ($item["categories"][0]["id"] == "groups") { ?>
			<img src="/_img/group/<?= $item['id'] ?>/cover.jpg" />
		<?php } else { ?>		
			<img src="/_img/magazine/<?= $item['id'] ?>/cover.jpg" />
		<?php } ?>		
		<div class="magazine-overlay"><button class="button button-rounded button-small">Подписаться</button></div>
	</a>
	<div class="title"><?= $item['name'] ?></div>
</div>
