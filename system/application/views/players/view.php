<?php if($player): ?>
	<?php if($this->tank_auth->is_admin(array('player' => $player->id))): ?>
		<ul class="nav nav-tabs">
			<li class="dropdown pull-left" data-dropdown="dropdown">
				<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="/player/edit/<?=$player->id;?>">Edit player</a></li>
					<?php if($this->tank_auth->is_admin()): ?><li>
						<?php if($player->activated): ?>
							<a href="/player/disable/<?=$player->id;?>">Disable player</a>
						<?php else: ?>
							<a href="/player/enable/<?=$player->id;?>">Enable player</a>
						<?php endif; ?>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		</ul>
	<?php endif; ?>
	
	<h2><?=$player->username?> <span class="header-small">(<?=$player->email;?>)</span></h2>
	
	<p><?=sprintf(_('Member since %s'), strftime('%A %e, %B %Y', mysql_to_unix($player->created)));?></p>
	
	<h3><?=_('Tournaments');?></h3>
	
	<?php if($tournaments): ?>
		<?php $year = false; ?>
			<?php foreach($tournaments as $tournament): ?>
				<?php
					if(!$year)
					{
						echo '<h4>'.strftime('%Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
						$year = strftime('%Y', mysql_to_unix($tournament->start_date));
					} else {
						$t_year = strftime('%Y', mysql_to_unix($tournament->start_date));
						if($t_year != $year)
						{
							echo '</ul><h4>'.strftime('%Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
							$year = $t_year;
						}
					}
				?>
				<li><a href="/tournament/view/<?=$tournament->id?>"><?=$tournament->name?></a>
					 - <?=strftime('%A %e %B', mysql_to_unix($tournament->start_date))?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p><?=_('No tournaments for this player.');?></p>
	<?php endif; ?>
<?php else: ?>
	<p><?=_('Player not found.');?></p>
<?php endif; ?>
