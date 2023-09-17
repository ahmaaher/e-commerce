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

	// Dashboard toggle latest members & latest items lists
	$('.toggle-list').click(function(){
		$(this).toggleClass('.selected').parent().next('.panel-body').fadeToggle(100);
		if($(this).hasClass('.selected')){
			$(this).html('<i class="fa fa-plus"></i>');
		}else{ $(this).html('<i class="fa fa-minus"></i>'); }
	});

	// Showing delete button on child category section
	$('.child_listed_cats').hover(function(){
		$(this).find('.delete_child_btn').fadeIn(100);
	}, function(){
		$(this).find('.delete_child_btn').fadeOut(200);
	});



});