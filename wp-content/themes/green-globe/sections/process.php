<?php 

add_action( 'bizberg_before_homepage_blog', 'green_globe_homepage_process' );
function green_globe_homepage_process(){

	$status = bizberg_get_theme_mod( 'green_globe_process_status' );

	if( $status == false ){
		return;
	} 

	$subtitle = bizberg_get_theme_mod( 'green_globe_process_subtitle' );
	$title    = bizberg_get_theme_mod( 'green_globe_process_title' );

	$data     = bizberg_get_theme_mod( 'green_globe_process_repeater' );
	$data     = json_decode( $data, true ); ?>

	<section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-12">
                    <div class="section-title text-center mb-60">
                        <h3 class="sub-title1">
                        	<?php echo esc_html( $subtitle ); ?>
                        </h3>
                        <h2><?php echo esc_html( $title ); ?></h2>
                    </div>
                </div>
            </div>

            <?php 
            if( !empty( $data ) ){ ?>

	            <div class="row">

	            	<?php 

	            	foreach( $data as $key => $value ){ 

	            		$icon    = !empty( $value['icon'] ) ? $value['icon'] : '';
	            		$page_id = !empty( $value['page_id'] ) ? $value['page_id'] : ''; ?>

		                <div class="col-xl-6 col-lg-6">
		                    <div class="features-item-two mb-30 d-flex align-items-center">
		                        <span class="number">0<?php echo absint( $key + 1 ); ?>.</span>
		                        <div class="icon">
		                            <i class="<?php echo esc_attr( $icon ); ?>"></i>
		                        </div>
		                        <div class="text">
		                            <h3 class="title"><?php echo esc_html( get_the_title( $page_id ) ); ?></h3>
		                        </div>
		                        <a href="<?php echo esc_url( get_the_permalink( $page_id ) ); ?>" class="icon-btn"><i class="fas fa-arrow-right"></i></a>
		                    </div>
		                </div>

		                <?php 
		            } ?>

	            </div>

	            <?php 
	        } ?>

        </div>
    </section>

	<?php
}