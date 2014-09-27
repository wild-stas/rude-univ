<?

namespace rude;

class template_departments
{
	public function __construct()
	{
		if (!template_session::is_admin() and !template_session::is_editor())
		{
			if (get('ajax'))
			{
				exit(RUDE_AJAX_ACCESS_VIOLATION);
			}

			return false;
		}


		switch (get('task'))
		{
			case 'remove': $status = departments::remove(get('id'));  break;
			case 'add': $status = departments::add(get('name'));  break;


			default:
				$status = false;
				break;
		}


		if (get('ajax'))
		{
			     if ($status) { exit(RUDE_AJAX_OK);    }
			else              { exit(RUDE_AJAX_ERROR); }
		}

		return true;
	}


	public function html()
	{
		template_html::doctype();

		?>
		<html>
			<? template_html::header() ?>

			<body>
				<? template_html::menu() ?>

				<script>
					rude.semantic.init.menu();
					rude.semantic.init.dropdown();
				</script>


				<div id="container">
					<? template_html::sidebar() ?>

					<div id="content" class="ui segment raised square-corners no-shadow">
						<? $this->main() ?>
					</div>
				</div>

				<? template_html::footer() ?>
			</body>
		</html>
		<?
	}

	public function main()
	{
		?>
		<div id="main">
			<?
				$departments = departments::get();
			?>
			<a href="#" onclick="$('#add_modal').modal('show');">
				<?= template_image::add() ?>	Добавить кафедру
			</a>
			<table class="ui table segment square-corners celled">
				<thead>
					<tr class="header">
						<th class="numeric">#</th>
						<th>Наименование кафедры</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?
					foreach ($departments as $department)
					{
						?>
						<tr id="department-<?= $department->id ?>">
							<td class="small numeric"><?= $department->id ?></td>
							<td><?= $department->name ?></td>
							<td class="icon first no-border"><?= template_image::edit() ?></td>
							<td class="icon last no-border">
								<a href="#" onclick="$.post('<?= template_url::ajax('departments', 'remove', $department->id) ?>').done(function(answer) { answer_removed(answer, <?= $department->id ?>); }); return false;">
									<?= template_image::remove() ?>
								</a>
							</td>
						</tr>
						<?
					}
				?>
				</tbody>
			</table>
		</div>

		<script>
			function answer_removed(answer, department_id)
			{
				console.log(answer);


				switch(answer)
				{
					case '<?= RUDE_AJAX_ERROR            ?>':

						break;

					case '<?= RUDE_AJAX_OK               ?>':
						console.log(this);

						$('#department-' + department_id).fadeOut('slow');
						break;

					case '<?= RUDE_AJAX_ACCESS_VIOLATION ?>':
						$('#access-violation').modal('show');
						break;

					default:
						break;
				}

				return false;
			}
		</script>


		<div id="add_modal" class="ui modal">
			<i class="close icon"></i>
			<div class="header">
				Добавить кафедру
			</div>
			<div class="content">
				<div class="ui form segment">
					<div class="field">
						<label for="name">Наименование кафедры</label>
						<div class="ui left labeled icon input">
							<input class="name" name="name" type="text" placeholder="Наименование кафедры">
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>
					<div class="ui error message">
						<div class="header">Найдены ошибки при заполнении формы</div>
					</div>
					<div class="ui blue submit button" value="add">Добавить</div>
				</div>
			</div>
		</div>

		<script>

			$('#add_modal .ui.form')
				.form({
					name: {
						identifier : 'name',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите наименование кафедры.'
							}
						]
					}
				},
				{
					onSuccess: function()
					{
						var name = $('.name').val();
						$.post('/?page=departments&task=add&name='+name+'&ajax=true')
						.done(function() { $('#add_modal').modal('hide');  rude.redirect('/?page=departments');}); return false;
					}
				})
			;
		</script>
	<?
	}
}