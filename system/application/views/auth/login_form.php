<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
	'size'	=> 30,
  'class' => (form_error('login') != '' ? 'error' : '')
);

if ($login_by_username AND $login_by_email) {
	$login_label = _('Email or login');
} else if ($login_by_username) {
	$login_label = _('Login');
} else {
	$login_label = _('Email');
}
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
  'class' => (form_error('password') != '' ? 'error' : '')
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8
);
$submit = array(
    'name' => 'submit',
    'id' => 'submit',
    'class' => 'btn btn-primary'
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table id="login-form">
	<tr>
		<td class="col1"><?php echo form_label($login_label, $login['id']); ?></td>
		<td class="col2"><?php echo form_input($login); ?></td>
		<td style="color: red;"><?php echo form_error($login['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>

	<?php if ($show_captcha) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
		</td>
		<td>
			<a href="javascript:Recaptcha.reload()"><?=_('Get another CAPTCHA');?></a>
			<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')"><?=_('Get an audio CAPTCHA');?></a></div>
			<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')"><?=_('Get an image CAPTCHA');?></a></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image"><?=_('Enter the words above');?></div>
			<div class="recaptcha_only_if_audio"><?=_('Enter the numbers you hear');?></div>
		</td>
		<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
		<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p><?=_('Enter the code exactly as it appears')?>:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label(_('Confirmation Code'), $captcha['id']); ?></td>
		<td><?php echo form_input($captcha); ?></td>
		<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
	</tr>
	<?php }
	} ?>

	<tr>
		<td>
			<?php echo form_label(_('Remember me'), $remember['id']); ?>
		</td>
		<td colspan="2">
			<?php echo form_checkbox($remember); ?>
			<?php echo form_submit($submit, _('Let me in')); ?>
		</td>
	</tr>
</table>
<?php echo form_close(); ?>

<p><?=_('If you forgot your password, <a href="/auth/forgot_password/">click here to reset it</a>');?>.</p>
<?php if ($this->config->item('allow_registration', 'tank_auth')): ?>
	<p><?=_('If you don\'t have an account yet, <a href="/auth/register/">click here to create one</a>');?>.</p>
<?php endif; ?>
