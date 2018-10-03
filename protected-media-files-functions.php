<?php
function protected_media_files_enqueue_scripts(){
	wp_enqueue_style( 'protected-media-files', plugins_url( 'css/protected-media-files.css', __FILE__ ) );
	wp_enqueue_script( 'protected-media-files', plugins_url( 'js/protected-media-files.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'protected-media-files', 'ajax_url', admin_url( 'admin-ajax.php' ) );
}
add_action( 'wp_enqueue_scripts', 'protected_media_files_enqueue_scripts' );

/* 3. AJAX CALLBACK
------------------------------------------ */

/* AJAX action callback */
add_action( 'wp_ajax_pmf', 'my_wp_ajax_noob_pmf_ajax_callback' );
add_action( 'wp_ajax_nopriv_pmf', 'my_wp_ajax_noob_pmf_ajax_callback' );


/**
 * Ajax Callback
 */
function my_wp_ajax_noob_pmf_ajax_callback(){
	//echo 1;
	$output = array();
	$url = $gal = '';
	$id = isset( $_POST['id'] ) ? $_POST['id'] : 0;
	$output['title'] = get_the_title( $id );

	$content_post = get_post($id);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	$output['content'] = $content;
	$files = get_post_meta( $id, '_pmf_gallery_images', true );
	$vf = array("video/mp4", "video/mpg", "video/mpeg", "video/mov", "video/avi", "video/flv", "video/wmv");
	$vf = array("mp4", "MP4");
	$if = array("gif", "png", "jpeg", "jpg", "JPG", "JPEG", "PNG", "GIF");
	foreach ($files as $key => $value) {
		$url = wp_get_attachment_url( $key );
		$slices = explode('.', $url);
		if (in_array(end($slices), $if)){
		    $gal .= '<img src="'.$url.'" />';
		}
		elseif (in_array(end($slices), $vf)){
			$gal .= '<video controls style="width: 100%"><source src="'.$url.'" type="video/mp4">Your browser does not support the video tag.</video>';
		}
	}
	$output['media'] = $gal;
	
	header("Content-type: text/x-json");
	echo json_encode($output);

	//die();	
	wp_die(); // required. to end AJAX request.
}





function protected_files_func( $atts = array(), $content = '' ) {
	$html = $container = '';

$html .= '<div id="pmfModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Modal Header</h2>
      </div>
      <div class="modal-body">
        <p>Content is loading.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>';

	$n = 1;
	$atts = shortcode_atts( array(
		'limit'				=> '-1',
		'offset'			=> 0,
		'category'			=> '',
		'tag'				=> '',
		'orderby'			=> '',
		'order'				=> '',
		'container'			=> 0,
		'container_class'	=> '',
		'class'				=> '',
		'grid'				=> 'one',
		'format'			=> 'title, content, image-270x150, excerpt-20, meta:meta_field_name',
		'pagination'		=> 0,
	), $atts, 'protected_files' );

	$cat = ($atts['category']) ? preg_replace('/\s+/', '', $atts['category']) : '';
	$tag = ($atts['tag']) ? preg_replace('/\s+/', '', $atts['tag']) : '';

	$args = array( 
		'post_type' 		=> 'p_file',
		'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
	);
	if ($atts['limit'] AND $atts['limit'] != '-1') $args['posts_per_page'] = $atts['limit'];
	if ($atts['offset']) $args['offset'] = $atts['offset'];

	if ($atts['category'] OR $atts['tag']) {
		$args['tax_query'] = array();
		if ($atts['category'] AND $atts['tag']) {
			$args['tax_query']['relation'] = 'OR';
		}
		if ($atts['category']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'p_file-category',
					'field'    => 'term_id',
					'terms'    => explode(',', $cat),
				);
		}
		if ($atts['tag']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'p_file-tag',
					'field'    => 'term_id',
					'terms'    => explode(',', $tag),
				);
		}
	}
	if ($atts['orderby']) $args['orderby'] = $atts['orderby'];
	if ($atts['order']) $args['order'] = $atts['order'];
	if ($atts['author']) $args['author'] = $atts['author'];

	$query = new WP_Query( $args );
	if ( $query->have_posts() ) :
		$idenfier = rand(10,1000);
		
		$html .= '<div id="protected-files-'.$idenfier.'" class="protected-files protected-files-container grid-row">';

		$html .= '<div class="grid '.$atts['grid'].'">';
		while ( $query->have_posts() ) : $query->the_post();			
			$html .= '<div class="grid-item item-'.$n.'">';			

			$slices = explode(',', str_replace(' ', '', $atts['format']));
			foreach ($slices as $slice) {
				if ($slice == 'title') {
					$html .= '<h3 class="p-file-title">';
					$html .= get_the_title();
					$html .= '</h3><!--/.p-file-title-->';
				} elseif ($slice == 'content') {
					$html .= '<div class="p-file-content">';
					$html .= get_the_content();
					$html .= '</div><!--/.p-file-content-->';					
				} elseif (preg_match("/image/i", $slice)) {
					$ipieces = explode('-', $slice);
					$ixpieces = explode('x', $ipieces[1]);
					$html .= '<div class="p-file-image">';
					if (has_post_thumbnail()) {
						$html .= '<img class="img-responsive img-pmf" src="'.aq_resize(get_the_post_thumbnail_url(), $ixpieces[0], $ixpieces[1] ).'" />';
					}
					$html .= '</div><!--/.p-file-image-->';					
				} elseif (preg_match("/excerpt/i", $slice)) {
					$epieces = explode('-', $slice);
					$html .= '<div class="p-file-excerpt">';
					$html .= wp_trim_words(get_the_content(), $epieces[1], '...');
					$html .= '</div><!--/.p-file-excerpt-->';					
				} elseif (preg_match("/meta:/i", $slice)) {					
					$mpieces = explode(':', str_replace(' ', '', $slice));
					$html .= '<div class="p-file-meta-'.end($mpieces).'">';
					$html .= get_post_meta( get_the_ID(), end($mpieces), true );
					$html .= '</div><!--/.p-file-meta-'.end($mpieces).'-->';					
				}
			}
			$html .= '<a id="'.get_the_ID().'" data-id="'.get_the_ID().'" class="p-file-link" href="'.get_the_permalink().'">Read More</a>';
			$html .= '</div><!--/.grid-item-->';	
			$n++;
		endwhile;

		$html .= '</div><!--./grid-->';

		$html .= '</div><!--/.protected-files-container-->';
		wp_reset_postdata();
		if ($atts['pagination']) :
		    $html .= '<div class="pagination-wrapper">'; 
		        $html .= '<nav class="navigation pagination" role="navigation">';
		            $html .= '<div class="nav-links">'; 
		            $big = 999999999; // need an unlikely integer
		            $html .= paginate_links( array(
		                'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
		                'format' => '?paged=%#%',
		                'current' => max( 1, get_query_var('paged') ),
		                'total' => $query->max_num_pages,
		                'prev_text'          => __('Prev'),
		                'next_text'          => __('Next')
		            ) );
		            $html .= '</div>';
		        $html .= '</nav>';
		    $html .= '</div>';
		endif;
	endif;

if (is_user_logged_in()) return $html;
else return '<p class="test-center">No content Found</p>';
}
add_shortcode( 'protected_files', 'protected_files_func' );


function auth_btn_func( $atts = array(), $content = '' ) {
	$html = '';
	$atts = shortcode_atts( array(
		'id' => 'value',
	), $atts, 'auth_btn' );
	$html .= '<div class="dropdown">';
	$html .= '<button class="top-btn btn-right dropdown-toggle" type="button" data-toggle="dropdown">Dropdown Example';
	$html .= '<span class="caret"></span></button>';
	$html .= '<ul class="dropdown-menu dropdown-menu-right">';
	if (is_user_logged_in()) {
		$html .= '<li><a href="'.home_url('/dashboard/').'">Dashboard</a></li>';
		$html .= '<li><a href="'.wp_logout_url(get_permalink(home_url())).'">Logout</a></li>';
	} else {
		$html .= '<li><a href="'.wp_login_url().'">Login</a></li>';
		$html .= '<li><a href="'.wp_registration_url().'">Register</a></li>';
	}
	$html .= '</ul>';
	$html .= '</div>';
	return $html;
}
add_shortcode( 'auth_btn', 'auth_btn_func' );

/*Login redirect*/
function admin_login_redirect( $redirect_to, $request, $user  ) {
    if (is_array( $user->roles )) {
        if (in_array( 'subscriber', $user->roles )) {
            return home_url('/dashboard/');
        } else {
            return admin_url();
        }
    } else {
        return home_url('/');
    }
    //return ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) ? admin_url() : home_url('/shopmanager/');
}
add_filter( 'login_redirect', 'admin_login_redirect', 10, 3 );

function single_post_redirect () {
	if (get_post_type() == 'p_file' AND !is_user_logged_in()) {
		$url = home_url();
		?>
		<script>window.location.href = '<?php echo home_url() ?>';</script>
		<?php 
		exit();
	}
}
add_action( 'wp_head', 'single_post_redirect' );

/*Limit admin access*/
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
	if ( is_admin() && !current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url('/dashboard/') );
		exit;
	}
}