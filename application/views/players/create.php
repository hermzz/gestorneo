<h2><?=_('New player');?></h2>

<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'value' => set_value('username'),
	'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
	'size'	=> 30,
	'class' => 'form-control'
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
	'class' => 'form-control'
);
$sex = array(
	'name'	=> 'sex',
	'id'	=> 'sex',
	'value'	=> set_value('sex'),
	'class' => 'form-control'
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

		<?php
			$sex_error = strlen(form_error($sex['name'])) > 0;
		?>
		<div class="clearfix <?=$sex_error ? 'error' : '';?>">
			<?php echo form_label(_('Sex'), $sex['id']); ?>
			<div class="input">
				<?php echo form_dropdown('sex', array('M' => _('Guy'), 'F' => _('Girl')), null, 'id="sex" class="form-control"'); ?>
				<?php if($sex_error): ?>
					<span class="help-inline">
						<?=form_error($sex['name']);?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</fieldset>
<?php echo form_submit(array('name' => 'create', 'value' => _('Create'), 'class' => 'btn btn-primary')); ?>
<?php echo form_close(); ?>
