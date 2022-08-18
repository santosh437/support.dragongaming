<?php

/**
 * Template Name: bbPress - User Login
 *
 * @package bbPress
 * @subpackage Theme
 */

if ( is_user_logged_in() ) {
	header("Location: https://support.dragongaming.com");
} else { 

get_header(); ?>

<style>
	#site-header, #site-footer-widgets, #site-footer {display: none;}
	#ht-site-container, body {background: #131722; }
</style>

<div class="login-page">
  <div class="form">
    <div class="register-form toggle-form">
      <?php echo do_shortcode('[user_registration_form id="24"]'); ?>
      <p class="message">Already registered? <a href="#">Sign In</a></p>
    </div>
    <div class="login-form toggle-form">
	   
	   <?php if (strpos($_SERVER['REQUEST_URI'], "failed") !== false){ ?>
		<small>Incorrect login details</small>
		<?php } ?>
		<?php wp_login_form(); ?>
		<a href="/lost-password">Forgot your password?</a>
		<p class="message">Not registered? <a href="#">Create an account</a></p>
    </div>
  </div>
</div>

<?php get_footer(); } ?>