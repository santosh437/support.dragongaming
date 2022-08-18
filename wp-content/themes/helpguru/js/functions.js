// Main JS File

// Start Wrapper
jQuery(document).ready(function ($) {
	$(window).load(function () {

		// Mobile Nav Menu
		$(function htMenuToggle() {

			$("#ht-nav-toggle").click(function () {
		        $("#nav-primary-menu").animate({
		            height: "toggle",
		            opacity: "toggle"
		        }, 400);
		    });
			
		});

		// HT fade in #ht-to-top
		$(function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > 100) {
					$('#ht-to-top').fadeIn('1000');
				} else {
					$('#ht-to-top').fadeOut('1000');
				}
			});

			// scroll body to 0px on click
			$('#ht-to-top').click(function () {
				$('body,html').animate({
					scrollTop: 0
				}, 800);
				return false;
			});	
		});
		
		// Login Form
		
		$('.message a').click(function(){
		   $('.toggle-form').animate({height: "toggle", opacity: "toggle"}, "slow");
		});
		
		$("#loginform #user_login").attr({"placeholder" : "User Name", "required": "required"});
		$("#loginform #user_pass").attr({"placeholder" : "Password", "required": "required"});

	});


	$('.hkb-category.card').click(function (ev) {
		$('.hkb-category.card').removeClass('active')

		if (ev.target.classList.contains('popupCloseButton') || ev.target.classList.contains('hkb-cat-link')){
			return;
		}
		$(this).addClass('active')
	});

	$('.popupCloseButton').click(function (ev) {
		ev.target.closest('.hkb-category.card').classList.remove('active');
	});

	$(document).click(function (ev) {
		var $target = $(ev.target);

		if(!$target.closest('.hkb-category.card').length &&
			$('.hkb-category.card').is(":visible")) {
			$('.hkb-category.card').removeClass('active');
		}
	});

});