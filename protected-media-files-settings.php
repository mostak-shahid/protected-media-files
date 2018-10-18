<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" ) {  
    if ($_POST['mos_pmf_submit'] == 'Save Changes') {
	    $mos_pmf_option = array();
	    foreach ($_POST as $field => $value) {
	    	$mos_pmf_option[$field] = trim($value);
	    }
	    update_option( 'mos_pmf_option', $mos_pmf_option, false );
	}
}

function mos_pmf_admin_menu () {
    add_submenu_page( 'edit.php?post_type=p_file', 'Settings', 'Settings', 'manage_options', 'pmf_settings', 'mos_pmf_admin_page' );
}
add_action("admin_menu", "mos_pmf_admin_menu");
function mos_pmf_admin_page () {
  if (@$_GET['tab']) $active_tab = $_GET['tab'];
  elseif (@$_COOKIE['pmf_active_tab']) $active_tab = $_COOKIE['pmf_active_tab'];
  else $active_tab = 'dashboard';

	$mos_pmf_option = get_option( 'mos_pmf_option' );
	?>
	<div class="wrap mos-pmf-wrapper">
        <h1><?php _e("Settings") ?></h1>
        <ul class="nav nav-tabs">
            <li class="tab-nav <?php if($active_tab == 'dashboard') echo 'active';?>"><a data-id="dashboard" href="?post_type=p_file&page=pmf_settings&tab=dashboard">Dashboard</a></li>
            <li class="tab-nav <?php if($active_tab == 'login') echo 'active';?>"><a data-id="login" href="?post_type=p_file&page=pmf_settings&tab=login">Login</a></li>
        </ul>
        <form method="post">
        	<div id="mos-pmf-dashboard" class="tab-con <?php if($active_tab == 'dashboard') echo 'active';?>">
        		<!-- <h3>Body Styling</h3> -->
		        <table class="form-table">
		            <tbody>
		                <tr>
		                    <th scope="row"><label for="mos_dashboard_url">Dashboard URL</label></th>
		                    <!-- <td><input type="text" name="mos_dashboard_url" id="mos_dashboard_url" class="regular-text" value="<?php echo @$mos_pmf_option['mos_dashboard_url']; ?>"></td> -->
		                    <td>
		                    	<?php $pages = get_pages();?>
		                    	<select name="mos_dashboard_url" id="mos_dashboard_url">
									<option value="">Select One</option>
								<?php foreach ($pages as $page) : ?>									
									<option value="<?php echo $page->ID?>" <?php selected( $mos_pmf_option['mos_dashboard_url'], $page->ID ) ?>><?php echo $page->post_title?></option>
								<?php endforeach; ?>
								</select>

		                    </td>
		                    	<?php 
		                    	//var_dump(get_pages()[0]);
		                    	/*
  ["ID"]=>
  int(9)
  ["post_author"]=>
  string(1) "1"
  ["post_date"]=>
  string(19) "2018-08-10 04:09:17"
  ["post_date_gmt"]=>
  string(19) "2018-08-10 04:09:17"
  ["post_content"]=> ''
   ["post_title"]=>
  string(5) "ABOUT"
  ["post_excerpt"]=>
  string(0) ""
  ["post_status"]=>
  string(7) "publish"
  ["comment_status"]=>
  string(6) "closed"
  ["ping_status"]=>
  string(6) "closed"
  ["post_password"]=>
  string(0) ""
  ["post_name"]=>
  string(5) "about"
  ["to_ping"]=>
  string(0) ""
  ["pinged"]=>
  string(0) ""
  ["post_modified"]=>
  string(19) "2018-09-20 05:47:27"
  ["post_modified_gmt"]=>
  string(19) "2018-09-20 05:47:27"
  ["post_content_filtered"]=>
  string(0) ""
  ["post_parent"]=>
  int(0)
  ["guid"]=>
  string(40) "http://naturopathic.belocal.today/about/"
  ["menu_order"]=>
  int(0)
  ["post_type"]=>
  string(4) "page"
  ["post_mime_type"]=>
  string(0) ""
  ["comment_count"]=>
  string(1) "0"
  ["filter"]=>
  string(3) "raw"
		                    	*/
		                    	?>

		                </tr>
		                <tr>
		                    <th scope="row"><label for="mos_btn_title">Auth Buttton Title</label></th>
		                    <td><input type="text" name="mos_btn_title" id="mos_btn_title" class="regular-text" value="<?php echo @$mos_pmf_option['mos_btn_title']; ?>"></td>
		                </tr>
		            </tbody>
		        </table>
          </div>
          <div id="mos-pmf-login" class="tab-con <?php if($active_tab == 'login') echo 'active';?>">
            <table class="form-table">
              <tbody>
                  <tr>
                    <th scope="row"><label for="mos_login_type">Login Type</label></th>
                    <td>
                      <?php $pages = get_pages();?>
                      <select name="mos_login_type" id="mos_login_type">
                        <option value="">Select One</option>
                        <option value="basic" <?php selected( $mos_pmf_option['mos_login_type'], 'basic' ) ?>>Basic Login</option>
                        <option value="pin" <?php selected( $mos_pmf_option['mos_login_type'], 'pin' ) ?>>Pin Login</option>
                      </select>
                    </td> 
                  </tr>
                  <tr>
                    <th scope="row"><label for="mos_login_pin">Login Pin</label></th>
                    <td><input type="text" name="mos_login_pin" id="mos_login_pin" class="regular-text" value="<?php echo @$mos_pmf_option['mos_login_pin']; ?>"></td>
                  </tr>
                </tbody> 
              </table>          
          </div>
	    	<p class="submit"><input type="submit" name="mos_pmf_submit" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>
    </div>
	<?php
}