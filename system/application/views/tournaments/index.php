<?php if($this->tank_auth->is_admin()): ?>
	<ul class="nav nav-tabs">
		<li class="dropdown pull-left" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="/tournament/create/"><?=_('New tournament');?></a></li>
			</ul>
		</li>
	</ul>
<?php endif; ?>

<h2><?=_('Upcoming tournaments');?></h2>

<?php if($future_tournaments): ?>
	<?php $year = false; ?>
		<?php foreach($future_tournaments as $tournament): ?>
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
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), 
					site_url('/tournament/view/'.$tournament->id), 
					$tournament->name, 
					strftime('%A %B %e', mysql_to_unix($tournament->start_date)));?>
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
	<?php $year = false; ?>
		<?php foreach($past_tournaments as $tournament): ?>
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
			<li>
				<?=sprintf(_('<a href="%s">%s</a> on %s'), 
					site_url('/tournament/view/'.$tournament->id), 
					$tournament->name, 
					strftime('%A %e %B', mysql_to_unix($tournament->start_date)));?>
				(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
					[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
					<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No past tournaments found.');?></p>
<?php endif; ?>
