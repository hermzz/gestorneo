<div class="navbar navbar-fixed-top navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="<?= site_url('') ?>">Gestorneo</a>
	</div>

	<div class="navbar-collapse collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
			<li><a href="/"><?=_('home');?></a></li>

			<?php if($this->tank_auth->is_logged_in()): ?>
				<li><a href="/tournament/"><?=_('tournaments');?></a></li>
				<li><a href="/player/"><?=_('players');?></a></li>
				<li><a href="/team/"><?=_('teams');?></a></li>
				<li><a href="/practice/"><?=_('practices');?></a></li>
				<li><a href="/auth/logout"><?=_('Logout');?></a></li>
			<?php endif; ?>
		</ul>

		<form action="#" class="pull-right">
			<select name="language_chooser" class="input-sm">
				<?php foreach($languages as $key => $name): ?>
					<option value="<?=$key?>" <?=$key==$selected_language?'selected="selected"':'';?>>
						<?=$name;?>
					</option>
				<?php endforeach; ?>
			</select>
		</form>
	</div>
</div>
