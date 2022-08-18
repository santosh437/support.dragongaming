<?php 

add_action( 'bizberg_before_homepage_blog', 'green_globe_homepage_services' );
function green_globe_homepage_services(){ 

    $status = bizberg_get_theme_mod( 'green_globe_services_status' );

    if( $status == false ){
        return;
    }

    $subtitle = bizberg_get_theme_mod( 'green_globe_services_subtitle' );
    $title    = bizberg_get_theme_mod( 'green_globe_services_title' );
    $data     = bizberg_get_theme_mod( 'green_globe_services_repeater' );
    $data     = json_decode( $data, true );  ?>

	<section class="features-section features-section1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-12">
                    <div class="section-title text-center mb-60">
                        <h3 class="sub-title1"><?php echo esc_html( $subtitle ); ?></h3>
                        <h2><?php echo esc_html( $title ); ?></h2>
                    </div>
                </div>
            </div>

            <?php 
            if( !empty( $data ) ){ ?>

                <div class="row justify-content-center">

                    <?php 
                    foreach ( $data as $key => $value ) {

                        $icon        = !empty( $value['icon'] ) ? $value['icon'] : '';
                        $page_id     = !empty( $value['page_id'] ) ? $value['page_id'] : '';
                        $servicesObj = get_post( $page_id ); ?>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="features-item mb-40">
                                <div class="icon">
                                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                </div>
                                <div class="text">
                                    <h4 class="title"><?php echo esc_html( get_the_title( $page_id ) ); ?></h4>
                                    <p><?php echo esc_html( sanitize_text_field( wp_trim_words( $servicesObj->post_content, 10, null ) ) ); ?></p>
                                </div>
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