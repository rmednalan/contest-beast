var ContestBeastDependencyBinder = (function() {
	var self = {}, bound_dependency_elements = [], $ = jQuery;

	self.bind_dependency_elements = function() {
		$('[data-dependency][data-dependency-value]').each(function() {
			var $this = $(this);

			var dependency_name = $this.attr('data-dependency');

			if(0 > $.inArray(dependency_name, bound_dependency_elements)) {
				bound_dependency_elements.push(dependency_name);

				$('[name="' + dependency_name + '"]').live('change click', function(event) {
					var $element = $(this);

					if($element.is(':checked') || !($element.is('[type="radio"]') || $element.is('[type="checkbox"]'))) {
						var value = $element.val();

						var $dependencies = $('[data-dependency="' + dependency_name + '"]').hide();

						$dependencies.filter('[data-dependency-value="' + value + '"]').show();
					} else if($element.is('[type="checkbox"]')) {
						$('[data-dependency="' + dependency_name + '"]').hide();
					}
				}).change();
			}
		});
	}
	return self;
})();

jQuery(document).ready(function($) {
	$('#contest-beast-skin').change(function(event) {
		var $selector = $(this),
			$preview = $('#contest-beast-skin-preview'),
			url = $selector.find('option:selected').data('url');

		$preview.attr('src', url);
	}).change();

	ContestBeastDependencyBinder.bind_dependency_elements();

	var $contest_beast_settings_from = $('#contest-beast-settings-form').size() > 0 ? $('#contest-beast-settings-form') : $('#post');
	var contest_beast_settings_form_submit_options = {
		data: { action: 'contest_beast_get_mailing_list_markup' },
		resetForm: false,
		success: function(data, status, xhr, $form) {

		},
		type: 'POST',
		url: ajaxurl
	};

	var contest_beast_has_initialized_mailing_list_provider = false;
	$('#contest-beast-mailing-list-provider').change(function(event) {
		var mailing_list_provider = $(this).val();

		if('none' == mailing_list_provider) {
			$('.contest-beast-opt-in-code-dependent').hide();
			$('.contest-beast-mailing-list-details-container').hide();
		} else if('aweber' == mailing_list_provider || 'mailchimp' == mailing_list_provider) {
			$('.contest-beast-opt-in-code-dependent').hide();
			$('.contest-beast-mailing-list-details-container').hide().filter('.contest-beast-mailing-list-' + mailing_list_provider + '-details-container').show();
		} else if('other' == mailing_list_provider) {
			$('#contest-beast-opt-in-code').change();
			$('.contest-beast-mailing-list-details-container').hide();
		}

		if(contest_beast_has_initialized_mailing_list_provider) {
			var $active_contest = $('#contest-beast-has-active-contest');
			var has_active_contest = $active_contest.val() == 1;
			if(has_active_contest && !confirm('You have ongoing events that use your previously selected mailing list provider. If you change this value, those contests will use your newly selected mailing list provider. Is this OK?')) {
				event.preventDefault();
				event.stopPropagation();

				$(this).find('option.last-selected').attr('selected', 'selected');
			} else {
				$active_contest.val(0);
			}
		}

		$(this).find('option').removeClass('last-selected').filter(':selected').addClass('last-selected');
	}).change();
	contest_beast_has_initialized_mailing_list_provider = true;

	$('#contest-beast-mailing-list-provider, #contest-beast-aweber-authorization, #contest-beast-mailchimp-api-key, #contest-beast-mailing-list-list').live('change', function(event) {
		var $this = $(this);
		if($this.is('#contest-beast-mailing-list-list') && $('#contest-beast-mailing-list-provider').val() != 'mailchimp') {
			return;
		}

		var $ajax_loaders = $('#contest-beast-mailing-list-list-ajax, #contest-beast-mailing-list-name-ajax').css('visibility', 'visible').show()
		, $containers = $('#contest-beast-mailing-list-list-container, #contest-beast-mailing-list-name-container').hide()
		, options = contest_beast_settings_form_submit_options;

		options.success = function(data, status, xhr, $form) {
			var $new_markup = $(data)
			, $new_list = $new_markup.find('#contest-beast-mailing-list-list-container')
			, $new_name = $new_markup.find('#contest-beast-mailing-list-name-container');

			$('#contest-beast-mailing-list-list-container').replaceWith($new_list);
			$('#contest-beast-mailing-list-name-container').replaceWith($new_name);

			$ajax_loaders.css('visibility', 'hidden').hide();
			$containers.show();
		};

		if($('#title').size() > 0) {
			options.data['contest-beast-is-edit-page'] = 1;
		}

		$contest_beast_settings_from.ajaxSubmit(options);
	});

	$('#contest-beast-opt-in-code').change(function(event) {
		var $this = $(this)
		, $dependents = $('.contest-beast-opt-in-code-dependent')
		, $parent = $this.parents('td')
		, form_code = $.trim($this.val());

		$parent.find('.contest-beast-opt-in-form-field').remove();
		if('' == form_code) {
			$dependents.hide();
		} else {
			$dependents.show();
			$parsed_form = $(form_code);

			var $form = $parsed_form.find('form');
			if(0 == $form.size()) {
				$form = $parsed_form.filter('form');
			}
			if(0 == $form.size()) {
				alert('Contest Beast could not find a form element in the Opt In Form Code you entered. Please copy the entire HTML code block from your mailing list provider into the Opt In Form Code field.');
				$dependents.hide();
				return;
			}

			var form_action = $form.attr('action');

			$('#contest-beast-opt-in-form-url').val(form_action);

			var hidden_inputs = {}
			, other_inputs = []
			, lowercased = ''
			, email_input_name = ''
			, name_input_name = '';

			var $inputs = $parsed_form.find('input[type!="submit"]').each(function(index, input) {
				var $input = $(input)
				, input_name = $input.attr('name')
				, input_type = $input.attr('type')
				, input_value = $input.val();

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

			$.each(hidden_inputs, function(index, hidden_input) {
				var $hidden_input = $('<input class="contest-beast-opt-in-form-field" type="hidden" name="contest-beast[opt-in-form-fields-hidden][' + index + ']" />').val(hidden_input).appendTo($parent);
			});
			$.each(other_inputs, function(index, other_input) {
				var $other_input = $('<input class="contest-beast-opt-in-form-field" type="hidden" name="contest-beast[opt-in-form-fields][]" />').val(other_input).appendTo($parent);
			});

			$('.contest-beast-opt-in-code-fields').each(function(index, select) {
				var $select = $(select)
				, previous_value = $select.val()
				, splash_field = $select.attr('data-contest-beast-field');

				$select.empty();

				$.each(other_inputs, function(other_inputs_index, other_input) {
					$select.append($('<option></option>').attr('value', other_input).text(other_input));
				});

				if('' != previous_value && -1 < $.inArray(previous_value, other_inputs)) {
					$select.val(previous_value);
				} else {
					if('email' == splash_field && '' != email_input_name) {
						$select.val(email_input_name);
					} else if('name' == splash_field && '' != name_input_name) {
						$select.val(name_input_name);
					}
				}
			});
		}
	});

	$('#contest-beast-opt-in-form-disable-name').bind('change click', function(event) {
		var $this = $(this)
		, $name_field = $('#contest-beast-opt-in-form-name-field');

		if($this.is(':checked')) {
			$name_field.attr('disabled', 'disabled');
		} else {
			$name_field.removeAttr('disabled');
		}
	}).change();

	$('.contest-branding-logo-toggle').click(function(event) {
		event.preventDefault();

		$('#contest-beast-branding-logo-action').val($(this).attr('data-branding-logo-action')).change();
	});

	/// EDITING SCREEN

	$('#contest-beast-add-to-mailing-list').bind('change click', function(event) {
		var $this = $(this)
		, $entry_fields = $('.contest-beast-mailing-list-details-container')
		, mailing_list_provider = $('#contest-beast-mailing-list-provider').val();

		$entry_fields.hide();
		if($this.is(':checked')) {
			$entry_fields.filter('.contest-beast-mailing-list-' + mailing_list_provider + '-details-container').show();
		}
	}).change();

	$('.contest-beast-date').datepicker();

	$('.contest-beast-choose-winners').live('click', function(event) {
		event.preventDefault();

		var $button = $(this)
		, $container = $button.parents('.contest-beast-winners-box')
		, $ajax_feedback = $container.find('.contest-beast-winner-action-ajax');

		$ajax_feedback.insertAfter($button).css({ visibility: 'visible', top: '-4px', 'margin-left': '5px', position: 'relative' }).show();

		$.post(
			ajaxurl,
			{
				action: 'contest_beast_choose_winners',
				'contest-id': $button.attr('data-contest-id'),
				method: $container.find('.contest-beast-winners-method').val()
			},
			function(data, status) {
				$container.replaceWith(data);
			}
		);
	});

	$('.contest-beast-disqualify-submission').live('click', function(event) {
		event.preventDefault();

		var $link = $(this)
		, $container = $link.parents('.contest-beast-winners-box')
		, $ajax_feedback = $container.find('.contest-beast-winner-action-ajax');

		$ajax_feedback.insertAfter($link).css({ visibility: 'visible', top: '-3px', 'margin-left': '5px', position: 'relative' }).show();

		$.post(
			ajaxurl,
			{ action: 'contest_beast_replace_winner', 'contest-id': $link.attr('data-contest-id'), 'submission-id': $link.attr('data-submission-id') },
			function(data, status) {
				$container.replaceWith(data);
			}
		);
	});
});