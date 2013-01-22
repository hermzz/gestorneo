<div class="navbar navbar-fixed-top navbar-inverse">
	<div class="navbar-inner">
		<div class="container">
			<div class="nav-collapse">
				<ul class="nav">
					<li><a href="/"><?=_('home');?></a></li>

					<?php if($this->tank_auth->is_logged_in()): ?>
						<li><a href="/tournament/"><?=_('tournaments');?></a></li>
						<li><a href="/player/"><?=_('players');?></a></li>
						<li><a href="/team/"><?=_('teams');?></a></li>
						<li><a href="/auth/logout"><?=_('Logout');?></a></li>
					<?php endif; ?>
				</ul>

				<form action="#" class="pull-right">
					<select name="language_chooser" class="input-small">
						<?php foreach($languages as $key => $name): ?>
							<option value="<?=$key?>" <?=$key==$selected_language?'selected="selected"':'';?>>
								<?=$name;?>
							</option>
						<?php endforeach; ?>
					</select>
				</form>
			</div>
		</div>
	</div>
</div>
