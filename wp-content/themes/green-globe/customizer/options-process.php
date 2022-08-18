<?php

add_action( 'init' , 'green_globe_process' );
function green_globe_process(){

	Kirki::add_section( 'green_globe_process_section', array(
        'title'   => esc_html__( 'Process', 'green-globe' ),
        'section' => 'homepage'
    ) );

    Kirki::add_field( 'bizberg', [
        'type'        => 'checkbox',
        'settings'    => 'green_globe_process_status',
        'label'       => esc_html__( 'Enable / Disable', 'green-globe' ),
        'section'     => 'green_globe_process_section',
        'default'     => false,
    ] );

    Kirki::add_field( 'bizberg', [
        'type'     => 'text',
        'settings' => 'green_globe_process_subtitle',
        'label'    => esc_html__( 'Subtitle', 'green-globe' ),
        'section'  => 'green_globe_process_section',
        'default'  => esc_html__( 'What We Offer', 'green-globe' ),
        'active_callback' => [
            [
                'setting'  => 'green_globe_process_status',
                'operator' => '==',
                'value'    => true,
            ]
        ],
    ] );

    Kirki::add_field( 'bizberg', [
        'type'     => 'text',
        'settings' => 'green_globe_process_title',
        'label'    => esc_html__( 'Title', 'green-globe' ),
        'section'  => 'green_globe_process_section',
        'default'  => esc_html__( 'We Help People & Small Business', 'green-globe' ),
        'active_callback' => [
            [
                'setting'  => 'green_globe_process_status',
                'operator' => '==',
                'value'    => true,
            ]
        ],
    ] );

    Kirki::add_field( 'bizberg', array(
        'type'        => 'advanced-repeater',
        'label'       => esc_html__( 'Process', 'green-globe' ),
        'section'     => 'green_globe_process_section',
        'settings'    => 'green_globe_process_repeater',
        'choices' => [
            'limit' => 4,
            'button_label' => esc_html__( 'Add Process', 'green-globe' ),
            'row_label' => [
                'value' => esc_html__( 'Process', 'green-globe' ),
            ],
            'fields' => [
                'icon'  => [
                    'type'        => 'fontawesome',
                    'label'       => esc_html__( 'Icon', 'green-globe' ),
                    'default'     => 'fab fa-accusoft',
                    'choices'     => bizberg_get_fontawesome_options(),
                ],
                'page_id'  => [
                    'type'        => 'select',
                    'label'       => esc_html__( 'Select Page', 'green-globe' ),
                    'choices'     => bizberg_get_all_pages(),
                ],
            ],
        ],
        'active_callback' => [
            [
                'setting'  => 'green_globe_process_status',
                'operator' => '==',
                'value'    => true,
            ]
        ],
    ) );

}