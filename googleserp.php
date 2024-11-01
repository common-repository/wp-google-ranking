<?php
/*
Plugin Name: WP Google Ranking
Plugin URI: http://www.sebastianomontino.com/2010/03/wp-google-ranking-wordpress-plugin/
Description: WP Google Ranking is a daily cron service, that helps you to track the Google SERPS's fluctuations. <br>It checks every day your Google position in several Google geo-servers(google.com / google.co.uk / google.de / google.es / google.it / google.fr and more coming soon :)).
Version: 0.6.1
Author: Sebastiano Montino
Author URI: http://www.sebastianomontino.com
*/
/*  Copyright 2010  Sebastiano Montino  (email : sebastiano.montino@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

require_once("functions.php");
add_action('admin_menu', 'add_pages');
register_activation_hook(__FILE__,"activate_plugin_serp");
add_action('serp_check', 'daily_cron');
register_deactivation_hook(__FILE__, 'deactivate_plugin_serp');
/*wp_deregister_script( 'jquery' );
wp_register_script('jquery', WP_PLUGIN_URL . '/wp-google-ranking/js/jquery-1.4.2.min.js');*/
wp_register_style('wpgr', WP_PLUGIN_URL . '/wp-google-ranking/css/style.css');
wp_enqueue_style('wpgr');

function daily_cron() {
	$options=get_options();
	google_check_cron();
	ga_stats("cron",get_option('siteurl'));
}

// PLUGIN ACTIVATION
if(!function_exists('activate_plugin_serp')) {
	function activate_plugin_serp() {
		wp_schedule_event(time(), 'daily', 'serp_check'); //SCHEDULE ACTIVATION

		global $wpdb;
		$table_name = $wpdb->prefix . "wp_serp";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
				id int UNSIGNED NOT NULL AUTO_INCREMENT,
				domain varchar(100) NOT NULL,
				url varchar(150) NOT NULL,
				title varchar(100) NOT NULL,
				position int(3) NOT NULL,
				timestamp varchar(11) NOT NULL,
				history varchar(4) NOT NULL,
				keyword varchar(30) NOT NULL,
				PRIMARY KEY (id) );";
			$tmp=$wpdb->query($sql);
			add_option("WP_GOOGLE_RANKING",array("domain1"=>"androidiani.com",
												"keyword1"=>"android italia",
												"searchengine1"=>"googleit",
												"days1"=>"1",
												"pages1"=>"3",
												"keywords"=>"1",
												"version"=>"0.6.1"));
		}
		else {
			//$version=get_option('version');
			$options=get_options();
			/*if($options['version']!="0.6") { //For now this solution :)
				$sql="ALTER TABLE `".$table_name."` ADD `keyword` VARCHAR( 30 ) NOT NULL ";
				$tmp=$wpdb->query($sql);
				update_option("WP_GOOGLE_RANKING",array("domain1"=>$options['domain'],
														"keyword1"=>$options['keyword'],
														"searchengine1"=>$options['searchengine'],
														"days1"=>$options['days'],
														"pages1"=>$options['pages'],
														"version"=>"0.6",
														"keywords"=>"1"));
			}*/
		}
		ga_stats("activation",get_option('siteurl'));
	}
}

// PLUGIN DE-ACTIVATION
if(!function_exists('deactivate_plugin_serp')) {
	function deactivate_plugin_serp() {
		wp_clear_scheduled_hook('serp_check');
		ga_stats("remotion",get_option('siteurl'));
	}
}

if(!function_exists('wp_google_ranking')) {
	function wp_google_ranking() {
		$options=get_options();
		//add_action('wp_print_styles', 'add_my_stylesheet');
		//$crons = _get_cron_array();
		//print_r($crons); // PRINT THE CRONTAB
			/*
				GETTING USER LEVEL
				$user_info = get_userdata(1);
				echo $user_info->user_level;
			*/
			
			google_check_cron(); // YEAH CHECKING&FUCKING
			global $wpdb;
			$table_name = $wpdb->prefix . "wp_serp";
			
			?>
			<div id="tabs" class='wrap'>
				<h2>Select here below the Keyword!</h2>
				<ul>
					<?php for($c=1;$c<=$options['keywords'];$c++) { ?>
						<li class='wpgr_tab_name'><a href="#tabs-<?php echo $c; ?>">Keyword Nb.<?php echo $c; ?></a></li>
					<?php } ?>
				</ul><br><br>
				<hr>
			<?php
			create_jquery("350","700","1");
			for($c=1;$c<=$options['keywords'];$c++) {
				echo "<div id='tabs-".$c."'>";
				$keyword="keyword".$c;
				$riga=$wpdb->get_results("SELECT url,title,position,timestamp FROM ".$table_name." WHERE keyword='".$options[$keyword]."' ORDER BY id DESC LIMIT 0,30");
				$d=0;
				foreach($riga as $row) {
					$row_date[$d]="'".date("d-m",$row->timestamp)."'";
					$row_position[$d]=intval($row->position);
					$d++;
				}
				// CREATING CHART
				//create_chart("400","800",$table_name,"1",$c);
				$type="1";
				?>		
				<div id="container_graph<?php echo $c; ?>" style="margin: 0 auto;overflow:hidden;width:700px;height:350px"></div>
				<?php
				echo "<br><br>";
				echo "<h2>SERP Table</h2>";
				echo "<table id='wpgr_table'>
						<tr>
							<th>Day</th>
							<th>URL</th>
							<th>Title</th>
							<th>Position</th>
						</tr>";
				foreach($riga as $row) {
					echo "<tr class='col'><td>".date("j-n-Y",$row->timestamp).
					"</td><td>".$row->url.
					"</td><td style='width:300px;overflow:hidden'>".$row->title.
					"</td><td>".$row->position."</td></tr>";
				}
				echo "</table>";
				echo "</div>";
			}
			?>
				</div>
			</div>
			<?php
			ga_stats("visualization",get_option('siteurl'));
	}
}


if(!function_exists('add_pages')) {
	function add_pages() {
		/*
		*	ADDING PLUGIN PAGES
		*/
		//add_options_page('WP Google Ranking Options', 'WP Google Ranking', 8, 'home', 'wp_google_ranking');
		add_menu_page( "WPGR", "WPGR", 8, 'wp-serp' , 'wp_google_ranking');
		add_submenu_page("wp-serp", "Options", "Options", 10, 'wp-google-ranking/options.php' ,null);
		
	}
}

if(!function_exists('save_options')) {
	function save_options($opt_arr) {
		update_option('WP_GOOGLE_RANKING',$opt_arr);
	}
}

if(!function_exists('get_options')) {
	function get_options() {
		return get_option("WP_GOOGLE_RANKING");
	}
}

if(!function_exists('dashboard_widget_serp')) {
	function dashboard_widget_serp() {
		$height="225";
		$width="500";
		global $wpdb;
		$table_name = $wpdb->prefix . "wp_serp";
		create_jquery("300","400","0");
		//create_chart($height,$width,$table_name,"2");
	}
}

if(!function_exists('add_dashboard_widget')) {
	function add_dashboard_widget() {
		wp_add_dashboard_widget('dashboard_widget', 'Dashboard WP SERP Widget', 'dashboard_widget_serp');
			// Globalize the metaboxes array, this holds all the widgets for wp-admin
		global $wp_meta_boxes;
		
		// Get the regular dashboard widgets array 
		// (which has our new widget already but at the end)

		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		
		// Backup and delete our new dashbaord widget from the end of the array

		$example_widget_backup = array('dashboard_widget' => $normal_dashboard['dashboard_widget']);
		unset($normal_dashboard['dashboard_widget']);

		// Merge the two arrays together so our widget is at the beginning

		$sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);

		// Save the sorted array back into the original metaboxes 

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

	}
}

if(!function_exists('google_check_cron')) {
	function google_check_cron() {
			$options=get_options();
			global $wpdb;
			// TODAY IS..
			$day=date("j");
			$month=date("n");
			$year=date("Y");
			$timestamp=time();
			$timestamp_today_midnight=mktime(0,0,5,$month,$day,$year);
			$table_name = $wpdb->prefix . "wp_serp";
			for($c=1;$c<=$options['keywords'];$c++) {
				$keyword="keyword".$c;
				$domain="domain".$c;
				if (($riga=$wpdb->get_var("SELECT timestamp FROM ".$table_name." WHERE keyword='".$options[$keyword]."' ORDER BY id DESC LIMIT 0,1")) < $timestamp_today_midnight ) {
					$position_serp=get_ranking($options,$c);
					//print_r($position_serp);
					$history=$wpdb->get_var("SELECT history FROM ".$table_name." ORDER BY id DESC LIMIT 0,1");
					$wpdb->query("INSERT INTO ".$table_name." (domain,url,title,position,timestamp,history,keyword) VALUES ('".$options[$domain]."','".$position_serp['url']."','".$position_serp['title']."','".$position_serp['position']."','".$timestamp."','".$history."','".$options[$keyword]."')");
					ga_stats("cron",get_option('siteurl'));
				}
			}
	}
}

function add_my_stylesheet() {
	$myStyleUrl = WP_PLUGIN_URL . '/wp-google-ranking/css/style.css';
	$myStyleFile = WP_PLUGIN_DIR . '/wp-google-ranking/css/style.css';
	if ( file_exists($myStyleFile) ) {
		wp_register_style('myStyleSheets', $myStyleUrl);
		wp_enqueue_style('myStyleSheets');
	}
}


add_action('wp_dashboard_setup', 'add_dashboard_widget' );


/*function testo() {
	echo "aaaaaaaaaaaaa!";
}

add_action('admin_footer', 'testo');
*/
?>