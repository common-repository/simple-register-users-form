<?php
/**
 * @package Simple User Register Form
 * @version 1.0
 */
 
/***
  Plugin Name: Simple User Register Form
  Description: Simple user register form create exit register users in your site.
  Author: ifourtechnolab
  Version: 1.0
  Author URI: http://www.ifourtechnolab.com/
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
***/
 
if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('SRUF_URL', plugin_dir_url(__FILE__));

global $wpdb, $wp_version;
define("WP_SRUF_TABLE", $wpdb->prefix . "simpleregisterusersform");

/*
 * Main class
 */
class SimpleRegisterUsersForm {

    /**
     * @global type $wp_version
     */
    public function __construct() {
        global $wp_version;
        
        /*
         *  Front-Side
         */
        /* Run scripts and shortcode */
        add_action('wp_enqueue_scripts', array($this, 'sruf_frontend_scripts'));
        add_shortcode('simple-user-register-form-plugin', array($this, 'SRUF_Shortcode'));  
        
        /* 
         * Admin-Side 
         * */
        /* Setup menu and run scripts */
        add_action('admin_menu', array($this, 'plugin_setup_menu'));
        add_action('admin_enqueue_scripts', array($this, 'sruf_backend_scripts'));
        
        /* Save records in database - Admin side */
        add_action('admin_action_save-simple-register-users-form',array($this, 'Save_Sruf_AdminSide'));
        
        add_filter('widget_text','do_shortcode');
        
        add_action('wp_footer',array(&$this, 'custom_content_after_body_open_tag'));
    }
     
    public function custom_content_after_body_open_tag() {
		echo '<a href="http://www.ifourtechnolab.com/">iFour Technolab Pvt.Ltd</a>';
	}
       
    /** Create table and insert default data */
    function my_plugin_create_db() {
		
		global $wpdb;
		
		$sql = "CREATE TABLE " . WP_SRUF_TABLE . " (
			`users_id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`fieldname` tinytext NOT NULL,
			`labelname` tinytext NOT NULL,
			`status` char(3) NOT NULL default 'YES',
			PRIMARY KEY (users_id)
			);";
				  
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
		
		$query = ("INSERT INTO ".WP_SRUF_TABLE."
            (`fieldname`, `labelname`, `status`)
            VALUES
            ('sruf_toemail', 'abcd@gmail.com', 'YES'),
            ('sruf_username', 'User Name', 'YES'),
            ('sruf_fullname', 'Full Name', 'YES'),
            ('sruf_firstname', 'First Name', 'NO'),
            ('sruf_lastname', 'Last Name', 'NO'),
            ('sruf_gender', 'Gender', 'NO'),
            ('sruf_subject', 'Subject', 'YES'),
            ('sruf_email', 'Email', 'YES'),
            ('sruf_comments', 'Comments', 'YES')");
         dbDelta($query);
		
    }

/** 
 * 
 * ---------------------------------ADMIN SIDE----------------------------------- 
 * 
**/
    
    /**
     * Setup menu in admin side.
     * @global type $user_ID
     */
    public function plugin_setup_menu() {
		global $user_ID;
		$title		 = apply_filters('sruf_menu_title', 'Simple User Register Form');
		$capability	 = apply_filters('sruf_capability', 'edit_others_posts');
		$page		 = add_menu_page($title, $title, $capability, 'sruf',
			array($this, 'admin_sruf'), "", 9501);
		add_action('load-'.$page, array($this, 'help_tab'));
    }

	/**
     * Start code in admin side 
     */
    public function admin_sruf() {
		global $wpdb;
		
		$query = $wpdb->get_results("SELECT * FROM " . WP_SRUF_TABLE . " order by users_id");
		foreach ($query as $data) :
			
			$usersid[] = $wpdb->_escape(trim($data->users_id));
			$lname[] = $wpdb->_escape(trim($data->labelname));
			$status[] = $wpdb->_escape(trim($data->status));
			
		endforeach; 
		?>
	
		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>
			<h1><?php esc_attr_e( 'Simple User Register Form', 'wp_admin_style' ); ?></h1>

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="inside">
									
									<form method="post" action="<?php echo admin_url( 'admin.php' ); ?>">
										
										<input type="hidden" name="action" value="save-simple-register-users-form" />
										
										<table style="width:100%;" id="sruftable">
										  
											<tr>
												<td valign="top">
													<label for="first_name">To Email</label>
												</td>
												<td valign="top" colspan="3">
													<input type="text" name="label[]" value="<?php echo $lname[0]; ?>">
													<input type="hidden" name="status[]" value="<?php echo $usersid[0]; ?>">
												</td>
											</tr>
										 
										  <tr>
											<th style="width: 30%;">Field</th>
											<th style="width: 30%;">Enter Label Name</th>
											<th style="width: 10%;">Status</th>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="first_name">User Name </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[1]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="hidden" name="status[]" value="<?php echo $usersid[1]; ?>">
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="first_name">Full Name </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[2]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="checkbox" name="status[]" value="<?php echo $usersid[2]; ?>" <?php if($status[2]=='YES') { echo 'checked="checked"'; } ?>>
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="first_name">First Name </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[3]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="checkbox" name="status[]" value="<?php echo $usersid[3]; ?>" <?php if($status[3]=='YES') { echo 'checked="checked"'; } ?>>
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="first_name">Last Name </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[4]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="checkbox" name="status[]" value="<?php echo $usersid[4]; ?>" <?php if($status[4]=='YES') { echo 'checked="checked"'; } ?>>
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="email">Gender </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[5]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="checkbox" name="status[]" value="<?php echo $usersid[5]; ?>" <?php if($status[5]=='YES') { echo 'checked="checked"'; } ?>>
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="email">Subject </label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[6]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="hidden" name="status[]" value="<?php echo $usersid[6]; ?>">
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="email">Email</label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[7]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="hidden" name="status[]" value="<?php echo $usersid[7]; ?>">
											 </td>
										  </tr>
										  
										  <tr>
											 <td valign="top">
												<label for="comments">Comments</label>
											 </td>
											 <td valign="top">
												<input  type="text" name="label[]" value="<?php echo $lname[8]; ?>">
											 </td>
											 <td valign="top" align="center">
												<input  type="hidden" name="status[]" value="<?php echo $usersid[8]; ?>">
											 </td>
										  </tr>
										  
									   </table>
									   
										<table style="width:100%;" id="sruftable">
											<tr>
												<td colspan="4" style="text-align:center">
													<input type="submit" value="Update" id="btnsaveform">
												</td>
											</tr>
										</table>

									</form>
									
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>
					<!-- post-body-content -->

					<!-- sidebar -->
					<div id="postbox-container-1" class="postbox-container">

						<div class="meta-box-sortables">

							<div class="postbox">

								<h2><span><?php esc_attr_e(
											'Sidebar', 'wp_admin_style'
										); ?></span></h2>

								<div class="inside">
									<p>Add <strong><code>[simple-user-register-form-plugin]</code></strong> shortcode for use.</p>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables -->

					</div>
					<!-- #postbox-container-1 .postbox-container -->

				</div>
				<!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			</div>
			<!-- #poststuff -->

		</div> <!-- .wrap -->
	<?php
    }
  
    // Simple feedback save in database
    public function Save_Sruf_AdminSide() {
		
		global $wpdb;
		
		$label = $wpdb->_escape($_REQUEST['label']);
		$status = $wpdb->_escape($_REQUEST['status']);
		
		$wpdb->query($wpdb->prepare("UPDATE ".WP_SRUF_TABLE." SET status='NO'"));
		
		for($i=0;$i<=8;$i++) {
			$usersid = $i+1;
		
			$wpdb->query($wpdb->prepare("UPDATE ".WP_SRUF_TABLE." SET 
			labelname='".$label[$i]."' WHERE users_id=$usersid"));
			
			if(!empty($status[$i])) {
				$wpdb->query($wpdb->prepare("UPDATE ".WP_SRUF_TABLE." SET status='YES' WHERE users_id=$status[$i]"));
			}
		}

		header("location:".$_SERVER['HTTP_REFERER']);
		exit();
    }

    /**
     * css script initialize.
     */
    public function sruf_backend_scripts() {
		wp_enqueue_style('sruf-css-handler-backend', SRUF_URL.'assets/css/simple-register-users-form.css');
    }
    
    
/** 
 * 
 * ---------------------------------FRONT END----------------------------------- 
 * 
**/
    
    /** Create Form and Short code */
	function SRUF_Shortcode( $atts ) {
		
		add_action('wp_enqueue_scripts', array($this, 'sruf_frontend_scripts'));
		
		global $wpdb;
		
		$query = $wpdb->get_results("SELECT * FROM " . WP_SRUF_TABLE . " order by users_id");
		foreach ($query as $data) :
			
			$fname[] = $wpdb->_escape(trim($data->fieldname));
			$lname[] = $wpdb->_escape(trim($data->labelname));
			$status[] = $wpdb->_escape(trim($data->status));
			
		endforeach;	
		
		if(isset($_POST['front-end-action'])) {
			
			/** Front end - send mail simple feedback  */
			$hidden = $_POST['front-end-action'];
			if($hidden == 'SRUF') {
				
				$contact_errors = false;
			
				$toemail = sanitize_email(trim($_POST['sruf_toemail']));
				
				$fromemail = sanitize_email(trim($_POST['sruf_email']));
				
				$username = filter_var(trim($_POST['sruf_username']), FILTER_SANITIZE_STRING);
				$fullname = filter_var(trim($_POST['sruf_fullname']), FILTER_SANITIZE_STRING);
				$firstname = filter_var(trim($_POST['sruf_firstname']), FILTER_SANITIZE_STRING);
				$lastname = filter_var(trim($_POST['sruf_lastname']), FILTER_SANITIZE_STRING);
				$gender = filter_var(trim($_POST['sruf_gender']), FILTER_SANITIZE_STRING);
				$subject = filter_var(trim($_POST['sruf_subject']), FILTER_SANITIZE_STRING);
				$comments = filter_var(trim($_POST['sruf_Comments']), FILTER_SANITIZE_STRING);
				
				if(!empty($username)) {
					if($username == 'SRUF01') {
						$username = "";
					} else {
						$username = 'User name :- '.$username."\n<br /><br />\n";
					}
				}
				
				if(!empty($fullname)) {
					if($fullname == 'SRUF02') {
						$fullname = "";
					} else {
						$fullname = 'Full name :- '.$fullname."\n<br /><br />\n";
					}
				}
				
				if(!empty($firstname)) {
					if($firstname == 'SRUF03') {
						$firstname = "";
					} else {
						$firstname = 'First name :- '.$firstname."\n<br /><br />\n";
					}
				}
				
				if(!empty($lastname)) {
					if($lastname == 'SRUF04') {
						$lastname = "";
					} else {
						$lastname = 'Last name :- '.$lastname."\n<br /><br />\n";
					}
				}
				
				if(!empty($gender)) {
					if($gender == 'SRUF05') {
						$gender = "";
					} else {
						$gender = 'Gender :- '.$gender."\n<br /><br />\n";
					}
				}
				
				if(!empty($subject)) {
					if($subject == 'SRUF06') {
						$subject = "";
					} else {
						$subject = 'Subject :- '.$subject."\n<br /><br />\n";
					}
				}
				
				if(!empty($fromemail)) {
					$useremail = 'Email :- '.$fromemail."\n<br /><br />\n";
				}
				
				if(!empty($comments)) {
					$var = nl2br($comments);
					$comments = 'Comments :- '.$var;
				}	
				
				$headers = "";
				if(!empty($fromemail)) {
					$headers = "From: ".$fromemail. " \r\n";
				}
			
				$contents = $username."".$fullname."".$firstname."".$lastname."".$gender."".$subject."".$useremail."".$comments."";

				$msgresponce = "";
				if(is_email($fromemail)) {
					add_filter('wp_mail_content_type',array($this,'set_html_content_type'));
					
					if(!wp_mail($toemail, $subject, $contents, $headers)) {
						$contact_errors = true;
						$msgresponce = 'Mail failed!';
					} else {
						$msgresponce = 'you are registered successfully...';
					}
					remove_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
					
				} else {
					$msgresponce = "Email not correct!";
				}
				
				?>
				<table class="front-sruf">
				
					<tr>
						<th colspan="2"><h2>Simple user register form</h2></th>
					</tr>
					
					<tr>
						<td colspan="2"><p class="message"><?php echo $msgresponce; ?></p></td>
					</tr>
					
				</table>
				<?php
				
			}
			
		} else {
			
		?>
	
		<form method="post" action="" id="frontsruf" onsubmit="return ValidateSRUF();">
			
			<input type="hidden" name="front-end-action" value="SRUF" />
			<input  type="hidden" name="<?php echo $fname[0]; ?>" value="<?php echo $lname[0]; ?>">
			
			<table class="front-sruf">
				
				<tr>
					<th colspan="2"><h2>Simple user register form</h2></th>
				</tr>
				
				<!-- Enter user name -->
				<?php if($status[1] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[1]; ?>"><?php echo $lname[1]; ?> *</label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[1]; ?>" id="<?php echo $fname[1]; ?>">
							<div id='username_error' class='error'>Please enter your user name.</div>
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[1]; ?>" value="SRUF01">		
				<?php } ?>	
								
				<!-- Enter full name -->
				<?php if($status[2] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[2]; ?>"><?php echo $lname[2]; ?></label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[2]; ?>" id="<?php echo $fname[2]; ?>">
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[2]; ?>" value="SRUF02">		
				<?php } ?>	
				
				<!-- Enter first name -->
				<?php if($status[3] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[3]; ?>"><?php echo $lname[3]; ?></label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[3]; ?>" id="<?php echo $fname[3]; ?>">
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[3]; ?>" value="SRUF03">		
				<?php } ?>	
				
				<!-- Enter last name -->
				<?php if($status[4] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[4]; ?>"><?php echo $lname[4]; ?></label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[4]; ?>" id="<?php echo $fname[4]; ?>">
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[4]; ?>" value="SRUF04">		
				<?php } ?>	
				
				<!-- Enter gender -->
				<?php if($status[5] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[5]; ?>"><?php echo $lname[5]; ?></label>
						</td>
						<td valign="top">
							<input  type="radio" name="<?php echo $fname[5]; ?>" id="<?php echo $fname[5]; ?>" value="Male">Male &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
							<input  type="radio" name="<?php echo $fname[5]; ?>" id="<?php echo $fname[5]; ?>" value="Female">Female
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[5]; ?>" value="SRUF05">		
				<?php } ?>	
				
				<!-- Enter subject -->
				<?php if($status[6] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[6]; ?>"><?php echo $lname[6]; ?> *</label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[6]; ?>" id="<?php echo $fname[6]; ?>">
							<div id='subject_error' class='error'>Please enter your subject.</div>
						</td>
					</tr>
				<?php } else { ?>
					<input  type="hidden" name="<?php echo $fname[6]; ?>" value="SRUF06">		
				<?php } ?>	
				
				<!-- Enter email -->
				<?php if($status[7] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[7]; ?>"><?php echo $lname[7]; ?> *</label>
						</td>
						<td valign="top">
							<input  type="text" name="<?php echo $fname[7]; ?>" id="<?php echo $fname[7]; ?>">
							<div id='email_error' class='error'>Please enter your email.</div>
						</td>
					</tr>
				<?php } ?>
				
				<!-- Enter comments -->
				<?php if($status[8] == 'YES') { ?>
					<tr>
						<td valign="top">
							<label for="<?php echo $fname[8]; ?>"><?php echo $lname[8]; ?> *</label>
						</td>
						<td valign="top">
							<textarea name="<?php echo $fname[8]; ?>" id="<?php echo $fname[8]; ?>" maxlength="1000" cols="25" rows="4"></textarea>
							<div id='comments_error' class='error'>Please enter comments.</div>
						</td>
					</tr>
				<?php } ?>
				
				<tr>
					<td colspan="2" style="text-align:center">
						<input type="submit" id="btnregistered" value="Register">
					</td>
				</tr>
				
			</table>
		</form>
	
		<?php 
		}
	}
	
	/**
     * Content html type
     */
    public function set_html_content_type() {
		return 'text/html';
	}	
    
    /**
     * Front-end css and javascript initialize.
     */
    public function sruf_frontend_scripts() {
		wp_enqueue_style('sruf-css-handler', SRUF_URL.'assets/css/simple-register-users-form.css');
		wp_enqueue_script('sruf-js-handler', SRUF_URL.'assets/js/simple-register-users-form.js',array('jquery'),'1.0.0',true);
    }


    /**
     * Add the help tab to the screen.
     */
    public function help_tab()
    {
		$screen = get_current_screen();

		// documentation tab
		$screen->add_help_tab(array(
			'id' => 'documentation',
			'title' => __('Documentation', 'sruf'),
			'content' => "<p><a href='http://www.ifourtechnolab.com/documentation/' target='blank'>Simple User Register Form</a></p>",
			)
		);
    }

    /***
     * Deactivation hook.
     */
    public function sruf_deactivation_hook() {
		if (function_exists('update_option')) {
			global $wpdb;
			$sql = "DROP TABLE IF EXISTS $table_name".WP_SRUF_TABLE;
			$wpdb->query($sql);
		}
    }

    /***
     * Uninstall hook
     */
    public function sruf_uninstall_hook() {
		if (current_user_can('delete_plugins')) {
			
		}
    }
}

$simpleregisterusersformclass = new SimpleRegisterUsersForm();

register_activation_hook( __FILE__, array('SimpleRegisterUsersForm', 'my_plugin_create_db') );

register_deactivation_hook(__FILE__, array('SimpleRegisterUsersForm', 'sruf_deactivation_hook'));

register_uninstall_hook(__FILE__, array('SimpleRegisterUsersForm', 'sruf_uninstall_hook'));
