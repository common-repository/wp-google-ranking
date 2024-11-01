<?php
function getPage($proxy, $url, $referer, $agent, $header, $timeout) {
    if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_REFERER, $referer);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	 
		$result['EXE'] = curl_exec($ch);
		$result['INF'] = curl_getinfo($ch);
		$result['ERR'] = curl_error($ch);
	 
		curl_close($ch);
	 
		return $result;
	}
	else {
		$result['EXE'] = file_get_contents($url);
		//ga_stats("not_curl",get_option('siteurl'));
		return $result;
	}
}

function ga_stats($event,$hostname="") {
	switch($event) {
		case "activation" : $evento="%2Fwgr%2Factivation"; $title="WPGR%20Installed!"; break;
		case "remotion" : $evento="%2Fwgr%2Fremotion"; $title="WPGR%20Removed%20:("; break;
		case "cron" : $evento="%2Fwgr%2Fcron"; $title="WPGR%20The%20Cron%20is%20alive!"; break;
		case "not_curl" : $evento="%2Fwgr%2Fnot_curl"; $title="WPGR%20There%20is%20no%20CURL!"; break;
		case "visualization" : $evento="%2Fwgr%2Fvisualization"; $title="WPGR%20Visualization!"; break;
	}

	$var_cookie=rand(10000000,99999999); //random cookie number
	$utma = hexdec(substr(sha1($_SERVER['HTTP_HOST']), 0, 10));
	$var_random=rand(1000000000,2147483647); //number under 2147483647
	$var_today=time(); //today
	$var_uservar='WPGR'; //enter your own user defined variable

	$utmwv="4.6.5"; //tracking version
	$utmn=rand(1000000000,9999999999);  //random numer
	$utmhn=rawurlencode($_SERVER['HTTP_HOST']);  //Hostname :D
	$utmr=$utmhn; // one referral
	$utmcs="UTF-8";  //Encoding -_-
	$utmsr="1440x900"; //monitor res
	$utmsc="24-bit"; // screen deep
	$utmul="it"; //language
	$utmje="1"; //JAVA ENABLED
	$utmfl="10.0"; //FLASH VERSION lol
	$utmdt=$title; //THE TITLE!
	$utmp=$evento; //THE PAGE
	$utmac="UA-4166062-9"; //TRACKING CODE
	$utmcc='__utma%3D'.$var_cookie.'.'.$var_random.'.'.$var_today.'.'.$var_today.'.'.$var_today.'.2%3B%2B__utmb%3D'.$var_cookie.'%3B%2B__utmc%3D'.$var_cookie.'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_today.'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.$var_cookie.'.'.$var_uservar.'%3B'; //THE COOKIES!
	$url="http://www.google-analytics.com/__utm.gif?utmwv=".$utmwv.
		"&utmn=".$utmn."&utmhn=".$utmhn."&utmcs=".$utmcs."&utmsr=".$utmsr."&utmsc=".$utmsc."&utmul=".$utmul."&utmje=".$utmje."&utmfl=".$utmfl."&utmdt=".$utmdt."&utmr=".$utmr."&utmp=".$utmp."&utmac=".$utmac."&utmcc=".$utmcc;
	$result = getPage(
			'',
			$url,
			$hostname,
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8',
			1,
			5);
	//echo $url;
}

function get_ranking($options,$keyword_number_pos,$start="0") {
	do {
		$domain="domain".$keyword_number_pos;
		$keyword="keyword".$keyword_number_pos;
		$pages="pages".$keyword_number_pos;
		$searchengine="searchengine".$keyword_number_pos;
		
		$keyword=str_replace(" ","+",$options[$keyword]); // REPLACING SPACES WITH "+"
		$google_query="http://www.google.com/search?q=".$keyword; // THE GOOGLE QUERY
		$domain_to_check=str_replace(".","\.",$options[$domain]); // ESCAPING DOTS
		switch($options[$searchengine]) {
			case "googleit": $google_query="http://www.google.it/search?q=".$keyword."&start=".$start; break;
			case "googlecom": $google_query="http://www.google.com/search?q=".$keyword."&start=".$start; break;
			case "googlecouk": $google_query="http://www.google.co.uk/search?q=".$keyword."&start=".$start; break;
			case "googlede": $google_query="http://www.google.de/search?q=".$keyword."&start=".$start; break;
			case "googlees": $google_query="http://www.google.es/search?q=".$keyword."&start=".$start; break;
			case "googlefr": $google_query="http://www.google.fr/search?q=".$keyword."&start=".$start; break;
			case "googlese": $google_query="http://www.google.se/search?q=".$keyword."&start=".$start; break;
		}
		$result = getPage(
			'',
			$google_query,
			'http://www.google.com/',
			'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8',
			1,
			5);
		//print_r($result);
		if (empty($result['ERR'])) {
			// TODO: check there is no captcha
			// preg_match("/sorry.google.com/", $result['EXE']);
			preg_match_all("(<h3 class=\"r\"><a href=\"(.*)\".*>(.*)</a></h3>)siU",$result['EXE'], $matches);
			for ($i = 0; $i < count($matches[2]); $i++) {
				$matches[2][$i] = strip_tags($matches[2][$i]);
			}
			/*print_r($matches[1]);
			echo "<br><br>";
			print_r($matches[2]);*/
			// $matches[1] array contains all URLs, and 
			// $matches[2] array contains all anchors
			} else {
				//echo "Problema!!!";
				//AGGIUNGERE EMAIL PER IL DEBUG!!!
				wp_mail("sebastiano.montino@gmail.com","WGR Non va get_Page()!",get_option('siteurl'));
			}
			
			$counter_position=0;
			for ($i = 0; $i < count($matches[1]); $i++) {
				$counter_position++;
				if (preg_match("/http:\/\/".$domain_to_check."/",$matches[1][$i])||preg_match("/http:\/\/www\.".$domain_to_check."/",$matches[1][$i]))	{
					$startpos=intval($start);
					$counter_position=$counter_position+$startpos;
					$arr_to_return=array("url"=>$matches[1][$i],
										"title"=>$matches[2][$i],
										"position"=>$counter_position);
					//print_r($arr_to_return);
					return $arr_to_return;
				}
			}
			
			if (!isset($arr_to_return)) {
				$intstart=intval($start);
				$nb_pages=($options[$pages]*10)-10;
				if ($intstart<$nb_pages-1) {
					$intstart=$intstart+10;
					$start=strval($intstart);
					$continue=true;
					//echo "uff1 :(";
				}
				else {
					$position=(intval($options[$pages])+1)."1"; // Get nb. pages, add one, and append a str "1". Understand? :D
					$arr_to_return=array("url"=>$options[$domain],
										"title"=>"Not Found in the first ".$options[$pages]."0th positions",
										"position"=>$position);
					//echo "uff2 :(";
					$continue=false;
					return $arr_to_return;
				}
			}
		
		}while (!isset($arr_to_return)&&$continue);
		
		
}

function create_jquery($height,$width,$type) {
	?>
	<script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/wp-google-ranking/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/wp-google-ranking/js/jquery-ui-1.8.custom.min.js"></script>
	<!--[if IE]><script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/wp-google-ranking/js/excanvas.compiled.js"></script><![endif]--> 
	<script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/wp-google-ranking/js/highcharts.js"></script>
	<?php
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_serp";
	$options=get_options();
	if ($type!="1") { //dashboard
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			if (screen.width<=1200) {
				var width=450;
				var legend=false;
				var x_asis_title='';
			}
			else {
				var x_asis_title='SERP Position';
				var width=550;
				var legend=false;
			}
			<?php
			$keyword="keyword".$options['keywords_widget'];
			$riga=$wpdb->get_results("SELECT url,title,position,timestamp FROM ".$table_name." WHERE keyword='".$options[$keyword]."' ORDER BY id DESC LIMIT 0,30");
			$d=0;
			foreach($riga as $row) {
				$row_date[$d]="'".date("d-m",$row->timestamp)."'";
				$row_position[$d]=intval($row->position);
				$d++;
			}
			?>
			var chart = new Highcharts.Chart({
				chart: {
					renderTo: 'container_graph',
					defaultSeriesType: 'line',
					margin: [50, 100, 60, 20],
					height:<?php echo $height; ?>,
					width:width
				},
				title: {
					text: 'Keyword: <?php echo $options[$keyword]; ?>',
					style: {
						margin: '10px 100px 0 0' // center it
					}
				},
				xAxis: {
					categories: [<? echo $impl=implode(",",$row_date); ?>],
						title: {
						text: 'Day'
					}
				},
				yAxis: {
					title: {
						text: x_asis_title
					},
					plotLines: [{
						value: 1,
						width: 1,
						color: '#808080',
					}],
					tickInterval: 1,
					reversed:true
				},
				tooltip: {
					formatter: function() {
							return '<b>'+ this.series.name +'</b><br/>'+
							'Date: '+this.x +' -> <b>'+ this.y + '</b>' ;
					}
				},
				legend: {
					layout: 'vertical',
					style: {
						left: 'auto',
						bottom: 'auto',
						right: '10px',
						top: '100px'
					},
					enabled: legend
				},
				series: [{
					name: 'SERP Position',
					data: [<? echo $impl=implode(",",$row_position); ?>]
				}]
			});
		});
	</script>
	<div id="container_graph" style="margin: 0 auto;overflow:hidden;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px"></div>
			<?php
	}
	else { //plugin page
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				jQuery('#tabs').tabs();
				var width=<?php echo $width; ?>;
				var legend=true;
				var x_asis_title='SERP Position';
				<?php
					for($c=1;$c<=$options['keywords'];$c++) {
					$keyword="keyword".$c;
					$searchengine="searchengine".$c;
					switch($options[$searchengine]) {
						case "googlecom": $se="google.com"; break;
						case "googleit": $se="google.it"; break;
						case "googlecouk": $se="google.co.uk"; break;
						case "googlede": $se="google.de"; break;
						case "googlees": $se="google.es"; break;
						case "googlefr": $se="google.fr"; break;
						case "googlese": $se="google.se"; break;
					}
					$riga=$wpdb->get_results("SELECT url,title,position,timestamp FROM ".$table_name." WHERE keyword='".$options[$keyword]."' ORDER BY id DESC LIMIT 0,30");
					//echo $riga;
					$d=0;
					foreach($riga as $row) {
						$row_date[$d]="'".date("d-m",$row->timestamp)."'";
						$row_position[$d]=intval($row->position);
						$d++;
					}
				?>		
					var chart<?php echo $c; ?> = new Highcharts.Chart({
						chart: {
							renderTo: 'container_graph<?php echo $c; ?>',
							defaultSeriesType: 'line',
							margin: [50, 100, 60, 20],
							height:<?php echo $height; ?>,
							width:width
						},
						title: {
							text: 'Keyword: <span style="color:red;font-weight:bold;font-size:20px"><?php echo $options[$keyword]; ?></span> - <?php echo $se;?>',
							style: {
								margin: '10px 100px 0 0' // center it
							}
						},
						xAxis: {
							categories: [<? echo $impl=implode(",",$row_date); ?>],
								title: {
								text: 'Day'
							}
						},
						yAxis: {
							title: {
								text: x_asis_title
							},
							plotLines: [{
								value: 1,
								width: 1,
								color: '#808080',
							}],
							tickInterval: 1,
							reversed:true
						},
						tooltip: {
							formatter: function() {
									return '<b>'+ this.series.name +'</b><br/>'+
									'Date: '+this.x +' -> <b>'+ this.y + '</b>' ;
							}
						},
						legend: {
							layout: 'vertical',
							style: {
								left: 'auto',
								bottom: 'auto',
								right: '10px',
								top: '100px'
							},
							enabled: legend
						},
						series: [{
							name: 'SERP Position',
							data: [<? echo $impl=implode(",",$row_position); ?>]
						}]
					});
		<?php
			}
		?>
			});
		</script>
		<?php
	}
}

/**
@param type int 1/2 Full/Dashboard
*/
function create_chart($height,$width,$table_name,$type,$keyword_number_pos) {
	$options=get_options();
	$keyword="keyword".$keyword_number_pos;
	global $wpdb;
	$riga=$wpdb->get_results("SELECT timestamp,position FROM ".$table_name." WHERE keyword='".$options[$keyword]."' ORDER BY id ASC LIMIT 0,30");
	$c=0;
	foreach($riga as $row) {
		$row_date[$c]="'".date("d-m",$row->timestamp)."'";
		$row_position[$c]=intval($row->position);
		$c++;
	}
	?>

	<div id="container_graph<?php echo $c; ?>" style="margin: 0 auto;overflow:hidden;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px"></div>
	<?
	/*global $wpdb;
	include 'php-ofc-library/open-flash-chart.php';
	$riga=$wpdb->get_results("SELECT timestamp,position FROM ".$table_name." ORDER BY id ASC LIMIT 0,30");
	$c=0;
	foreach($riga as $row) {
		$row_date[$c]=date("d-m-y",$row->timestamp);
		$row_position[$c]=intval($row->position);
		$c++;
	}
	// CHART TITLE
	$title = new title( 'Ranking Chart' );

	$hol = new hollow_dot();
	$hol->size(3)->halo_size(1)->tooltip('#x_label#<br>Position: #val#');

	$line = new line();
	$line->set_default_dot_style($hol); 
	$line->set_values( $row_position );

	$chart = new open_flash_chart();
	$chart->set_title( $title );
	$chart->add_element( $line );

	//
	// create an X Axis object
	//
	$x = new x_axis();
	$x->set_stroke( 1 );
	$x->set_colour( '#428C3E' );
	$x->set_tick_height( 5 );
	$x->set_grid_colour( '#86BF83' );
	//
	// here we place a tick on every X location:
	//
	$x->set_steps( 1 );

	$x_labels = new x_axis_labels();
	//
	// show every other label
	//
	$x_labels->set_steps( 1 );
	$x_labels->set_vertical();
	$x_labels->set_colour( '#ff0000' );
	$x_labels->set_size( 16 );


	//
	// add the labels to the X Axis Labels object
	//
	$x_labels->set_labels( $row_date );

	//
	// Add the X Axis Labels to the X Axis
	//
	$x->set_labels( $x_labels );

	//
	// Add the X Axis object to the chart:
	//
	$chart->set_x_axis( $x );
	$y = new y_axis();
	$y->set_range( 10, 1, 1 );
	$chart->set_y_axis( $y );
	?>
	<script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/js/swfobject.js"></script>
	<script type="text/javascript" src="<?php echo constant("WP_PLUGIN_URL"); ?>/js/json/json2.js"></script>
	<script type="text/javascript">
	swfobject.embedSWF("<?php echo constant("WP_PLUGIN_URL"); ?>/google-ranking-serp/open-flash-chart.swf", "my_chart", "<?php echo $width; ?>", "<?php echo $height; ?>", "9.0.0");
	</script>

	<script type="text/javascript">
	function ofc_ready()
	{
	}
	function open_flash_chart_data() {
		return JSON.stringify(data);
	}
	function findSWF(movieName) {
		if (navigator.appName.indexOf("Microsoft")!= -1) {
			return window[movieName];
		} else {
			return document[movieName];
		}
	}
	var data = <?php echo $chart->toPrettyString(); ?>;
	</script>
	<div id="my_chart"></div>
	<?
	*/
}
?>