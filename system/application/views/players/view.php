<?php if($player): ?>
	<h2><?=$player->username?> <span class="header-small">(<?=$player->email;?>)</span></h2>
	
	<p><?=sprintf(_('Member since %s'), strftime('%A %e, %B %Y', mysql_to_unix($player->created)));?></p>
	
	<h3><?=_('Tournaments');?></h3>
	
	<?php if($this->tank_auth->is_admin(array('player' => $player->id))): ?>
		<p><a href="/player/edit/<?=$player->id;?>">Edit player</a></p>
	<?php endif; ?>
	
	<?php if($tournaments): ?>
		<ul>
			<?php foreach($tournaments as $tournament): ?>
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
