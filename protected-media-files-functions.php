<?php
function protected_media_files_admin_enqueue_scripts(){
	global $pagenow, $typenow;
	// var_dump($pagenow); //edit.php
	// var_dump($typenow); //p_file
	if ($pagenow == 'edit.php' AND $typenow == 'p_file') {
		wp_enqueue_style( 'protected-media-files-admin', plugins_url( 'css/protected-media-files-admin.css', __FILE__ ) );

		//wp_enqueue_media();

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'protected-media-files-admin', plugins_url( 'js/protected-media-files-admin.js', __FILE__ ), array('jquery') );
	}
}
add_action( 'admin_enqueue_scripts', 'protected_media_files_admin_enqueue_scripts' );
function protected_media_files_enqueue_scripts(){
	wp_enqueue_style( 'protected-media-files', plugins_url( 'css/protected-media-files.css', __FILE__ ) );
	wp_enqueue_script( 'protected-media-files', plugins_url( 'js/protected-media-files.js', __FILE__ ), array('jquery') );
	$ajax_params = array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'ajax_nonce' => wp_create_nonce('pmf_verify'),
	);
	wp_localize_script( 'protected-media-files', 'ajax_obj', $ajax_params );
}
add_action( 'wp_enqueue_scripts', 'protected_media_files_enqueue_scripts' );

/* 3. AJAX CALLBACK
------------------------------------------ */

/* AJAX action callback */
add_action( 'wp_ajax_pmf', 'my_wp_ajax_noob_pmf_ajax_callback' );
add_action( 'wp_ajax_nopriv_pmf', 'my_wp_ajax_noob_pmf_ajax_callback' );

add_action( 'wp_ajax_pmf_login', 'my_wp_ajax_noob_pmf_login_ajax_callback' );
add_action( 'wp_ajax_nopriv_pmf_login', 'my_wp_ajax_noob_pmf_login_ajax_callback' );


/**
 * Ajax Callback
 */
function my_wp_ajax_noob_pmf_ajax_callback(){
	check_ajax_referer( 'pmf_verify', 'security' );
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
function my_wp_ajax_noob_pmf_login_ajax_callback(){
	check_ajax_referer( 'pmf_verify', 'security' );
	$mos_pmf_option = get_option( 'mos_pmf_option' );
	$output = array();
	$output['pmf_access'] = false;
	$pmf_pcode = isset( $_POST['pmf_pcode'] ) ? $_POST['pmf_pcode'] : 0;
	if (@$mos_pmf_option['mos_login_type'] == 'pin' AND @$mos_pmf_option['mos_login_pin']) {
		$mos_login_pin = $mos_pmf_option['mos_login_pin'];
		if ($pmf_pcode == $mos_login_pin)
			$output['pmf_access'] = true;
			$output['redirect'] =  get_the_permalink( $mos_pmf_option['mos_dashboard_url'] );
	}
	//$output['pmf_pcode'] = $pmf_pcode;
	header("Content-type: text/x-json");
	echo json_encode($output);

	//die();	
	wp_die(); // required. to end AJAX request.
}





function protected_files_func( $atts = array(), $content = '' ) {
	$mos_pmf_option = get_option( 'mos_pmf_option' );
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

if (isset($_COOKIE['pmf_access'])) return $html;
else return '<p class="test-center">No content Found</p>';
}
add_shortcode( 'protected_files', 'protected_files_func' );


function auth_btn_func( $atts = array(), $content = '' ) {
	$mos_pmf_option = get_option( 'mos_pmf_option' );
	$title = ($mos_pmf_option['mos_btn_title']) ? $mos_pmf_option['mos_btn_title'] : 'Login Area';
	$url = ( $mos_pmf_option['mos_dashboard_url'] ) ? get_the_permalink( $mos_pmf_option['mos_dashboard_url'] ) : home_url();
	$html = '';
	$atts = shortcode_atts( array(
		'id' => 'value',
	), $atts, 'auth_btn' );
	$html .= '<div class="dropdown">';
	$html .= '<button class="top-btn btn-right dropdown-toggle" type="button" data-toggle="dropdown">' . $title;
	$html .= '<span class="caret"></span></button>';
	$html .= '<ul class="dropdown-menu dropdown-menu-right">';
	if ($mos_pmf_option['mos_login_type'] == 'basic') {
		if (is_user_logged_in()) {
			$html .= '<li><a href="'.$url.'">Dashboard</a></li>';
			$html .= '<li><a href="'.wp_logout_url(get_permalink(home_url())).'">Logout</a></li>';
		} else {
			$html .= '<li><a href="'.wp_login_url().'">Login</a></li>';
			$html .= '<li><a href="'.wp_registration_url().'">Register</a></li>';
		}
	} else {
		if (!isset($_COOKIE['pmf_access'])) $html .= '<li><a id="pmf_poup" href="javascript:void(0)">Login</a></li>';
		else { 
			$html .= '<li><a href="'.$url.'">Dashboard</a></li>';
			$html .= '<li><a id="pmf_logout" href="'.home_url().'">Logout</a></li>';
		}
	}
	$html .= '</ul>';
	$html .= '</div>';
	return $html;
}
add_shortcode( 'auth_btn', 'auth_btn_func' );

/*Login redirect*/
function admin_login_redirect( $redirect_to, $request, $user  ) {
	$mos_pmf_option = get_option( 'mos_pmf_option' );
	$url = ( $mos_pmf_option['mos_dashboard_url'] ) ? get_the_permalink( $mos_pmf_option['mos_dashboard_url'] ) : home_url();
    if (is_array( $user->roles )) {
        if (in_array( 'subscriber', $user->roles )) {
            return $url;
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
	$mos_pmf_option = get_option( 'mos_pmf_option' );
	$url = ( $mos_pmf_option['mos_dashboard_url'] ) ? get_the_permalink( $mos_pmf_option['mos_dashboard_url'] ) : home_url();
	if ( is_admin() && !current_user_can( 'administrator' ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		wp_redirect( home_url($url) );
		exit;
	}
}




add_action('wp_login', 'add_custom_cookie_admin');
function add_custom_cookie_admin() {
  //if(is_admin()) {
    setcookie('pmf_access', 1, time() + 86400, '/'); // expire in a day
  //}
}
add_action('wp_logout', 'remove_custom_cookie_admin');
function remove_custom_cookie_admin() {
  setcookie('pmf_access', '', time() - 3600);
}

/*function myplugin_activate() {
	?>
	<script>
		alert(0);
		//window.location.href = <?php echo admin_url('/edit.php?post_type=p_file&page=pmf_settings')?>;
	</script>
	<?php
}
register_activation_hook( __FILE__, 'myplugin_activate' );*/


