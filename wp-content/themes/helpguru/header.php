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
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style-nav.css">
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style_nav_new.css">
    <link rel="apple-touch-icon" sizes="180x180"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16"
          href="<?php echo get_template_directory_uri(); ?>/images/fav/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/images/fav/site.webmanifest">
    <?php wp_head(); ?>
</head>
<body <?php body_class(''); ?> itemtype="http://schema.org/WebPage" itemscope="itemscope">

<!-- #ht-site-container -->
<div id="ht-site-container" class="clearfix ht-layout-<?php echo get_theme_mod('ht_setting_widthmode', 'fullwidth') ?>">

    <div class="header-wrapper">
        <div class="ht-container">
            <div class="ht-container-sub-wrapper">
                <header class="cd-morph-dropdown">


                    <div class="nav-mobile">
                        <a class="" title="<?php bloginfo('name'); ?>"
                           href="<?php echo home_url(); ?>">
                            <img alt="<?php bloginfo('name'); ?>" src="<?php echo ht_theme_logo(); ?>"
                                 width="175"/>
                            <?php if (is_front_page()) { ?>
                                <h1 class="site-title" itemprop="headline"><?php bloginfo('name'); ?></h1>
                            <?php } ?>
                        </a>

                        <a href="#0" class="nav-trigger">Open Nav<span aria-hidden="true"></span></a>
                    </div>

                    <div class = "main-wrapper">
						<nav class = "navbar">
							<div class = "navbar-collapse">
								<ul class = "navbar-nav">
									<li class="nav-logo" data-content="">
										<a class="d-flex align-items-center" title="<?php bloginfo('name'); ?>"
											href="<?php echo home_url(); ?>">
											<img alt="<?php bloginfo('name'); ?>" src="<?php echo ht_theme_logo(); ?>"
												width="240"/>
											<?php if (is_front_page()) { ?>
												<h1 class="site-title" itemprop="headline"><?php bloginfo('name'); ?></h1>
											<?php } ?>
										</a>
									</li>
									<?php if (have_rows('menu_item', 'options')): while (have_rows('menu_item', 'options')): the_row();
										$top_level_link = get_sub_field('link')
									?>

									<li>
										<a href = "#" class = "menu-link">
											<?php echo $top_level_link['title'] ?>
											<?php if (have_rows('dropdown_items')): ?>
												<span style="margin-left: 2px">
													<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
														 fill="currentColor"
														 class="bi bi-chevron-down" viewBox="0 0 16 16"><path
																fill-rule="evenodd"
																d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
													</svg>
												</span>
											<?php endif; ?>
										</a>
										<?php if (have_rows('dropdown_items')): ?>
										<div class = "sub-menu">
											<!-- item -->
											<div class = "sub-menu-item">
												<ul>
													 <?php $cnt = 0;
														if (have_rows('dropdown_items')): while (have_rows('dropdown_items')): the_row();
                                                        $link = get_sub_field('link');
														
														?>
														<div class="row">
															<?php if ($cnt == 3): ?><?php echo "hi"; ?>
																</div><div class="row">
															<?php endif; ?>
															<div class="col">
																<li>
																	<a href="<?php echo $link['url'] ?>" class="menu-link"><?php echo $link['title'] ?></a>	
																</li>
															</div>
														</div>
                                                    <?php $cnt = $cnt+1; endwhile;endif; ?>
												</ul>
											</div>
											<!-- end of item -->
										</div>
										<?php endif; ?>
										
									</li>
									<?php endwhile; endif; ?>
								</ul>
							</div>
						</nav>
					</div>

                    <div class="morph-dropdown-wrapper">
                        <div class="dropdown-list">
                            <ul>
                                <?php if (have_rows('menu_item', 'options')): while (have_rows('menu_item', 'options')): the_row();
                                    $top_level_link = get_sub_field('link')
                                    ?>
                                    <?php if (have_rows('dropdown_items')): ?>
                                        <li id="<?php echo str_replace(' ', '-', strtolower($top_level_link['title'])) ?>"
                                            class="dropdown links">
                                            <a href="#" class="label"><?php echo $top_level_link['title'] ?></a>
                                            <div class="content">
                                                <ul class="links-list">
                                                    <?php if (have_rows('dropdown_items')): while (have_rows('dropdown_items')): the_row();
                                                        $link = get_sub_field('link') ?>
                                                        <li>
                                                            <a href="<?php echo $link['url'] ?>"><?php echo $link['title'] ?></a>
                                                        </li>
                                                    <?php endwhile;endif; ?>
                                                </ul>
                                            </div>
                                        </li>
                                    <?php else: ?>
                                        <li class="links">
                                            <a href="#" class="label"><?php echo $top_level_link['title'] ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endwhile;endif; ?>
                            </ul>
                            <div class="bg-layer" aria-hidden="true"></div>
                        </div> <!-- dropdown-list -->
                    </div> <!-- morph-dropdown-wrapper -->
                </header>
            </div>
        </div>
    </div>
    <script src="<?php echo get_template_directory_uri() . '/js/nav.js' ?>"></script>
	<script src="<?php echo get_template_directory_uri() . '/js/script_new_nav.js' ?>"></script>
    
    