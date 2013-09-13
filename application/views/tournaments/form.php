<h2><?= $title ?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		// datepicker defaults
		$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
		$.datepicker.setDefaults({
			firstDay: 1,
			dateFormat: 'dd/mm/yy',
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      selectOtherMonths: true
		});

		// datepicker selections
		// start_date launches other updates
		$("#start_date_picker")
		.datepicker({
			onSelect: function(dateText, inst) {
				$('#start_date').val(dateText).keyup();
			}
		});

		$("#end_date_picker").datepicker({
			onSelect: function(dateText, inst) {
				$('#end_date').val(dateText);
			}
		});


		$("#signup_deadline_picker").datepicker({
			onSelect: function(dateText, inst) {
				$('#signup_deadline').val(dateText);
			}
		});

		// handle date input field changes
		$(".inline-date input").keyup(function(){
			var id = $(this).attr('id');

			$("#"+id+"_picker").datepicker("setDate", $(this).val());

			// start_date launches other updates
			if(id == 'start_date') {
				dateText = $(this).val();
				var dparts = dateText.split("/");
				var dd = new Date(dparts[2], dparts[1]-1, dparts[0]);
				var edate = dd;
				edate.setDate(edate.getDate() + 7 - edate.getDay());
				edate = padJsDate(edate.getDate()) + '/' + padJsDate(edate.getMonth() + 1) + '/' + edate.getFullYear();
				$('#end_date').val(edate);
				$('#end_date_picker').datepicker("option", "minDate", dateText).datepicker("setDate", edate);

				var ddate = dd;
				ddate.setDate(ddate.getDate() - 70);
				ddate = padJsDate(ddate.getDate()) + '/' + padJsDate(ddate.getMonth() +1) + '/' + ddate.getFullYear();
				$('#signup_deadline').val(ddate);
				$('#signup_deadline_picker').datepicker("setDate", ddate).datepicker("option", "maxDate", dateText);
			}
		});

<?php if(('' != set_value('start_date')) or isset($tournament->start_date)) : ?>
		$('#start_date').val('<?= isset($tournament->start_date) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->start_date)) : set_value('start_date');?>').keyup();
		$('#end_date').val('<?= isset($tournament->end_date) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->end_date)) : set_value('end_date');?>').keyup();
		$('#signup_deadline').val('<?= isset($tournament->signup_deadline) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->signup_deadline)) : set_value('signup_deadline');?>').keyup();
<?php endif; ?>

		// datepicker language from cookie
		if($.cookie("language") !== 'en') {
			$(".hasDatepicker").datepicker( "option", $.datepicker.regional[ $.cookie("language") ] );
		}

		// teams autocomplete
		$('input[name="teams_autocomplete"]').autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '/ajax/team_autocomplete',
					dataType: "jsonp",
					data: {
						term: request.term
					},
					success: function(data)
					{
						if(data.success)
						{
							response($.map(data.results, function(item)
							{
								return $("li.teams.r-"+item.id).length ? null : {
									label: item.name,
									value: item.id
								}
							}));
						} else {
							console.log('fail');
						}
					}
				});
			},
			select: function(event, ui)
			{
				if($('#teams_container .teams').length == 0)
				{
					$('#teams_container').html('');
				}

				$('#teams_container').append(
					'<li class="teams r-'+ui.item.value+'">' + ui.item.label +
					'<input type="hidden" name="teams[]" value="'+ui.item.value+'" /'+'>' +
					' [<a href="#">x</a>]</li>'
				);

			},
			close: function() {
				$('input[name="teams_autocomplete"]').val('');
			}
		});

		// remove an item from the teams list
		$('#teams_container [class*=r-] a').live("click", function (e) {
			$(e.target).parent().remove();

			if($('#teams_container .teams').length == 0)
			{
				$('#teams_container').html('<li><?= str_replace("'", "\'", _('No teams selected')) ?></li>');
			}

			return false;
		});

		// admins autcomplete
		$('input[name="players_autocomplete"]').autocomplete({
			source: function(request, response) {
				$.ajax({
					url: '/ajax/player_autocomplete',
					dataType: "jsonp",
					data: {
						term: request.term
					},
					success: function(data)
					{
						if(data.success)
						{
							response($.map(data.results, function(item)
							{
								return $("li.players.r-"+item.id).length ? null : {
									label: item.name,
									value: item.id
								}
							}));
						} else {
							console.log('fail');
						}
					}
				});
			},
			select: function(event, ui)
			{
				if($('#players_container .players').length == 0)
				{
					$('#players_container').html('');
				}

				$('#players_container').append(
					'<li class="players r-'+ui.item.value+'">' + ui.item.label +
					'<input type="hidden" name="admins[]" value="'+ui.item.value+'" /'+'>' +
					' [<a href="#">x</a>]</li>'
				);
			},
			close: function() {
				$('input[name="players_autocomplete"]').val('');
			}
		});

		// remove an admin from the list
		$('#players_container [class*=r-] a').live('click', function (e) {
			$(e.target).parent().remove();

			if($('#players_container .players').length == 0)
			{
				$('#players_container').html('<li><?= str_replace("'", "\'", _('No players selected')) ;?></li>');
			}

			return false;
		});

		// notes preview (set to same size as textarea)
		$("#notes_preview-link").click(function(e){
			e.preventDefault();
			setNotesView(this);
			$("#notes_preview")
				.load('<?= site_url("/misc/markdown_preview") ?>', {markdown: $("#notes").val()})
				.css('min-height', $("#notes").height());
		});
		// notes textarea
		$("#notes-link").addClass('disabled').click(function(e){
			e.preventDefault();
			setNotesView(this);
		});
		// notes markdown
		$("#markdown_help-link").click(function(e){
			e.preventDefault();
			setNotesView(this);
		});
		// generic set notes view
		function setNotesView(t) {
			$(".notes-section .span10").hide().filter("#"+t.id.replace("-link", "")).show();
			$(".notes-section a.btn").removeClass('disabled').filter("#"+t.id).addClass('disabled');
		}
	});
</script>

<?php if( '' != validation_errors()) : ?>
<div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
	<?= validation_errors() ?>
</div>
<?php endif; ?>

<form action="#" id="tournament_form" method="post" class="well">
	<fieldset>
		<div class="clearfix">
			<label for="name"><?=_('Name');?></label>
			<div class="input">
				<input type="text" class="form-control col-md-6" id="name" name="name" value="<?= isset($tournament->name) ? $tournament->name : set_value('name');?>" />
			</div>
		</div>

		<div class="clearfix inline-date">
			<label for="start_date"><?=_('Start date');?></label>
			<div class="input">
				<input type="text" class="form-control col-md-2" id="start_date" name="start_date" value="<?= isset($tournament->start_date) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->start_date)) : set_value('start_date');?>" />
			</div>
			<div id="start_date_picker"></div>
		</div>

		<div class="clearfix inline-date">
			<label for="end_date"><?=_('End date');?></label>
			<div class="input">
				<input type="text" class="form-control col-md-2" id="end_date" name="end_date" value="<?= isset($tournament->end_date) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->end_date)) : set_value('end_date');?>" />
			</div>
			<div id="end_date_picker"></div>
		</div>

		<div class="clearfix inline-date">
			<label for="signup_deadline"><?=_('Signup deadline');?></label>
			<div class="input">
				<input type="text" class="form-control col-md-2" id="signup_deadline" name="signup_deadline" value="<?= isset($tournament->signup_deadline) ? strftime('%d/%m/%Y', mysql_to_unix($tournament->signup_deadline)) : set_value('signup_deadline');?>" />
			</div>
			<div id="signup_deadline_picker"></div>
		</div>

		<div class="clearfix notes-section">
			<label for="notes"><?=_('Notes');?></label>
			<div class="btn-toolbar">
			  <div class="btn-group">
			    <a class="btn btn-default" href="#" id="notes-link"><i class="glyphicon glyphicon-pencil"></i><?=_('edit notes');?></a>
			    <a class="btn btn-default" href="#" id="notes_preview-link"><i class="glyphicon glyphicon-eye-open"></i><?=_('preview notes');?></a>
			    <a class="btn btn-default" href="/misc/page/markdown_help" id="markdown_help-link" target="_blank"><i class="glyphicon glyphicon-question-sign"></i><?=_('markdown help');?></a>
			  </div>
			</div>
			<div class="input">
				<textarea id="notes" name="notes" rows="8" cols="60" class="col-md-10"><?= isset($tournament->notes) ? htmlentities(utf8_decode($tournament->notes)) : set_value('notes');?></textarea>
				<div id="notes_preview" class="col-md-10"></div>
				<div id="markdown_help" class="col-md-10"><?= $this->load->view('misc/markdown_help', '', true)?></div>
			</div>
		</div>
	</fieldset>

    <fieldset class="teams">
    	<legend><?=_('Teams');?></legend>

    	<div class="clearfix">
			<div class="input">
				<input type="text" class="form-control" name="teams_autocomplete" />

				<ul id="teams_container">
  				<?php if($teams && isset($selected_teams) && count($selected_teams)): ?>
					<?php foreach($teams as $team): ?>
						<?php if(in_array($team->id, $selected_teams)): ?>
							<li class="teams r-<?=$team->id;?>">
								<?=$team->name;?>
								<input type="hidden" name="teams[]" value="<?=$team->id;?>" />
								[<a href="#">x</a>]
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<li><?=_('No teams selected');?></li>
				<?php endif; ?>
	    	</ul>
			</div>
		</div>
    </fieldset>

    <fieldset class="admins">
    	<legend><?=_('Admins');?></legend>

    	<div class="clearfix">
			<div class="input">
				<input type="text" class="form-control" name="players_autocomplete" />

				<ul id="players_container">
				<?php if($users && isset($tournament_admins) && count($tournament_admins)): ?>
					<?php foreach($users as $user): ?>
						<?php if(in_array($user->id, $tournament_admins)): ?>
							<li class="players r-<?=$user->id;?>">
								<?=$user->username;?>
								<input type="hidden" name="admins[]" value="<?=$user->id;?>" />
								[<a href="#">x</a>]
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<li><?=_('No players selected');?></li>
				<?php endif; ?>
				</ul>
			</div>
		</div>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?= htmlspecialchars($form_action) ?>" class="btn btn-primary btn-lg" />
</form>
