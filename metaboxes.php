<?php
function pmf_metaboxes() {
    $prefix = '_pmf_';   
    $pmf_details = new_cmb2_box(array(
        'id' => $prefix . 'gallery_details',
        'title' => __('Gallery Details', 'cmb2'),
        'object_types' => array('p_file'),
        //'show_on'      => array( 'key' => 'page-template', 'value' => 'page-template/lightbox-gallery-page.php' ),
    )); 
    // $pmf_details->add_field( array(
    //     'name' => 'Each image width',
    //     'id'   => $prefix . 'image_width',
    //     'type' => 'text_number',
    // ));
    // $pmf_details->add_field( array(
    //     'name' => 'Each image height',
    //     'id'   => $prefix . 'image_height',
    //     'type' => 'text_number',
    // ));
    // $pmf_details->add_field( array(
    //     'name'             => 'Large Image Size',
    //     'desc'             => 'Select an option',
    //     'id'               => $prefix . 'large_image_size',
    //     'type'             => 'select',
    //     'default'          => 'container',
    //     'options'          => array(
    //         'actual' => __( 'Actual Size', 'cmb2' ),
    //         'max'   => __( 'Max Size (Width 1920px)', 'cmb2' ),
    //         'container'     => __( 'Container Size (Width 1140px)', 'cmb2' ),
    //     ),
    // ) );
    // $pmf_details->add_field( array( 
    //     'name' => __('Gallery Layout', 'cmb2'), 
    //     'id' => $prefix . 'gallery_layout', 
    //     'type' => 'select', 
    //     'default'          => '6',
    //     'options'          => array(
    //         '6' => __( 'Two Column', 'cmb2' ),
    //         '4'   => __( 'Three Column', 'cmb2' ),
    //         '3'     => __( 'Four Column', 'cmb2' ),
    //     ),
    //     'default' => '6',
    // ));      
    // $pmf_details->add_field(array(
    //     'name' => 'Grid Spacing',
    //     'desc' => 'Yes I like to use gap between grids.',
    //     'id'   => $prefix.'gallery_gap',
    //     'type' => 'checkbox',
    // ));  
    $pmf_details->add_field(array(
        'name' => 'Gallery Images',
        'desc' => '',
        'id'   => $prefix.'gallery_images',
        'type' => 'file_list',
        'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
        //'query_args' => array( 'type' => 'image' ), // Only images attachment
    ));

     

}
add_action('cmb2_admin_init', 'pmf_metaboxes');