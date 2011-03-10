<h2><?=sprintf(_('Edit player %s'), $player->username);?></h2>

<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'value' => set_value('username', $player->username),
	'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
	'size'	=> 30,
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email', $player->email),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$sex = array(
	'name'	=> 'sex',
	'id'	=> 'sex',
	'value'	=> set_value('sex', $player->sex),
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
	<tr>
		<td><?php echo form_label(_('Full name'), $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label(_('Email Address'), $email['id']); ?></td>
		<td><?php echo form_input($email); ?></td>
		<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
	</tr>
	<tr>
		<td colspan="3">
			<?=_('Leave passwords empty if you don\'t want to change them.');?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label(_('Password'), $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label(_('Confirm Password'), $confirm_password['id']); ?></td>
		<td><?php echo form_password($confirm_password); ?></td>
		<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label(_('Sex'), $sex['id']); ?></td>
		<td><?php echo form_dropdown('sex', array('M' => _('Guy'), 'F' => _('Girl')), $player->sex, 'id="sex"'); ?></td>
		<td style="color: red;"><?php echo form_error($sex['name']); ?></td>
	</tr>
</table>
<?php echo form_submit('editInfo', _('Edit information')); ?>
<?php echo form_close(); ?>
