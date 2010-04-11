<?php if($player): ?>
	<h2><?=$player->username?></h2>
	
	<h3><?=_('Tournaments');?></h3>
	
	<?php if($tournaments): ?>
		<ul>
			<?php foreach($tournaments->result() as $tournament): ?>
				<li><a href="/tournament/view/<?=$tournament->id?>"><?=$tournament->name?></a>
					 - <?=date('F Y', $tournament->u_date)?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p><?=_('No tournaments for this player.');?></p>
	<?php endif; ?>
<?php else: ?>
	<p><?=_('Player not found.');?></p>
<?php endif; ?>
