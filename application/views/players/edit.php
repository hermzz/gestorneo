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
<?php echo form_open($this->uri->uri_string(), array('class' => 'well')); ?>
<fieldset>
		<?php
			$name_error = strlen(form_error($username['name'])) > 0;
		?>
		<div class="clearfix <?=$name_error ? 'error' : '';?>">
			<?php echo form_label(_('Full name'), $username['id']); ?>
			<div class="input">
				<?php echo form_input($username); ?>
				<?php if($name_error): ?>
					<span class="help-inline">
						<?=form_error($username['name']); ?><?=isset($errors[$username['name']])?$errors[$username['name']]:'';?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<?php
			$email_error = strlen(form_error($email['id'])) > 0;
		?>
		<div class="clearfix <?=$email_error ? 'error' : '';?>">
			<?php echo form_label(_('Email Address'), $email['id']); ?>
			<div class="input">
				<?php echo form_input($email); ?>
				<?php if($email_error): ?>
					<span class="help-inline">
						<?=form_error($email['name']);?><?=isset($errors[$email['name']])?$errors[$email['name']]:'';?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="alert-message warning">
			<p><?=_('Leave passwords empty if you don\'t want to change them.');?></p>
		</div>

		<?php
			$pass1_error = strlen(form_error($password['name'])) > 0;
		?>
		<div class="clearfix <?=$pass1_error ? 'error' : '';?>">
			<?php echo form_label(_('Password'), $password['id']); ?>
			<div class="input">
				<?php echo form_password($password); ?>
				<?php if($pass1_error): ?>
					<span class="help-inline">
						<?=form_error($password['name']);?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<?php
			$pass2_error = strlen(form_error($confirm_password['name'])) > 0;
		?>
		<div class="clearfix <?=$pass2_error ? 'error' : '';?>">
			<?php echo form_label(_('Confirm Password'), $confirm_password['id']); ?>
			<div class="input">
				<?php echo form_password($confirm_password); ?>
				<?php if($pass2_error): ?>
					<span class="help-inline">
						<?=form_error($confirm_password['name']);?>
					</span>
				<?php endif; ?>
			</div>
		</div>

		<?php
			$sex_error = strlen(form_error($sex['name'])) > 0;
		?>
		<div class="clearfix <?=$sex_error ? 'error' : '';?>">
			<?php echo form_label(_('Sex'), $sex['id']); ?>
			<div class="input">
				<?php echo form_dropdown('sex', array('M' => _('Guy'), 'F' => _('Girl')), $player->sex, 'id="sex"'); ?>
				<?php if($sex_error): ?>
					<span class="help-inline">
						<?=form_error($sex['name']);?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</fieldset>
<?php echo form_submit(array('name' => 'editInfo', 'value' => _('Edit information'), 'class' => 'btn btn-primary')); ?>
<?php echo form_close(); ?>
