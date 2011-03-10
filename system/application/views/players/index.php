<h2><?=_('Players');?></h2>

<?php if(count($players) > 0): ?>
	<ul>
		<?php foreach($players as $player): ?>
			<li><a href="<?=site_url('/player/view/'.$player->id)?>">
				<?=$player->username?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No players found.');?></p>
<?php endif; ?>
