<?

namespace rude;

class template_users
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
			case 'remove': $status = users::remove(get('id')); break;
			case 'edit': $status = users::edit(get('id'),get('name'),get('role_id')); break;


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
				$users = users::get();
			?>
			<table class="ui table segment square-corners celled">
				<thead>
					<tr class="header">
						<th class="numeric">#</th>
						<th>Имя</th>
						<th>Статус</th>
						<th colspan="2" class="right icon-add"><i class="icon add sign pointer" title="Добавить" onclick="$('#add_modal').modal('show');"></i></th>
					</tr>
				</thead>
				<tbody>
				<?
					foreach ($users as $user)
					{
						?>
						<tr id="user-<?= $user->id ?>">
							<td class="small numeric"><?= $user->id ?></td>
							<td><?= $user->name ?></td>
							<td><?= $user->role ?></td>
							<?
								$role_id=users_roles::get_by_name($user->role);
							?>
							
								<td class="icon first no-border">
								<a href="#" onclick="$('#edit_modal').modal('show'); $('.id').val('<?= $user->id?>');
									$('.editusername').val('<?= $user->name?>');
									$('#edit_role').val('<?= $role_id->id?>');
									$('#user_role_dd').dropdown('set selected',<?= $role_id->id?>);
									">
									<i class="icon edit" title="Редактировать"></i>
								</a>
							</td>
							<td class="icon last no-border">
								<a href="#" onclick="$.post('<?= template_url::ajax('users', 'remove', $user->id) ?>').done(function(answer) { answer_removed(answer, <?= $user->id ?>); }); return false;">
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
			function answer_removed(answer, user_id)
			{
				console.log(answer);


				switch(answer)
				{
					case '<?= RUDE_AJAX_ERROR            ?>':

						break;

					case '<?= RUDE_AJAX_OK               ?>':
						console.log(this);

						$('#user-' + user_id).fadeOut('slow');
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
				Добавить пользователя
			</div>

			<div class="content">
				<div class="ui form segment">
					<div class="field">
						<label for="username">Имя пользователя</label>
						<div class="ui left labeled input icon">
							<input class="username" name="username" type="text" placeholder="Имя вашего нового пользователя...">
							<i class="user icon"></i>
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>

					<div class="field">
						<label for="password">Пароль</label>
						<div class="ui left labeled input icon">
							<input class="password" name="password" type="password">
							<i class="lock icon"></i>
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>

					<div class="field">
						<label>Роль</label>
						<div class="ui fluid selection dropdown">
							<div class="default text">Выберите роль пользователя</div>

							<input type="hidden" id="role_name">
							<div style="max-height: 150px;" class="menu">
								<?	$users_roles = users_roles::get();
								foreach ($users_roles as $role)
								{
									?>
									<div class="item"  data-value="<?= $role->id  ?>"><?= $role->name  ?></div>
								<?
								}?>
							</div>
						</div>
					</div>

					<div class="ui error message">
						<div class="header">Найдены ошибки при заполнении формы</div>
					</div>

					<div class="ui blue submit button">Добавить</div>
				</div>
			</div>

		</div>


		<script>
			$('#add_modal .ui.form')
				.form({
					username: {
						identifier : 'username',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите имя для пользователя.'
							}
						]
					},
					role_name: {
						identifier : 'role_name',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите роль для пользователя.'
							}
						]
					},
					password: {
						identifier : 'password',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите пароль для пользователя.'
							},
							{
								type   : 'length[6]',
								prompt : 'Ваш пароль должен быть хотя бы 6 символов в длину.'
							}
						]
					}
				},
				{
					onSuccess: function()
					{
						var username = $('#add_modal .username').val();
						var password = $('#add_modal .password').val();
						var role_id = $('#role_name').val();






						$.ajax({
							url : '/?page=registration',

							type: 'POST',

							data :
							{
								username: username,
								password: password,
								role_id : role_id
							},

							success: function(answer)
							{
								console.log(answer);

								if (answer)
								{
									$('#add_modal .ui.error.message').html('<ul class="list"><li>' + answer + '</li></ul>').show('slow');
								}
								else
								{
									rude.redirect('/?page=users');
								}
							}
						});
					}
				})
			;
		</script>

		<div id="edit_modal" class="ui modal">
			<i class="close icon"></i>
			<div class="header">
				Редактировать пользователя
			</div>
			<div class="content">
				<div class="ui form segment">
					<div class="field">
						<label for="editusername">Имя пользователя</label>
						<div class="ui left labeled input">
							<input class="editusername" name="editusername" type="text" placeholder="Имя пользователя">
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
					<!--<div class="field">
						<label for="edit_password">Пароль</label>
						<div class="ui left labeled input">
							<input class="edit_password" name="edit_password" type="password">
							<i class="lock icon"></i>
							<div class="ui corner label">
								<i class="icon asterisk"></i>
							</div>
						</div>
					</div>-->
					<div class="field">
						<label>Роль пользователя</label>
						<div class="ui fluid selection dropdown" id="user_role_dd">
							<div class="text">Выберите роль</div>

							<input type="hidden" id="edit_role">
							<div style="max-height: 150px;" class="menu">
								<?	$roles_list = users_roles::get();
								foreach ($roles_list as $role)
								{
									?>
									<div class="item" data-value="<?= $role->id  ?>"><?= $role->name  ?></div>
								<?
								}?>
							</div>
						</div>
					</div>

					<div class="ui error message">
						<div class="header">Найдены ошибки при заполнении формы</div>
					</div>
					<div class="ui blue submit button" value="edit">Изменить</div>
				</div>
			</div>
		</div>

		<script>

			$('#edit_modal .ui.form')
				.form({
					editusername: {
						identifier : 'editusername',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите имя пользователя.'
							}
						]
					},
					edit_role: {
						identifier : 'edit_role',
						rules: [
							{
								type   : 'empty',
								prompt : 'Пожалуйста, укажите роль пользователя.'
							}
						]
					}

				},
				{
					onSuccess: function()
					{
						var name = $('.editusername').val();
						var id = $('.id').val();
						var role_id = $('#edit_role').val();

						$.post('/?page=users&task=edit&id='+id+'&name='+name+'&role_id='+role_id+'&ajax=true')
							.done(function() { $('#edit_modal').modal('hide');  rude.redirect('/?page=users');}); return false;
					}
				})
			;
		</script>
		<?
	}
}