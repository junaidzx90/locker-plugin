<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Locker_Plugin
 * @subpackage Locker_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Locker_Plugin
 * @subpackage Locker_Plugin/admin
 * @author     Md Junayed <admin@easeare.com>
 */
class Locker_Plugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		$lockable_types = get_option('lockable-post-types');
		if(is_array($lockable_types)){
			if(in_array(get_post_type($post), $lockable_types)){
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/locker-plugin-admin.css', array(), $this->version, 'all' );
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;
		$lockable_types = get_option('lockable-post-types');
		if(is_array($lockable_types)){
			if(in_array(get_post_type($post), $lockable_types) || (isset($_GET['page']) && $_GET['page'] == 'locker-plugin')){
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/locker-plugin-admin.js', array( 'jquery' ), $this->version, false );
			}
		}
	}

	function locker_admin_menu(){
		add_options_page( 'Locker settings', 'Locker settings', 'manage_options', 'locker-plugin', [$this, 'locker_plugin_html'],null );

		// Add options to settigs
		add_settings_section( 'locker-plugin-sections', '', '', 'locker-plugin-page' );

		add_settings_field( 'locks-contents', 'Locked Contents', [$this, 'locked_contents'], 'locker-plugin-page', 'locker-plugin-sections', '' );
		register_setting( 'locker-plugin-sections', 'locks-contents');
		
		add_settings_field( 'redirect_url', 'Redirect URL (If using)', [$this, 'redirect_url_input'], 'locker-plugin-page', 'locker-plugin-sections', '' );
		register_setting( 'locker-plugin-sections', 'redirect_url');

		add_settings_field( 'lockable-post-types', 'Where will show locker functionalities', [$this, 'lockable_post_types'], 'locker-plugin-page', 'locker-plugin-sections', '' );
		register_setting( 'locker-plugin-sections', 'lockable-post-types');

		add_settings_field( 'locker_for_all_user', 'For all type users', [$this, 'locker_for_all_user_cb'], 'locker-plugin-page', 'locker-plugin-sections', '' );
		register_setting( 'locker-plugin-sections', 'locker_for_all_user');

	}
	// Menupage callback
	function locker_plugin_html(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/locker-plugin-admin-display.php';
	}

	// Fields
	function locked_contents(){
		echo '<textarea class="widefat" name="locks-contents" id="locks-contents">'.wpautop( get_option('locks-contents') ).'</textarea>';
		echo '<small><p><strong style="color: red">HTML</strong> Supported</p></small>';
	}

	function redirect_url_input(){
		echo '<input class="widefat" placeholder="Page url" type="url" name="redirect_url" id="redirect_url" value="'.get_option('redirect_url').'">';
		echo '<small><p>You can use it for custom page</p></small>';
	}
	
	function lockable_post_types(){
		$post_types = get_post_types( );
		if($post_types){
			echo '<select multiple name="lockable-post-types[]">';
			foreach($post_types as $type){
				$selected = get_option('lockable-post-types');
				if(is_array($selected)){
					if(in_array($type, $selected)){
						echo '<option selected value="'.$type.'">'.$type.'</option>';
					}else{
						echo '<option value="'.$type.'">'.$type.'</option>';
					}
				}else{
					echo '<option value="'.$type.'">'.$type.'</option>';
				}
			}
			echo '</select>';
		}
	}

	function locker_for_all_user_cb(){
		$checked = 0;
		if(get_option('locker_for_all_user')){
			$checked = 1;
		}else{
			$checked = 0;
		}
		echo '<input type="checkbox" '.($checked?'checked':'').' id="locker_for_all_user" name="locker_for_all_user" value="'.$checked.'">';
		echo '<small><p>Default for visitors</p></small>';
	}

	// Meta boxes
	function locker_plugin_meta_boxes(){
		$lockable_types = get_option('lockable-post-types');
		if(is_array($lockable_types)){
			foreach($lockable_types as $screen){
				add_meta_box( 'is_post_locked', 'Locked Contents', [$this, 'locked_post_setting'], $screen, 'side', 'default' );
			}
		}
	}

	function locked_post_setting($post){
		$today = date('Y-m-d h:i:sa');
		$locked = get_post_meta($post->ID, 'is_post_locked', true);
		$end = date('Y-m-d h:i:sa', strtotime($locked['end_date']));

		echo '<div id="locker_setting">';

		if(is_locked($post->ID) && strtotime($today) <= strtotime($end)){
			echo '<span class="is_loked">
			<button class="edit_locked">Edit</button>
			</span>';
		}

		// Select dates
		echo '<div class="select-dates">';

		$start_date = '';
		$is_locked = get_post_meta($post->ID, 'is_post_locked', true);
		if($is_locked && is_array($is_locked) && in_array($is_locked['start_date'], $is_locked) && !empty($is_locked['start_date'])){
			$start_date = date('Y-m-d\TH:i:s', strtotime($is_locked['start_date']));
		}

		$end_date = '';
		$is_locked = get_post_meta($post->ID, 'is_post_locked', true);
		if($is_locked && is_array($is_locked) && in_array($is_locked['end_date'], $is_locked) && !empty($is_locked['end_date'])){
			$end_date = date('Y-m-d\TH:i:s', strtotime($is_locked['end_date']));
		}

		echo '<label for="start-date">Start date (Optional)
		<input class="widefat" value="'.$start_date.'" type="datetime-local" name="locker_lock-start-date" id="start-date">
		</label>';

		echo '<label for="end-date">End Date (Required)
		<input class="widefat" value="'.$end_date.'" type="datetime-local" name="locker_lock-end-date" id="end-date">
		</label>';

		echo '</div>';// Select dates

		// Options
		echo '<div class="options_settings">';

		echo '<h4 class="lock-title">Option (Required)</h4>';
		$selected = '';
		$is_locked = get_post_meta($post->ID, 'is_post_locked', true);
		if($is_locked && is_array($is_locked)){
			$selected = $is_locked['option'];
		}
		
		echo '<label>
			<input '.(($selected == 'redirect')?'checked':'').' type="radio" name="locked_option" value="redirect" />
			<span>Redirect</span>
		</label>';

		echo '<label>
			<input '.(($selected == 'contents')?'checked':'').' type="radio" name="locked_option" value="contents" />
			<span>Locked Contents</span>
		</label>';

		if(strtotime($today) > strtotime($end)){
			echo '<p style="color: #e76c6c">Date has expired.</p>';
		}

		echo '</div>';// Options
		echo '</div>';// Wrapper end
	}

	function locker_plugin_save_post($post_id){
		$locked = array();

		if(isset($_POST['locked_option'])){
			$option = $_POST['locked_option'];

			if(!empty($option)){
				$locked = [
					'option' => $option
				];
				
			}else{
				delete_post_meta( $post_id, 'is_post_locked' );
			}

			// Start date
			if(isset($_POST['locker_lock-start-date'])){
				$start_date = $_POST['locker_lock-start-date'];
				$locked['start_date'] = $start_date;
			}
			// End date
			if(isset($_POST['locker_lock-end-date']) && !empty($_POST['locker_lock-end-date'])){
				$end_date = $_POST['locker_lock-end-date'];
				$locked['end_date'] = $end_date;
			}else{
				$locked = [];
			}
		}

		update_post_meta( $post_id, 'is_post_locked', $locked);
	}

	// Contents
	function locker_plugin_the_content($the_content){
		$lock = false;
		if(get_option('locker_for_all_user')){
			$lock = true;
		}else{
			if(!is_user_logged_in(  )){
				$lock = true;
			}
		}
		
		if($lock){
			global $post;

			if(is_locked($post->ID)){
				$is_locked = get_post_meta($post->ID, 'is_post_locked', true);
				if(is_array($is_locked)){
					$today = date('Y-m-d h:i:sa');

					$start_date = '';
					if($is_locked['start_date']){
						$start_date = date('Y-m-d h:i:sa', strtotime($is_locked['start_date']));
					}
					$end_date = '';
					if($is_locked['end_date']){
						$end_date = date('Y-m-d h:i:sa', strtotime($is_locked['end_date']));
					}

					$is_start = true;
					if($start_date){
						if(strtotime($today) < strtotime($start_date)){
							$is_start = false;
						}
					}
					
					if(strtotime($today) <= strtotime($end_date) && $is_start){
						$lock_option = $is_locked['option']; //lock option
						if($lock_option === 'redirect'){
							$redirect_url = esc_url( get_option('redirect_url') );
							wp_redirect( $redirect_url );
						}else if($lock_option === 'contents'){
							$lock_contents = get_option('locks-contents');
							return $lock_contents;
						}else{
							return $the_content; //Default
						}
					}else{
						return $the_content; //Default
					}
				}
			}else{
				return $the_content; //Default
			}
		}else{
			return $the_content; //Default
		}
	}

}