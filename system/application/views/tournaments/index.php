<div class="tabbable">
	<ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab"><?=_('Upcoming tournaments');?></a></li>
    <li><a href="#tab2" data-toggle="tab"><?=_('Past tournaments');?></a></li>
<?php if($this->tank_auth->is_admin()): ?>
		<li class="dropdown pull-left" data-dropdown="dropdown">
			<a href="#" class="dropdown-toggle">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="/tournament/create/"><?=_('New tournament');?></a></li>
			</ul>
		</li>
<?php endif; ?>
	</ul>
  <div class="tab-content">
	  <div class="tab-pane active" id="tab1">
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
								strftime('%A %B '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%#d' : '%e'), mysql_to_unix($tournament->start_date)));?>
							(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
								[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
								<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p><?=_('No upcoming tournaments found.');?></p>
			<?php endif; ?>
	  </div>
	  <div class="tab-pane" id="tab2">
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
								strftime('%A '.(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '%#d' : '%e').' %B', mysql_to_unix($tournament->start_date)));?>
							(<?=_('Players');?>: <?=$this->tournament_model->countSignedUp($tournament->id)?>
								[<?=$this->tournament_model->countSignedUp($tournament->id, 'M')?>M/
								<?=$this->tournament_model->countSignedUp($tournament->id, 'F')?>F])
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p><?=_('No past tournaments found.');?></p>
			<?php endif; ?>
	  </div>
	</div>
</div>



