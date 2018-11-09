				<table cellspacing="0" cellpadding="0" border="0" class="admin_promocodes_table">
				<tr>
					<th>#</th>
					<th>Код</th>
					<th>Скидка</th>
					<th>Подробное описание кода</th>
					<th>Дата начала</th>
					<th>Дата окончания</th>
					<th>К журналам</th>
					<th>Дата создания</th>
					<th>Завершен</th>
					<th>Удален</th>
					<th></th>
					<th></th>
				</tr>
				<?php
					if (!count($arr_promocodes)) {
						echo '<tr><td colspan="9" align="center">Действующих промокодов нет</td></tr>';
					}
					foreach ($arr_promocodes as $item) {
						echo '<tr class="';
						if ($item['completed']) echo 'completed ';
						if ($item['deleted']) echo 'deleted ';
						echo '">';
							echo '<td>' . $item['id'] . '</td>';
							echo '<td>' . $item['code_text'] . '</td>';
							echo '<td>' . $item['discount_percent'] . '%</td>';
							echo '<td>' . $item['code_name'] . '</td>';
							echo '<td>' . substr($item['datetime_start'], 0, strpos($item['datetime_start'], ' ')) . '</td>';
							echo '<td>' . substr($item['datetime_end'], 0, strpos($item['datetime_end'], ' ')) . '</td>';
							echo '<td>';
								if ($item['for_all_magazines']) {
									echo 'Ко всем'; 
								} else {
									echo 'Выборочно';
								}
							echo '</td>';
							echo '<td>' . $item['datetime_created'] . '</td>';
							echo '<td align="center">';
								if ($item['completed']) {
									echo '<span class="promocode_completed"></span>';
								}
							echo '</td>';
							echo '<td align="center">';
								if ($item['deleted']) {
									echo '<span class="promocode_deleted"></span>';
								}
							echo '</td>';
							echo '<td>';
								if (!$item['deleted']) {
									echo '<a href="/admin/?act=edit_promocode&id=' . $item['id'] . '">Редактировать</a>';
								}
							echo '</td>';
							echo '<td>';
								if (!$item['deleted']) {
									echo '<a href="#" class="delete_promocode_link" promocode_id="' . $item['id'] . '" promocode_code_text="' . $item['code_text'] . '">Удалить</a>';
								}
							echo '</td>';
						echo '</tr>';
					}
				?>
				</table>
				<div style="height: 10px;"></div>
