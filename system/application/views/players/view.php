<?php if($player): ?>
	<?php if($this->tank_auth->is_admin(array('player' => $player->id))): ?>
		<ul class="tabs">
			<li class="dropdown pull-right" data-dropdown="dropdown">
				<a href="#" class="dropdown-toggle">Admin</a>
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
		<?php $month_year = false; ?>
			<?php foreach($tournaments as $tournament): ?>
				<?php
					if(!$month_year)
					{
						echo '<h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
						$month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
					} else {
						$t_month_year = strftime('%m-%Y', mysql_to_unix($tournament->start_date));
						if($t_month_year != $month_year)
						{
							echo '</ul><h4>'.strftime('%B %Y', mysql_to_unix($tournament->start_date)).'</h4><ul>';
							$month_year = $t_month_year;
						}
					}
				?>
				<li><a href="/tournament/view/<?=$tournament->id?>"><?=$tournament->name?></a>
					 - <?=strftime('%A %e, %B %Y', mysql_to_unix($tournament->start_date))?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p><?=_('No tournaments for this player.');?></p>
	<?php endif; ?>
<?php else: ?>
	<p><?=_('Player not found.');?></p>
<?php endif; ?>
