<div id='wp-google-ranking' class='wrap'><br>
<h2>WP Google Ranking Configuration</h2>
<hr>
<?php
	if (!empty($_POST)) {
		save_options($_POST);
		global $wpdb;
		$options=get_options();
		$table_name = $wpdb->prefix . "wp_serp";
		$sql="";
		for ($c=5;$c>$options['keywords'];$c--) {
			$domain="domain".$c;
			$keyword="keyword".$c;
			//echo "deleted ".$options[$domain];
			$sql="DELETE FROM " . $table_name . " WHERE domain='".$options[$domain]."' AND keyword='".$options[$keyword]."'";
			$tmp=$wpdb->query($sql);
		}
		//$sql = "TRUNCATE TABLE " . $table_name . ";";
		//$tmp=$wpdb->query($sql);
		//echo $sql;
	}
	$options=get_options();
	switch($options['searchengine']) {
		case "googlecom": $googlecom="checked=\"checked\""; break;
		case "googleit": $googleit="checked=\"checked\""; break;
		case "googlecouk": $googlecouk="checked=\"checked\""; break;
		case "googlede": $googlede="checked=\"checked\""; break;
		case "googlees": $googlees="checked=\"checked\""; break;
		case "googlefr": $googlefr="checked=\"checked\""; break;
		case "googlese": $googlese="checked=\"checked\""; break;
	}
?>
<script type="text/javascript">
var js_on={ <?php 
	foreach ($options as $key => $value) {
		echo "\"".$key."\" : \"" .$value ."\",\n";
	}
 ?> };
 var kwds_1_s;var kwds_2_s;var kwds_3_s;var kwds_4_s;var kwds_5_s;
 switch (js_on.keywords){
	case 1: kwds_1_s="selected";
	case 2: kwds_2_s="selected";
	case 3: kwds_3_s="selected";
	case 4: kwds_4_s="selected";
	case 5: kwds_5_s="selected";
 }
 var asd="<tr><td>Number of Keywords: </td><td><select name='keywords' style='width:40px' onchange='nb_keywords();' id='select_nbkwds'><option value='1' "+kwds_1_s+">1</option><option value='2'"+kwds_2_s+">2</option><option value='3' "+kwds_3_s+">3</option><option value='4'"+kwds_4_s+">4</option><option value='5'"+kwds_5_s+">5</option></select></td></tr><tr><td>The Keyword to display in the Dashboard: </td><td><select name='keywords_widget' style='width:40px'><option value='1' <?php echo $options['keywords_widget']=="1" ? "selected":""; ?>>1</option><option value='2' <?php echo $options['keywords_widget']=="2" ? "selected":""; ?>>2</option><option value='3' <?php echo $options['keywords_widget']=="3" ? "selected":""; ?>>3</option><option value='4' <?php echo $options['keywords_widget']=="4" ? "selected":""; ?>>4</option><option value='5' <?php echo $options['keywords_widget']=="5" ? "selected":""; ?>>5</option></select></td></tr>";
	function nb_keywords(){
		var old_number=js_on.keywords;
		var nb_kwds=document.wpgr.keywords.value;
		var injection="";
		var c;
		var googleitd="";var googlecomd="";var googlecoukd="";var googleded="";var googleesd="";var googlefrd="";var googlesed="";
		var defaultse=new Array();
		for(c=1;c<=5;c=c+1) {
			var se="searchengine"+c;
			switch(js_on[se]) {
				case 'googleit': defaultse[c]=1; break;
				case 'googlecom': defaultse[c]=2; break;
				case 'googlecouk': defaultse[c]=3; break;
				case 'googlede': defaultse[c]=4; break;
				case 'googlees': defaultse[c]=5; break;
				case 'googlefr': defaultse[c]=6; break;
				case 'googlese': defaultse[c]=7; break;
			}
		}
		if (parseInt(old_number,10)<parseInt(nb_kwds,10)) {//alert(parseInt(old_number));alert(parseInt(nb_kwds));
			for(c=parseInt(old_number,10)+1;c<parseInt(nb_kwds,10)+1;c=c+1) {
				var domain="domain"+c;
				var keyword="keyword"+c;
				var pages="pages"+c;
				var searchengine="searchengine"+c;
				if (js_on[domain] === undefined) {js_on[domain]="";}
				if (js_on[keyword] === undefined) {js_on[keyword]="";}
				if (js_on[pages] === undefined) {js_on[pages]="";}
				switch(defaultse[c]) {
					case 1: googleitd="checked"; break;
					case 2: googlecomd="checked"; break;
					case 3: googlecoukd="checked"; break;
					case 4: googleded="checked"; break;
					case 5: googleesd="checked"; break;
					case 6: googlefrd="checked"; break;
					case 7: googlesed="checked"; break;
				}
				//alert(googleitd[c+1]);
				injection="<tr><td colspan='2'>&nbsp;</td></tr><tr><td colspan='2'><span style='text-decoration:underline;font-size:18px;font-weight:bold;'>Keyword Nb."+ c +"</span></td></tr><tr><td>Domain: </td><td><input type='text' name='"+ domain +"' value='" + js_on[domain] +"'></td></tr><tr><td>Keyword: </td><td><input type='text' name='" + keyword + "' value='" + js_on[keyword] +"'></td></tr><tr><td><b>NEW</b> Nb. Pages to Check: </td><td><input type='text' name='"+ pages +"' value='"+ js_on[pages] +"'></td></tr><tr><td>Search Engines: </td><td><label><input type='radio' name='"+searchengine+"' value='googleit' "+googleitd+" />Google.it</label> <label><input type='radio' name='"+searchengine+"' value='googlecom' "+googlecomd+"/>Google.com</label> <label><input type='radio' name='"+searchengine+"' value='googlecouk' "+googlecoukd+"/>Google.co.uk</label> <label><input type='radio' name='"+searchengine+"' value='googlede' "+googleded+"/>Google.de</label> <label><input type='radio' name='"+searchengine+"' value='googlees' "+googleesd+"/>Google.es</label> <label><input type='radio' name='"+searchengine+"' value='googlefr' "+googlefrd+"/>Google.fr</label> <label><input type='radio' name='"+searchengine+"' value='googlese' "+googlesed+"/>Google.se</label></td></tr>";
				jQuery("#wpgr_table_options").append(injection);
			}
			js_on.keywords=parseInt(old_number,10)+parseInt(nb_kwds,10)-1;
			//alert(js_on['keywords']);
		}
		else {
			for(c=1;c<parseInt(nb_kwds,10)+1;c=c+1) {
				var domain="domain"+c;
				var keyword="keyword"+c;
				var pages="pages"+c;
				var searchengine="searchengine"+c;
				if (js_on[domain] === undefined) {js_on[domain]="";}
				if (js_on[keyword] === undefined) {js_on[keyword]="";}
				if (js_on[pages] === undefined) {js_on[pages]="";}
				switch(defaultse[c]) {
					case 1: googleitd="checked"; break;
					case 2: googlecomd="checked"; break;
					case 3: googlecoukd="checked"; break;
					case 4: googleded="checked"; break;
					case 5: googleesd="checked"; break;
					case 6: googlefrd="checked"; break;
					case 6: googlesed="checked"; break;
				}
				//alert(googleitd[c+1]);
				injection+="<tr><td colspan='2'>&nbsp;</td></tr><tr><td colspan='2'><span style='text-decoration:underline;font-size:18px;font-weight:bold;'>Keyword Nb."+ c +"</span></td></tr><tr><td>Domain: </td><td><input type='text' name='"+ domain +"' value='" + js_on[domain] +"'></td></tr><tr><td>Keyword: </td><td><input type='text' name='" + keyword + "' value='" + js_on[keyword] +"'></td></tr><tr><td><b>NEW</b> Nb. Pages to Check: </td><td><input type='text' name='"+ pages +"' value='"+ js_on[pages] +"'></td></tr><tr><td>Search Engines: </td><td><label><input type='radio' name='"+searchengine+"' value='googleit' "+googleitd+" />Google.it</label> <label><input type='radio' name='"+searchengine+"' value='googlecom' "+googlecomd+"/>Google.com</label> <label><input type='radio' name='"+searchengine+"' value='googlecouk' "+googlecoukd+"/>Google.co.uk</label> <label><input type='radio' name='"+searchengine+"' value='googlede' "+googleded+"/>Google.de</label> <label><input type='radio' name='"+searchengine+"' value='googlees' "+googleesd+"/>Google.es</label> <label><input type='radio' name='"+searchengine+"' value='googlefr' "+googlefrd+"/>Google.fr</label> <label><input type='radio' name='"+searchengine+"' value='googlese' "+googlesed+"/>Google.se</label></td></tr>";
			}
			jQuery("#wpgr_table_options").html(asd+injection);
			js_on.keywords=parseInt(nb_kwds,10);
			//alert(js_on.keywords);
		}

		
	}
	jQuery(document).ready(function() {
		var defaultse=new Array();
		var c;
		for(c=1;c<=5;c=c+1) {
			var se="searchengine"+c;
			switch(js_on[se]) {
				case 'googleit': defaultse[c]=1; break;
				case 'googlecom': defaultse[c]=2; break;
				case 'googlecouk': defaultse[c]=3; break;
				case 'googlede': defaultse[c]=4; break;
				case 'googlees': defaultse[c]=5; break;
				case 'googlefr': defaultse[c]=6; break;
				case 'googlese': defaultse[c]=7; break;
			}
			//alert(googleitd[c]);
			//dump(googleitd);
		}
		var googleitd;var googlecomd;var googlecoukd;var googleded;var googleesd;var googlefrd;var googlesed;
		for (c=1;c<=js_on.keywords;c=c+1) {
			var domain="domain"+c;
			var keyword="keyword"+c;
			var pages="pages"+c;
			var searchengine="searchengine"+c;
			switch(defaultse[c]) {
				case 1: googleitd="checked"; break;
				case 2: googlecomd="checked"; break;
				case 3: googlecoukd="checked"; break;
				case 4: googleded="checked"; break;
				case 5: googleesd="checked"; break;
				case 6: googlefrd="checked"; break;
				case 7: googlesed="checked"; break;
			}
			injection="<tr><td colspan='2'>&nbsp;</td></tr><tr><td colspan='2'><span style='text-decoration:underline;font-size:18px;font-weight:bold;'>Keyword Nb."+ c +"</span></td></tr><tr><td>Domain: </td><td><input type='text' name='"+ domain +"' value='" + js_on[domain] +"'></td></tr><tr><td>Keyword: </td><td><input type='text' name='" + keyword + "' value='" + js_on[keyword] +"'></td></tr><tr><td><b>NEW</b> Nb. Pages to Check: </td><td><input type='text' name='"+ pages +"' value='"+ js_on[pages] +"'></td></tr><tr><td>Search Engines: </td><td><label><input type='radio' name='"+searchengine+"' value='googleit' "+googleitd+" />Google.it</label> <label><input type='radio' name='"+searchengine+"' value='googlecom' "+googlecomd+"/>Google.com</label> <label><input type='radio' name='"+searchengine+"' value='googlecouk' "+googlecoukd+"/>Google.co.uk</label> <label><input type='radio' name='"+searchengine+"' value='googlede' "+googleded+"/>Google.de</label> <label><input type='radio' name='"+searchengine+"' value='googlees' "+googleesd+"/>Google.es</label> <label><input type='radio' name='"+searchengine+"' value='googlefr' "+googlefrd+"/>Google.fr</label> <label><input type='radio' name='"+searchengine+"' value='googlese' "+googlesed+"/>Google.se</label></td></tr>";
			jQuery("#wpgr_table_options").append(injection);
		}
	});
</script>
<?php
	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="wpgr">';
?>
	<table id="wpgr_table_options">
	<tr><td>Number of Keywords: </td>
	<td><select name='keywords' style="width:40px" onchange="nb_keywords();" id="select_nbkwds">
		<option value='1' <?php echo $options['keywords']=="1" ? "selected":""; ?>>1</option>
		<option value='2' <?php echo $options['keywords']=="2" ? "selected":""; ?>>2</option>
		<option value='3' <?php echo $options['keywords']=="3" ? "selected":""; ?>>3</option>
		<option value='4' <?php echo $options['keywords']=="4" ? "selected":""; ?>>4</option>
		<option value='5' <?php echo $options['keywords']=="5" ? "selected":""; ?>>5</option>
	</select></td></tr>
	<tr><td>The Keyword to display in the Dashboard: </td>
	<td><select name='keywords_widget' style="width:40px">
		<option value='1' <?php echo $options['keywords_widget']=="1" ? "selected":""; ?>>1</option>
		<option value='2' <?php echo $options['keywords_widget']=="2" ? "selected":""; ?>>2</option>
		<option value='3' <?php echo $options['keywords_widget']=="3" ? "selected":""; ?>>3</option>
		<option value='4' <?php echo $options['keywords_widget']=="4" ? "selected":""; ?>>4</option>
		<option value='5' <?php echo $options['keywords_widget']=="5" ? "selected":""; ?>>5</option>
	</select></td></tr>
<?php
	/*for($c=1;$c<=$options['keywords'];$c++) {
		$domain="domain".$c;
		$keyword="keyword".$c;
		$pages="pages".$c;
		$searchengine="searchengine".$c;
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
		echo '<tr><td colspan="2"><span style="text-decoration:underline;font-size:18px;font-weight:bold;">Keyword Nb.'.$c.'</span></td></tr>';
		echo '<tr><td>Domain: </td><td><input type="text" name="'.$domain.'" value="'.$options[$domain].'"></td></tr>';
		echo '<tr><td>Keyword: </td><td><input type="text" name="'.$keyword.'" value="'.$options[$keyword].'"></td></tr>';
		echo '<tr><td><b>NEW</b> Nb. Pages to Check: </td><td><input type="text" name="'.$pages.'" value="'.$options[$pages].'"></td></tr>';
		//echo '<tr><td><i>Days:</i> </td><td><input type="text" name="days'.$c.'" value="'.$options['days'].'"></td></tr>';
		?>
		<tr><td>Search Engines: </td><td>
			  <label><input type='radio' name="<?php echo $searchengine; ?>" value='googleit' <?php echo $options[$searchengine]=="googleit" ? "checked=\"checked\"":""; ?>/>Google.it</label>
			  <label><input type='radio' name="<?php echo $searchengine; ?>" value='googlecom' <?php echo $options[$searchengine]=="googlecom" ? "checked=\"checked\"":""; ?> />Google.com</label>
			  <label><input type="radio" name="<?php echo $searchengine; ?>" value="googlecouk" <?php echo $options[$searchengine]=="googlecouk" ? "checked=\"checked\"":""; ?>/>Google.co.uk</label>
			  <label><input type="radio" name="<?php echo $searchengine; ?>" value="googlede" <?php echo $options[$searchengine]=="googlede" ? "checked=\"checked\"":""; ?>/>Google.de</label>
			  <label><input type="radio" name="<?php echo $searchengine; ?>" value="googlees" <?php echo $options[$searchengine]=="googlees" ? "checked=\"checked\"":""; ?>/>Google.es</label>
			  <label><input type="radio" name="<?php echo $searchengine; ?>" value="googlefr" <?php echo $options[$searchengine]=="googlefr" ? "checked=\"checked\"":""; ?>/>Google.fr</label></td></tr>
		<?php
	}*/
	echo '</table>';
	echo "<div class='submit'><input type=\"submit\" value=\"Send the data\"></input></div></form>";
	echo "<hr>";
	//echo "<i>Italic parameters are under development</i>";
	//print_r($options);
?>
</div>