<?

namespace rude;

class template_specializations
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
			case 'remove': $status = specializations::remove(get('id')); break;
			case 'add': $status = specializations::add(get('name'),get('code'));  break;
			case 'edit': $status = specializations::edit(get('id'),get('name'),get('code'));  break;

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
				$specializations = specializations::get();
			?>
			<table class="ui table segment square-corners celled">
				<thead>
					<tr class="header">
						<th class="numeric">#</th>
						<th>Наименование</th>
						<th class="middle">Код</th>
						<th colspan="2" class="right icon-add"><i class="icon add sign pointer" title="Добавить" onclick="$('#add_modal').modal('show');"></i></th>
					</tr>
				</thead>
				<tbody>
				<?
					foreach ($specializations as $specialization)
					{
						?>
						<tr id="specialization-<?= $specialization->id ?>">
							<td class="small numeric"><?= $specialization->id ?></td>
							<td><?= $specialization->name ?></td>
							<td class="monospace numeric"><?= $specialization->code ?></td>
							<td class="icon first no-border">
								<a href="#" onclick="$('#edit_modal').modal('show'); $('.id').val('<?= $specialization->id?>');  $('.editname').val('<?= $specialization->name?>');  $('.editcode').val('<?= $specialization->code?>');">
									<i class="icon edit" title="Редактировать"></i>
								</a>
							</td>
							<td class="icon last no-border">
								<a href="#" onclick="$.post('<?= template_url::ajax('specializations', 'remove', $specialization->id) ?>').done(function(answer) { answer_removed(answer, <?= $specialization->id ?>); }); return false;">
									<i class="icon remove circle" title="Удалить"></i>
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
			function answer_removed(answer, specialization_id)
			{
				console.log(answer);


				switch(answer)
				{
					case '<?= RUDE_AJAX_ERROR            ?>':

						break;

					case '<?= RUDE_AJAX_OK               ?>':
						console.log(this);

						$('#specialization-' + specialization_id).fadeOut('slow');
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
				Добавить специализацию
			</div>
			<div class="content">
				<div class="ui form segment">
					<div class="field">
						<label for="name">Наименование специализации</label>
						<div class="ui left labeled input">
							<input class="name" name="name" type="text" placeholder="Наименование специализации">
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>

					<div class="field">
						<label for="code">Код специализации</label>
						<div class="ui left labeled input">
							<input class="code" name="code" type="text" placeholder="Код специализации">
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
								prompt : 'Пожалуйста, укажите наименование специализации.'
							}
						]
					},
					code: {
						identifier : 'code',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите код специализации.'
							}
						]
					}
				},
				{
					onSuccess: function()
					{
						var name = $('.name').val();
						var code = $('.code').val();
						$.post('/?page=specializations&task=add&name='+name+'&code='+code+'&ajax=true')
							.done(function() { $('#add_modal').modal('hide'); rude.redirect('/?page=specializations'); }); return false;
					}
				})
			;
		</script>


		<div id="edit_modal" class="ui modal">
			<i class="close icon"></i>
			<div class="header">
				Редактировать специализацию
			</div>
			<div class="content">
				<div class="ui form segment">
					<div class="field">
						<label for="editname">Наименование специализации</label>
						<div class="ui left labeled input">
							<input class="editname" name="editname" type="text" placeholder="Наименование специализации">
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>

					<div class="field">
						<label for="editcode">Код специализации</label>
						<div class="ui left labeled input">
							<input class="editcode" name="editcode" type="text" placeholder="Код специализации">
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>
					<div class="field" hidden>
						<label for="id">id</label>
						<div class="ui left labeled input">
							<input class="id" name="id" type="text" placeholder="id">
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>
					<div class="ui error message">
						<div class="header">Найдены ошибки при заполнении формы</div>
					</div>
					<div class="ui blue submit button" value="add">Изменить</div>
				</div>
			</div>
		</div>

		<script>

			$('#edit_modal .ui.form')
				.form({
					editname: {
						identifier : 'editname',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите наименование специализации.'
							}
						]
					},
					editcode: {
						identifier : 'editcode',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите код специализации.'
							}
						]
					}
				},
				{
					onSuccess: function()
					{
						var name = $('.editname').val();
						var code = $('.editcode').val();
						var id = $('.id').val();
						$.post('/?page=specializations&task=edit&id='+id+'&name='+name+'&code='+code+'&ajax=true')
							.done(function() { $('#edit_modal').modal('hide'); rude.redirect('/?page=specializations'); }); return false;
					}
				})
			;
		</script>
		<?
	}
}