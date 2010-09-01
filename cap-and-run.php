<?php
/*
Plugin Name: Cap & Run
Version: 1.0
Description: Drop caps and run-ins for posts and pages.
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Author: Michael A. Alderete, Aldosoft
Author URI: http://github.com/alderete/Cap-and-Run
License: GPL2

Copyright 2010 by Michael A. Alderete

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Define the CapAndRun plugin
global $wp_version;
if ( version_compare($wp_version, "2.8", "<") ):
	// Requirement for 2.8 is semi-arbitrary, gotta draw the line somewhere
	exit('Cap &amp; Run requires WordPress 2.8 or later. 
		  <a href="http://codex.wordpress.org/Upgrading_WordPress">Please upgrade.</a>
		  Upgrading is a good idea, for many reasons beyond Cap &amp; Run.');
else:
	if ( ! class_exists('CapAndRun') ) {
		class CapAndRun {
		
			var $options_name = 'CapAndRun_Options';
			var $options_loaded = false;
		
			function activate_plugin() {
				$set_defaults = $this->get_options();
			}
		
			function get_options() {
				
				if ( ! $this->options_loaded ) {
					// These are the default options
					$car_options = array(
						'add_initial_cap' => false,
						'add_run_in'      => 'none', // none | line | words
						'run_in_words'    => '5',
						'add_styles_to_header' => 'no', // no | yes
						'styles_to_add'   => '',
					);
			
					// Now merge in the saved options
					$set_options = get_option($this->options_name);
					if ( ! empty($set_options) ) {
						foreach($set_options as $key => $option) {
							$car_options[$key] = $option;
						}
					}
				
					// Make sure we save back a complete set
					update_option($this->options_name, $car_options);
				
					// Save options, so we're not writing db multiple times / page view
					$this->options_loaded = $car_options;
				}
				return($this->options_loaded);
			}
			
			// Processing functions, run on every page view
			function add_style_definitions() {
				$options = $this->get_options();
				if( 'yes' === $options['add_styles_to_header'] ) {
					echo "<!-- Styles added by Cap & Run plugin -->\n";
					echo "<style type=\"text/css\">\n";
					echo $options['styles_to_add'];
					echo "</style>\n";
					echo "<!-- End of Cap & Run style additions -->\n\n";
				}
			}
			
			function add_styling_markup($content = '') {
				// error_log($content);
				$options = $this->get_options();
				if( !( is_single() || is_page() || is_admin() ) || 
				    (($options['add_initial_cap'] === false) && ($options['add_run_in'] === 'none')) ) {
					return($content); // shortcut out
				}
				
				$pattern = '/<p( .*)?( class="(.*)")??( .*)?>((<[^>]*>|\s)*)((?:&quot;|&#8220;|&#8216;|&lsquo;|&ldquo;|\')?[A-Z])(([a-zA-Z]+\s+){0,' . $options['run_in_words'] . '}?)/U';
				$replacement = '<p$1 class="first-child $3"$4>$5<span title="$7" class="cap"><span>$7</span></span><span class="run-in">$8</span>';
				$content = preg_replace($pattern, $replacement, $content, 1 );

				/*
				// Handle the drop cap
				if( $options['add_initial_cap'] === true ) {
					// The next three lines are copied from the Drop Caps plugin by Thomas Milburn
					// http://instantsolve.net/blog/plugins/
					$pattern = '/<p( .*)?( class="(.*)")??( .*)?>((<[^>]*>|\s)*)((&quot;|&#8220;|&#8216;|&lsquo;|&ldquo;|\')?[A-Z])/U';
					$replacement = '<p$1 class="first-child $3"$4>$5<span title="$7" class="cap"><span>$7</span></span>';
					$content = preg_replace($pattern, $replacement, $content, 1 );
				}
				
				// Handle the run-in
				if( $options['add_run_in'] === true ) {
					$pattern = '//U';
					$replacement = '';
					$content = preg_replace($pattern, $replacement, $content, 1);
				}
				*/
				
				return($content);
			}
			
			// Administrative functions, runs only for WP Admins
			function add_options_page() {
				if ( function_exists('add_theme_page') ) {
				  	add_theme_page( 'Cap &amp; Run', 'Cap &amp; Run', 'manage_options', 
									basename(__FILE__), array(&$this, 'options_page') );
				}
			}
		
			function options_page() {
			
				$options = $this->get_options();
				
				// Save a submitted options form
				if ( isset($_POST['update_capandrun']) && check_admin_referer('capandrun_options_page') ) {
					// TODO -- check the nonce?
					$options['add_initial_cap'] = (isset($_POST['car_add_initial_cap'])) ? true : false;
					if ( isset($_POST['car_add_run_in']) ) {
						if ( 'line' == $_POST['car_add_run_in'] ) {
							$options['add_run_in'] = 'line';
						}
						elseif ( 'words' == $_POST['car_add_run_in'] ) {
							$options['add_run_in'] = 'words';
						}
						else {
							$options['add_run_in'] = 'none';
						}
					}
					if ( isset($_POST['cars_num_words']) ) {
						$options['run_in_words'] = min(max(1, (int)$_POST['cars_num_words']), 10); // between 1..10
					}
					if ( isset($_POST['car_add_styles']) ) {
						if ( 'yes' == $_POST['car_add_styles'] ) {
							$options['add_styles_to_header'] = 'yes';
						}
						else {
							$options['add_styles_to_header'] = 'no';
						}
					}
					if ( isset($_POST['car_styles']) ) {
						$options['styles_to_add'] = stripslashes($_POST['car_styles']); // TODO, better escape?
					}
					update_option($this->options_name, $options);
					echo '<div class="updated"><p><strong>' . __('Settings updated.', 'CapAndRun') . 
					     '</strong></p></div>';
				}

				// Display options form
				?>
				<div class="wrap">
				
					<h2>Cap &amp; Run Options</h2>

					<p class="narrow"><strong>Cap &amp; Run</strong> can add styles to display initial
					capital letters (generally called "drop caps") and text "run-ins"
					(first few words or first line) in a different text style than the
					normal body copy.</p>
				
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
						<h3>Drop Caps</h3>
						<ul>
							<li><input type="checkbox" id="car_add_initial_cap" name="car_add_initial_cap" <?php if ($options['add_initial_cap']) echo 'checked="checked"'; ?> />
							<label for="car_add_initial_cap">Stylize first letter of post/page as Drop Cap</label></li>
						</ul>
					
						<h3>Text Run-In</h3>
						<ul>
							<li><input type="radio" id="car_add_run_in_none" name="car_add_run_in" value="none" <?php 
									if ('none' === $options['add_run_in']) echo 'checked="checked"'; ?> />
								<label for="car_add_run_in_none">No text run-in</label></li>
							<li><input type="radio" id="car_add_run_in_line" name="car_add_run_in" value="line" <?php 
									if ('line' === $options['add_run_in']) echo 'checked="checked"'; ?> />
								<label for="car_add_run_in_line">Stylize first line of post/page as 
									Run In (using CSS only &mdash; more "clean")</label></li>
							<li><input type="radio" id="car_add_run_in_words" name="car_add_run_in" value="words" <?php 
									if ('words' === $options['add_run_in']) echo 'checked="checked"'; ?> />
								<label for="car_add_run_in_words">Stylize first 
									<input type="text" id="cars_num_words" name="cars_num_words" size="3" <?php 
									if ($options['run_in_words']) echo 'value="' . $options['run_in_words'] . '"'; 
									?> /> words of post/page as Run In (using &lt;span&gt; + CSS &mdash; more compatible)</label></li>
						</ul>
				
						<h3>Styling</h3>
						<ul>
							<li><input type="radio" id="car_add_styles_no" name="car_add_styles" value="no" <?php 
								if ('no' === $options['add_styles_to_header']) echo 'checked="checked"'; ?> />
								<label for="car_add_styles_no">Do not add styles to &lt;header&gt; (you must manually add styles to site CSS file)</label></li>
							<li><input type="radio" id="car_add_styles_yes" name="car_add_styles" value="yes" <?php 
								if ('yes' === $options['add_styles_to_header']) echo 'checked="checked"'; ?> />
								<label for="car_add_styles_yes">Add these styles to &lt;header&gt;:</label><br />
								&nbsp;&nbsp;&nbsp;&nbsp;<textarea id="car_styles" name="car_styles" style="width: 60%;" rows="8"><?php 
								echo stripslashes($options['styles_to_add']); ?></textarea></li>
						</ul>
					
						<div class="submit">
							<?php wp_nonce_field('capandrun_options_page'); ?> 
							<input type="submit" class="button-primary" name="update_capandrun" 
								value="<?php _e('Update Settings', 'CapAndRun'); ?>" />
						</div>
					
					</form>
					
					<h3>Tests</h3>
					<?php
						require('tests.php');
					
						foreach($cases as $case) {
							echo "<hr />";
							echo "<h4>Test Case:</h4>\n\n<p><code>" . 
									htmlspecialchars($case) . 
								 "</code></p>\n\n";
							echo "<h4>Result:</h4>\n\n<p><code>" . 
									htmlspecialchars($this->add_styling_markup($case)) . 
								 "</code></p>\n\n";
						}
					
					?>

				</div>
				<?php
			}
			
		} // end class CapAndRun definition
	}
endif; // end WordPress version check

// Activate the plugin
if ( class_exists('CapAndRun') ) {
	$cap_and_run = new CapAndRun();
	if ( function_exists('register_activation_hook') ) {
		register_activation_hook(__FILE__, array(&$cap_and_run, 'activate_plugin'));
	}
	if ( function_exists('add_action') ) {
		add_action('admin_menu', array(&$cap_and_run, 'add_options_page'));
		add_action('wp_head', array(&$cap_and_run, 'add_style_definitions'));
	}
	if ( function_exists('add_filter') ) {
		add_filter('the_content', array(&$cap_and_run, 'add_styling_markup'), 90);
	}
}

?>