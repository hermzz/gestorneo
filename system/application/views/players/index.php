<h2><?=_('Players');?></h2>

<?php if(count($active_players) > 0): ?>
	<h3>Active</h3>
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

<?php if(count($old_players) > 0): ?>
	<h3>The exes</h3>
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
