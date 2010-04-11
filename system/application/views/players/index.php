<h2><?=_('Players');?></h2>

<?php if(count($players) > 0): ?>
	<ul>
		<?php foreach($players->result() as $player): ?>
			<li><a href="<?=site_url('/player/view/'.$player->id)?>">
				<?=$player->username?></a> (<?=sprintf(_('joined %s'), strftime('%a %e, %B %Y', mysql_to_unix($player->created)));?>)
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No players found.');?></p>
<?php endif; ?>
