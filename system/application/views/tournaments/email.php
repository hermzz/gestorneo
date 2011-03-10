<h2><?=sprintf(_('Email team for %s'), $tournament->name);?></h2>

<script>
	$(document).ready(function() {
		$('[name="submitPreviewEmail"]').click(function () {
			f = $(this).parent()[0];
			old = f.action;
			
			f.action = '/tournament/email/<?=$tournament->id;?>/preview';
			f.target = '_blank';
			f.submit();
			
			f.target = '';
			f.action = old;
			
			return false;
		});
		
		for(team_name in emails)
		{
			$('#team_list').append('<input type="checkbox" name="'+team_name+'" /'+'>'+team_name+'<br /'+'>');
		};
		
		$('#team_list input').click(function(e) {
			$('#email_list').html('&nbsp;');
			$('#team_list input').each(function(k,v) {
				if(v.checked)
				{
					for(team_name in emails)
					{
						if(v.name == team_name)
						{
							for(email in emails[team_name])
							{
								$('#email_list').append(emails[team_name][email]+', ');
							}
						}
					}
				}
			});
		});
	});

emails = {
	<?php foreach($teams as $team): ?>
		<?php if($team->players): ?>
			'<?=$team->name;?>': [
				<?php foreach($team->players as $player): ?>
					'<?=$player->email;?>',
				<?php endforeach; ?>
			],
		<?php endif;?>
	<?php endforeach; ?>
	
	<?php if($players_unassigned): ?>
		'<?=_('Unassigned players');?>': [
		<?php foreach($players_unassigned as $player): ?>
			'<?=$player->email;?>',
		<?php endforeach; ?>
		],
	<?php endif;?>
	
	<?php if($players_waiting): ?>
		'<?=_('Waiting list');?>': [
		<?php foreach($players_waiting as $player): ?>
			'<?=$player->email;?>',
		<?php endforeach; ?>
		],
	<?php endif;?>
};
	
</script> 

<?=validation_errors()?>

<form action="#" method="post">
	<label for="subject"><?=_('Subject');?></label>
    <input type="text" id="subject" name="subject" /><br />
    
    <label for="message"><?=_('Message');?></label><br />
    <textarea id="message" name="message" cols="60" rows="20"></textarea><br />
    
    <p><a href="/misc/page/markdown_help" target="_blank"><?=_('markdown help');?></a></p>

    <input type="submit" name="submitSendEmail" value="<?=_('Send');?>" />
    <input type="submit" name="submitPreviewEmail" value="<?=_('Preview');?>" />
</form>

<h3><?=_('Generate email list');?></h3>

<p id="team_list"></p>

<p id="email_list" class="neutral message">&nbsp;</p>
