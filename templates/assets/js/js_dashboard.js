jQuery(document).ready(function($) {
	
	$('.datepicker').datepicker({minDate: 0});
	
	$('table.dataTable').dataTable({"searching": false, "paging": false, "language": {"info": ""}});
	
	var copy_sel = $('.the_clipboard');
	// Disables other default handlers on click (avoid issues)
	copy_sel.on('click', function(e) {
		e.preventDefault();
	});
	// Apply clipboard click event
	$('.the_clipboard').clipboard({
		path: 'wp-content/plugins/contest-beast/templates/assets/swf/jquery.clipboard.swf',
		copy: function() {
			labdevs_show_settings_saved();
			return $(this).parent().find('.spy_link_permalink').val();
		}
	});
	
	$("#post_title").change(function(e) {
		$.post(ajaxurl,
			{
				action: 'gen_contest_url',
				title: $('#post_title').val()
			},
			function( data ) {
				if ( data != '-1' ) {
					$('#url').val(data);
					$('#thankyou-page').html($('#url_prefix').val() + data);
				}
			}
		);
	});
	//popup update contest

	$("#mcapiverify").click(function(e) {
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action:'verify_mailchimp',mcapi:$('#mcApiKey').val()}
		}).done(function( response ) {
			
			console.log(response);
			$('#mclistIds').html(response);

		}); 
	});
	
	$("#getresponseverify").click(function(e) {
		console.log('fasfasd');
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action:'verify_getresponse',grapi:$('#getresponseApiKey').val()}
		}).done(function( response ) {
			
			console.log(response);
			$('#getresponselistIds').html(response);

		}); 
	});
	
	$(document).on('click', '#mailchimp_save', function(e){
		
		
		var dataSr = $('input[name^="mc_list"], input[name^="mc_name"]').serializeArray();
		var mcApiKey = $('#mcApiKey').val();
		dataSr.push({name: 'mailchimp', value: mcApiKey});
		
		dataSr.push({name: 'action', value: 'save_mailchimp'});
		var pdata = $.param(dataSr); // store json string
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: pdata
		}).done(function( response ) {
			console.log(response);
		});
	});
	
	$(document).on('click', '#getreponse_save', function(e){
	
		
		var dataSr = $('input[name^="gr_list"], input[name^="gr_name"]').serializeArray();
		var getresponseApiKey = $('#getresponseApiKey').val();
		dataSr.push({name: 'getresponse', value: getresponseApiKey});
		
		dataSr.push({name: 'action', value: 'save_getresponse'});
		var pdata = $.param(dataSr); // store json string
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: pdata
		}).done(function( response ) {
			console.log(response);
		});
	});
	
	$(document).on('click', '#aweberapiverify', function(e){
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action:'verify_aweber',_aweber_auth_code:$('#contest_beast_aweber_auth_code').val(),_aweber_list_name:$('#contest_beast_aweber_list_name').val()}
		}).done(function( response ) {
			var $msg = '';
			if(response==1){
				$msg = 'Aweber successfully configured.';
				$class = 'success';
			}else{
				$msg = 'Invalid auth code. Please try with fresh auth code or contact support.';
				$class = 'danger';
			}
			$('#aweber_message').html($msg);
			$('#aweber_message').attr('class','alert');
			$('#aweber_message').addClass('alert-'+$class);
			$('#aweber_message').show('slow');
		}); 
	});
	
	$(".doupdate_contest").click(function(e) {
		
		e.preventDefault();		
		var data_action = $(this).attr('data-action');
		var contest_post_content;
		 
		var dataSr = $('input[name^="contest-beast"], select[name^="contest-beast"], textarea[name^="contest-beast"]').serializeArray();
		
		var post_editor_content = tinyMCE.get('post_content').getContent();
		dataSr.push({name: 'post_content', value: post_editor_content});
		
		var contest_addmailing_list = [];

		var post_title = $('input[name="post_title"]').val();
		var contest_id = $('input[name="contest_id"]').val();
		
		dataSr.push({name: 'contest_id', value: contest_id});
		dataSr.push({name: 'action', value: 'contest_update_datacontest'}); 
			
		
		var contest_thank_you_content = tinyMCE.get('contest_thank_you_content').getContent();
		dataSr.push({name: 'contest_thank_you', value: contest_thank_you_content}); 

		dataSr.push({name: 'post_title', value: post_title});
		dataSr.push({name: 'post_permalink', value: post_title});
		
		$( ".addmaillist" ).each(function(index, element) {
		  contest_addmailing_list[index] = $(element).html();
		});
		
		dataSr.push({name: 'contest_mail_list', value: contest_addmailing_list});
		
 
		console.log(dataSr);
		  
		var pdata = $.param(dataSr); // store json string
		
	
   
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: pdata
		}).done(function( response ) {
			
			console.log(response);

			 if (data_action=="Update"){
				 $('#myModal .modal-body').html('Contest was successfully updated!');
				$('#myModal .btnfrt').attr('href','/my-account/dashboard/');
				$('#myModal .btnfrt').html('Back to Dashboard'); 
				
			}
			 
			 $('#myModal').modal({
					keyboard: false
				})  
			

		}); 
		 
		
		/* 
		$('input[name^="contest-beast"]').each(function(index,cl) {
			console.log(index); 
			console.log(cl); 
			console.log($(this).val()); 
		});
		$('select[name^="contest-beast"]').each(function(index,cl) {
			console.log($(this).val()); 
		}); */
		
	});
	
	//popup update contest
	$('#menuaccount').on('click', function(event) {
		$( "#profilemenus" ).toggle( "slow", function() {
			// Animation complete.
		});
	});
	
	$('#mailing').on('change', function(event) {
			
			var $this = $(this)
				, $dependents = $('.contest-beast-opt-in-code-dependent')
				, $parent = $this.parents('td')
				, form_code = $.trim($this.val());
				$parent.find('.contest-beast-opt-in-form-field').remove();
			
			
			
			if('' == form_code) {
				$dependents.hide();
			} else {
				$dependents.show();
				$parsed_form = parseOptInCode(form_code);
				console.log($parsed_form);
				
			if($parsed_form == false) {
					// alert('Contest Beast could not find a form element in the Opt In Form Code you entered. Please copy the entire HTML code block from your mailing list provider into the Opt In Form Code field.');
					$dependents.hide();
					return;
			}
			$('#contest-beast-opt-in-form-url').val($parsed_form.action);
			$('.contest-beast-opt-in-code-fields').each(function(index, select) {
				var $select = $(select)
				, previous_value = $select.val()
				, splash_field = $select.attr('data-contest-beast-field');
				$select.empty();
				$.each($parsed_form.others, function(other_inputs_index, other_input) {
				$select.append($('<option></option>').attr('value', other_input).text(other_input));
				});
					
				if('' != previous_value && -1 < $.inArray(previous_value, $parsed_form.others)) {
					$select.val(previous_value);
				} else {
					if('email' == splash_field && '' != $parsed_form.email) {
						$select.val($parsed_form.email);
					} else if('name' == splash_field && '' != $parsed_form.name) {
						$select.val($parsed_form.name);
				}
				}
			});
		}
	}).change();
	
	$(document).on('click', '.update_spy_link_button', function(e){
		
		e.preventDefault();		
		var post_id = $(this).attr('data-id');
				
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action: 'contest_dashboard_update',post_id : post_id}
		}).done(function( response ) {
			console.log(response);
			$('#contest_form_modal').html(response);
			
		});
		

		$('#model_edit_contest').modal('show');	
		
	});
	
	
	$(document).on('click', '.delete_spy_link_button', function() {
		var id = $(this).attr('data-id');
		$('#delete_spy_link_id').val( id );
		$('#myModal_2').modal('show');
	});
	
	$(document).on('click', '#delete_spy_link_button', function() {
				$('#myModal_2').modal('hide');
				$('#updating_table').show();
				var spy_link_id = $('#delete_spy_link_id').val();
				$.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'labdevs_spy_link_delete',
						spy_link_id : spy_link_id,
					}
				}).done(function( data ) {
					//update_spy_links_counter();
					$('.form-horizontal').find("input[type=hidden], input[type=text], textarea").val("");
					$.ajax({
						type: "POST",
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: {
							action: 'labdevs_spy_link_table',
						},
						beforeSend: function() {
						}
					}).done(function( data ){
						if( data === '0' ) {
							data = '';
						}
						$("#spy_link_row").html(data);
						$('#updating_table').hide();
					});
					location.reload();
				});
			});
			
	
	$("#url_prefix + .dropdown-menu li a").click(function(e) {
		e.preventDefault();
		$('#url_prefix').html($(this).text()+' <span class="caret"></span>');
		$('#url_prefix').val($(this).attr('href'));
	});
	
	
	$("#url").change(function(e) {
		e.preventDefault();
		$('#url_msg').html('');
		$('#url_checking').show();
		$.post(
			ajaxurl,
			{
				action: 'check_contest_url',
				post_id: $('#spy_link_id').val(),
				post_name: $('#url').val()
			},
			function( data ) {
				$('#url_checking').hide();
				if ( data != '-1' ) {
					if(data == 'Yes') {
						$('<span style="color:green;">URL is available</span>').appendTo('#url_msg');
						$('#thankyou-page').html($('#url_prefix').val() + $('#url').val());
					} else {
						$('<span style="color:red;">URL is not available. Used default</span>').appendTo('#url_msg');
						$('#thankyou-page').html($('#url_prefix').val() + data);
						$('#url').val(data);
					}
				}
			}
		);
	});
	
	$(document).on('change', '#contest_rules', function(){
		$( ".open_rules_content" ).toggle( "slow", function() {
			// Animation complete.
		});
	});
	
	$(document).on('change', '#contest_custom_tracking', function(){
		$( ".open_custom_tracking" ).toggle( "slow", function() {
			// Animation complete.
		});
	});
	
	$(document).on('change', '#custom_mailing', function(){
		$( ".open_mailing_content" ).toggle( "slow", function() {
			// Animation complete.
		});
	});
	

		
		$.fn.multipleInput = function() {
		var newle = 0; 
          return this.each(function() {
 
               // create html elements
 
               // list of email addresses as unordered list
               $list = $('#ullistmail');
				
               // input
               var $input = $('<input type="text" />').keyup(function(event) {
 
                    if(event.which == 32 || event.which == 188) {
                         // key press is space or comma
                        var val = $(this).val().slice(0, -1); // remove space/comma from value
					
						//validate if is email
						if( !validateEmail(val)) {
							$('#mailing-list-error').html('Invalid email address.');
						}else{
						newle++;
						$('#mailing-list-error').html('');
                         // append to list of emails with remove button
                         $list.append($('<li class="multipleInput-email"><span class="addmaillist"> ' + val + '</span></li>')
                              .append($('<a href="#" class="multipleInput-close" title="Remove" id="js'+newle+'"><i class="fa fa-times fa-1" aria-hidden="true"></i></a>'))
                         );
						 							
						}
                         $(this).attr('placeholder', '');
                         // empty input
                         $(this).val('');
                    }
 
               });
 
               // container div
			   
               var $container = $('.multipleInput-container').click(function() {
                    $input.focus();
               });
 
               // insert elements into DOM
               $container.append($list).append($input).insertAfter($(this));
 
               // add onsubmit handler to parent form to copy emails into original input as csv before submitting
               var $orig = $(this);
               $(this).closest('form').submit(function(e) {
 
                    var emails = new Array();
                    $('.multipleInput-email span').each(function() {
                         emails.push($(this).html());
                    });
                    emails.push($input.val());
 
                    $orig.val(emails.join());
 
               });
 
               return $(this).hide();
 
          });
 
     };


	$('#my_input').multipleInput(); 
	
	$(document).on('click', '.multipleInput-close', function(e){
		e.preventDefault();	
		$(this).parent().remove();
	});
	

});

function validateEmail($email) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	return emailReg.test( $email );
}	

function svrTimeDisplay(svrTime) {
	svrTime += 1;
	var date = new Date(svrTime*1000);
	var month = "0" + (date.getUTCMonth() + 1);
	var day = "0" + date.getUTCDate();
	var year = date.getUTCFullYear();
	var hours = date.getUTCHours();
	var minutes = "0" + date.getUTCMinutes();
	var seconds = "0" + date.getUTCSeconds();
	var ampm = hours >= 12 ? 'PM' : 'AM';
	hours = hours % 12;
	hours = hours % 12;
	hours = "0" + hours;
	var formattedTime = month.substr(month.length-2) + "/" + day.substr(day.length-2) + "/" + year + ' ' +
	hours.substr(hours.length-2) + ':' + minutes.substr(minutes.length-2) + ':' + seconds.substr(seconds.length-2) + ' ' + ampm;
	// document.getElementById("server-time").innerHTML = date.toGMTString();
	//document.getElementById("server-time").innerHTML = formattedTime; 
	return setInterval(formattedTime, 1000);
}
 
function parseOptInCode(code) {
	console.log(code);
	
	var form_code = jQuery.trim(code),
	$parsed_form = jQuery(form_code),
	$form = $parsed_form.find('form');
	console.log($form);
	
	if(0 == $form.size()) {
		$form = $parsed_form.filter('form');
	}
	
	if(0 == $form.size()) {
		return false;
	}
	
	var form_action = $form.attr('action'),
	hidden_inputs = {},
	other_inputs = [],
	lowercased = '',
	email_input_name = '',
	name_input_name = '';
	
	$parsed_form.find('input[type!="submit"]').each(function(index, input) {
		var $input = jQuery(input),
		input_name = $input.attr('name'),
		input_type = $input.attr('type'),
		input_value = $input.val();
		
		if('hidden' == input_type) {
			hidden_inputs[input_name] = input_value;
		} else {
			other_inputs.push(input_name);
			lowercased = input_name.toLowerCase();
			if(-1 < lowercased.indexOf('email')) {
				email_input_name = input_name;
			} else if(-1 < lowercased.indexOf('name')) {
				name_input_name = input_name;
			}
		}
	});

	return {
		'action' : form_action,
		'email'  : email_input_name,
		'name'   : name_input_name,
		'hidden' : hidden_inputs,
		'others' : other_inputs
		};
}

function labdevs_show_settings_saved() {
   jQuery('.labdevs_small_success').fadeIn(500);
   setTimeout( function(){ jQuery('.labdevs_small_success').fadeOut(500); } , 2000 );
}
function parseOptInCode(code) {
							var form_code = jQuery.trim(code),
								$parsed_form = jQuery(form_code),
								$form = $parsed_form.find('form');
							if(0 == $form.size()) {
								$form = $parsed_form.filter('form');
							}
							if(0 == $form.size()) {
								return false;
							}
							var form_action = $form.attr('action'),
								hidden_inputs = {},
								other_inputs = [],
								lowercased = '',
								email_input_name = '',
								name_input_name = '';
							$parsed_form.find('input[type!="submit"]').each(function(index, input) {
								var $input = jQuery(input),
									input_name = $input.attr('name'),
									input_type = $input.attr('type'),
									input_value = $input.val();
								if('hidden' == input_type) {
									hidden_inputs[input_name] = input_value;
								} else {
									other_inputs.push(input_name);
									lowercased = input_name.toLowerCase();
									if(-1 < lowercased.indexOf('email')) {
										email_input_name = input_name;
									} else if(-1 < lowercased.indexOf('name')) {
										name_input_name = input_name;
									}
								}
							});
							return {
								'action' : form_action,
								'email'  : email_input_name,
								'name'   : name_input_name,
								'hidden' : hidden_inputs,
								'others' : other_inputs
							};
						}