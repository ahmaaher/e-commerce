$(function() {
	"use strict";
	//Hide placeholder on form focus
	$("[placeholder]").focus(function() {
		$(this).attr("data-text", $(this).attr("placeholder"));
		$(this).attr("placeholder", "");
	}).blur(function() {
		$(this).attr("placeholder", $(this).attr("data-text"));
	})

	// Add asterisk after input fields
	$('input').each(function() {
		if($(this).attr('required') === 'required') {
			$(this).after('<span class="asterisk">*</span>');
		}
	});


	// test code, trying to do the show-hide password by click function .....
	/*
		$('.show-pass').click(function(){
			$('.password').attr('type', function(index, attr){ return attr == 'passwotd' ? 'text' : 'password';});
		});
	*/

	// Show-hide password field by hovering the eye icon
	$('.show-pass').hover(
		function() {$('.password').attr('type', 'text');},
		function() {$('.password').attr('type', 'password');}
	);

	// Confirmation message
	$('.confirm').click(function() {
		return confirm("Are you sure you want to do this ?");
	});

	// Showing login & signup forms
	$('.login-signup h3 span').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('.login-signup form').hide();
		$('.' + $(this).data('class')).show();
	});

	// Making live demo for adding item by custom attribute (data-class)
	$('.cr-ads form .live').keyup(function(){
		$($(this).data('anything')).text($(this).val());
	});
});