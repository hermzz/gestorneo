<h2><?=_('New tournament');?></h2>

<link href="/static/css/base/ui.datepicker.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(document).ready(function(){
		$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
		$.datepicker.setDefaults({
			firstDay: 1,
			dateFormat: 'dd/mm/yy',
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
      selectOtherMonths: true
		});


		$("#start_date_picker")
		.datepicker({
			onSelect: function(dateText, inst) {
				$('#start_date').val(dateText);

				var dparts = dateText.split("/");
				var dd = new Date(dparts[2], dparts[1]-1, dparts[0]);
				var edate = dd;
				edate.setDate(edate.getDate() + 7 - edate.getDay());
				edate = edate.getDate() + '/' + (edate.getMonth() + 1) + '/' + edate.getFullYear();
				$('#end_date_picker').datepicker("option", "minDate", dateText).datepicker("setDate", edate);
				$('#end_date').val(edate);

				var ddate = dd;
				ddate.setDate(ddate.getDate() - 70);
				ddate = ddate.getDate() + '/' + (ddate.getMonth() +1) + '/' + ddate.getFullYear();
				$('#deadline_date').val(ddate);
				$('#deadline_date_picker').datepicker("setDate", ddate).datepicker("option", "maxDate", dateText);
			}
		});

		$("#end_date_picker").datepicker({
			onSelect: function(dateText, inst) {
				$('#end_date').val(dateText);
			}
		});


		$("#deadline_date_picker").datepicker({
			onSelect: function(dateText, inst) {
				$('#deadline_date').val(dateText);
			}
		});

		if($.cookie("language") !== 'en') {
			$(".hasDatepicker").datepicker( "option", $.datepicker.regional[ $.cookie("language") ] );
		}

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
								return {
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

				$('#teams_container .r-'+ ui.item.value+' a').click(function (e) {
					$(e.target).parent().remove();

					return false;
				});
			},
			close: function() {
				$('input[name="teams_autocomplete"]').val('');
			}
		});

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
								return {
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

				$('#players_container .r-'+ ui.item.value+' a').click(function (e) {
					$(e.target).parent().remove();

					return false;
				});
			},
			close: function() {
				$('input[name="players_autocomplete"]').val('');
			}
		});

		$("#notes_preview-link").click(function(e){
			e.preventDefault();
			setNotesView(this);
			$("#notes_preview")
				.load('<?= site_url("/misc/markdown_preview") ?>', {markdown: $("#notes").val()})
				.css('min-height', $("#notes").height());
		});

		$("#notes-link").addClass('disabled').click(function(e){
			e.preventDefault();
			setNotesView(this);
		});

		$("#markdown_help-link").click(function(e){
			e.preventDefault();
			setNotesView(this);
		});

		function setNotesView(t) {
			$(".notes-section .span10").hide().filter("#"+t.id.replace("-link", "")).show();
			$(".notes-section a.btn").removeClass('disabled').filter("#"+t.id).addClass('disabled');
		}
	});
</script>

<?=validation_errors()?>

<form action="#" id="tournament_form" method="post" class="well">
	<fieldset>
		<div class="clearfix">
			<label for="name"><?=_('Name');?></label>
			<div class="input">
				<input type="text" id="name" name="name" class="span6" value="<?=set_value('name');?>" />
			</div>
		</div>

		<div class="clearfix inline-date">
			<label for="start_date"><?=_('Start date');?></label>
			<div class="input">
				<input type="text" id="start_date" name="start_date" class="span2" value="<?=set_value('start_date');?>" />
			</div>
			<div id="start_date_picker"></div>
		</div>

		<div class="clearfix inline-date">
			<label for="end_date"><?=_('End date');?></label>
			<div class="input">
				<input type="text" id="end_date" name="end_date" class="span2" value="<?=set_value('end_date');?>" />
			</div>
			<div id="end_date_picker"></div>
		</div>

		<div class="clearfix inline-date">
			<label for="deadline_date"><?=_('Signup deadline');?></label>
			<div class="input">
				<input type="text" id="deadline_date" name="deadline_date" class="span2" value="<?=set_value('deadline_date');?>" />
			</div>
			<div id="deadline_date_picker"></div>
		</div>

		<div class="clearfix notes-section">
			<label for="notes"><?=_('Notes');?></label>
			<div class="btn-toolbar">
			  <div class="btn-group">
			    <a class="btn" href="#" id="notes-link"><i class="icon-pencil"></i><?=_('edit notes');?></a>
			    <a class="btn" href="#" id="notes_preview-link"><i class="icon-eye-open"></i><?=_('preview notes');?></a>
			    <a class="btn" href="/misc/page/markdown_help" id="markdown_help-link" target="_blank"><i class="icon-question-sign"></i><?=_('markdown help');?></a>
			  </div>
			</div>
			<div class="input">
				<textarea id="notes" name="notes" rows="8" cols="60" class="span10"><?=set_value('notes');?></textarea>
				<div id="notes_preview" class="span10"></div>
				<div id="markdown_help" class="span10"><?= $this->load->view('misc/markdown_help', '', true)?></div>
			</div>
		</div>
	</fieldset>

    <fieldset class="teams">
    	<legend><?=_('Teams');?></legend>

    	<div class="clearfix">
			<div class="input">
				<input type="text" name="teams_autocomplete" />

				<ul id="teams_container">
    				<li><?=_('No teams selected');?></li>
		    	</ul>
			</div>
		</div>
    </fieldset>

    <fieldset class="admins">
    	<legend><?=_('Admins');?></legend>

    	<div class="clearfix">
			<div class="input">
				<input type="text" name="players_autocomplete" />

				<ul id="players_container">
					<li><?=_('No players selected');?></li>
				</ul>
			</div>
		</div>
    </fieldset>

    <input type="submit" name="submitNewTournament" value="<?=_('Add');?>" class="btn btn-primary btn-large" />
</form>
