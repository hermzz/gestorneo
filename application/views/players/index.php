	<div class="tabbable">
		<ul class="nav nav-tabs">
	    <li class="active"><a href="#tab1" data-toggle="tab"><?=_('Active');?></a></li>
	    <li><a href="#tab2" data-toggle="tab"><?=_('The exes');?></a></li>
	<?php if($this->tank_auth->is_admin()): ?>
			<li class="dropdown pull-left" data-dropdown="dropdown">
				<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="/player/create/"><?=_('New player');?></a></li>
				</ul>
			</li>
	<?php endif; ?>
		</ul>
	  <div class="tab-content">
			<h2><?=_('Players');?></h2>
		  <div class="tab-pane active" id="tab1">
				<?php if($active_players): ?>
					<h3><?=_('Active');?></h3>
					<ul>
						<?php foreach($active_players as $player): ?>
							<li><a href="<?=site_url('/player/view/'.$player->id)?>">
								<?=$player->username?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p><?=_('No active players.');?></p>
				<?php endif; ?>

		  </div>
		  <div class="tab-pane" id="tab2">
				<?php if($old_players): ?>
					<h3><?=_('The exes');?></h3>
					<ul>
						<?php foreach($old_players as $player): ?>
							<li><a href="<?=site_url('/player/view/'.$player->id)?>">
								<?=$player->username?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p><?=_('No inactive players.');?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>