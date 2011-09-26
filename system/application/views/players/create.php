<h2><?=_('New player');?></h2>

<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'value' => set_value('username'),
	'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
	'size'	=> 30,
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$sex = array(
	'name'	=> 'sex',
	'id'	=> 'sex',
	'value'	=> set_value('sex'),
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
		<td><?php echo form_label(_('Sex'), $sex['id']); ?></td>
		<td><?php echo form_dropdown('sex', array('M' => _('Guy'), 'F' => _('Girl')), null, 'id="sex"'); ?></td>
		<td style="color: red;"><?php echo form_error($sex['name']); ?></td>
	</tr>
</table>
<?php echo form_submit('create', _('Create')); ?>
<?php echo form_close(); ?>
