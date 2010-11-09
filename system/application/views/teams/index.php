<h2><?=_('Teams');?></h2>

<p><a href="/team/create"><?=_('Create a new team');?></a></p>

<?php if($teams): ?>
	<ul>
		<?php foreach($teams->result() as $team): ?>
			<li><a href="<?=site_url('/team/view/'.$team->id)?>"><?=$team->name?></a></li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p><?=_('No teams found.');?></p>
<?php endif; ?>
