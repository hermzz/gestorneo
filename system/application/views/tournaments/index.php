<?php if($this->tank_auth->is_admin()): ?>
	<p><?=_('Add a <a href="/tournament/create/">new tournament</a>');?></p>
<?php endif; ?>

<h2><?=_('Upcoming tournaments');?></h2>

<?php if($future_tournaments): ?>
	<ul>
		<?php foreach($future_tournaments->result() as $tournament): ?>
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), site_url('/tournament/view/'.$tournament->id), $tournament->name, strftime('%a %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
				(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
					[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
					<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No upcoming tournaments found.');?></p>
<?php endif; ?>

<h2><?=_('Past tournaments');?></h2>

<?php if($past_tournaments): ?>
	<ul>
		<?php foreach($past_tournaments->result() as $tournament): ?>
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), site_url('/tournament/view/'.$tournament->id), $tournament->name, strftime('%a %e, %B %Y', mysql_to_unix($tournament->start_date)));?>
				(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
					[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
					<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No past tournaments found.');?></p>
<?php endif; ?>
