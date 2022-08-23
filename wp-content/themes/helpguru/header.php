<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/custom.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/reset.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
	
    <link rel="apple-touch-icon" sizes="180x180"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/images/fav/site.webmanifest">
    <?php wp_head(); ?>
	<style>
		.dropdown-menu {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 1000;
			display: none;
			float: left;
			min-width: 160px;
			padding: 5px 0;
			margin: 2px 0 0;
			font-size: 14px;
			text-align: left;
			list-style: none;
			background-color: #000000;
			-webkit-background-clip: padding-box;
			background-clip: padding-box;
			border: 1px solid #ccc;
			border: 1px solid rgba(0,0,0,.15);
			border-radius: 4px;
			-webkit-box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
			box-shadow: 0 6px 12px rgb(0 0 0 / 18%);
		}
		a, a:visited, .bbp-author-name {
			color: #f5f8fa;
		}
		@media (max-width: 767px)
.navbar-nav .open .dropdown-menu {
    position: static;
    float: none;
    width: auto;
    margin-top: 0;
    background-color: #000000;
    border: 0;
    -webkit-box-shadow: none;
    box-shadow: none;
}
	</style>
</head>
<body <?php body_class(''); ?> itemtype="http://schema.org/WebPage" itemscope="itemscope">

<!-- #ht-site-container -->
<div id="ht-site-container" class="clearfix ht-layout-<?php echo get_theme_mod('ht_setting_widthmode', 'fullwidth') ?>">

    <div class="header-wrapper">
        <div class="ht-container">
            <div class="ht-container-sub-wrapper">
                <header class="cd-morph-dropdown">
					<div class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <div class="navbar-header" style="padding-right:10px;">
						<a class="" title="<?php bloginfo('name'); ?>"
                           href="<?php echo home_url(); ?>">
                            <img alt="<?php bloginfo('name'); ?>" src="<?php echo ht_theme_logo(); ?>"
                                 width="175"/>
                            <?php if (is_front_page()) { ?>
                                <h1 class="site-title" itemprop="headline"><?php bloginfo('name'); ?></h1>
                            <?php } ?>
                        </a>
                        <button class="navbar-toggle" data-target="#mobile_menu" data-toggle="collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>

                    </div>

                    <div class="navbar-collapse collapse" id="mobile_menu">
                        <ul class="nav navbar-nav">
							<li>
								<a title="<?php bloginfo('name'); ?>"
									href="<?php echo home_url(); ?>">
									Home
								</a>
							</li>
                            <?php if (have_rows('menu_item', 'options')): while (have_rows('menu_item', 'options')): the_row();
								$top_level_link = get_sub_field('link')
							?>
                            <li>
								<a href="#" class="<?php echo (have_rows('dropdown_items')) ? 'dropdown-toggle' : '' ?>" data-toggle="<?php echo (have_rows('dropdown_items')) ? 'dropdown' : '' ?>">
									<?php echo $top_level_link['title'] ?> <span class="<?php echo (have_rows('dropdown_items')) ? 'caret' : '' ?>"></span>
								</a>
								
									<ul class="dropdown-menu">
										<?php $cnt = 0;
											if (have_rows('dropdown_items')): while (have_rows('dropdown_items')): the_row();
											$link = get_sub_field('link');
										
										?>
											<a href="<?php echo $link['url'] ?>" class="menu-link"><?php echo $link['title'] ?></a><br>
										<?php endwhile;endif; ?>
									</ul>
								
                            </li>
                            <?php endwhile; endif; ?>
                        </ul>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
                </header>
            </div>
        </div>
    </div>
    <script src="<?php echo get_template_directory_uri() . '/js/nav.js' ?>"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
