<?php 
/*
Plugin Name: Bits2Bytes
Plugin URI: http://wordpress.org/extend/plugins/bits2bytes
Description: Bits2Bytes is a wordpress simple/yet powerful widget, that allow users convert <a target='_blank' href='https://en.wikipedia.org/wiki/Units_of_information'>computer data units</a> to each other.
Version: 1.0
Author: MostafaS
Author URI: https://profiles.wordpress.org/mostafas
*/

/*  Copyright 2015  Mostafa

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



//Load Text Domain
function bits2bytes_load_textdomain() {
	load_plugin_textdomain( 'bits2bytes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_filter( 'wp_loaded', 'bits2bytes_load_textdomain' );

if ( !defined( 'B2B_Widget_PATH' ) )
	define( 'B2B_Widget_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined( 'B2B_Widget_BASENAME' ) )
	define( 'B2B_Widget_BASENAME', plugin_basename( __FILE__ ) );
if(function_exists(plugins_url))
	{
		define( 'B2B_PLUGIN_URL', plugins_url() );
	}

// use widgets_init Action hook to execute custom function
add_action( 'widgets_init', 'b2b_register_widgets' );

 //register our widget
function b2b_register_widgets() {
    register_widget( 'b2b_widget' );
}



//b2b_widget class
class b2b_widget extends WP_Widget {

    //process our new widget
    function b2b_widget() {
        $widget_ops = array('classname' => 'b2b_widget', 'description' => __('Allow users convert computer data units to each other','bits2bytes') ); 
        $this->WP_Widget('b2b_bits2bytes_widget', __('Bits2Bytes Widget','bits2bytes'), $widget_ops);
    }
 
     //build our widget settings form
    function form($instance) {
        $defaults = array( 'title' => __('Bits2Bytes','bits2bytes') ); 
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags($instance['title']);
		
        ?>
			<p><?php
			/* Translators: Title: Title of widget */
			_e('Title','bits2bytes') ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>"  type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }

    //save our widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
 
        return $instance;
    }
 
    //display our widget
    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
        $title = apply_filters('widget_title', $instance['title'] );
        //$name = empty($instance['name']) ? '&nbsp;' : apply_filters('widget_name', $instance['name']);
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		$style_url = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/style.css";
		$script_ajax = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/ajax_request.js";
		$script_serialization = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/serialization.js";
		$script_bits2bytes = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/bits2bytes.js";
		$loader_gif = B2B_PLUGIN_URL."/bits2bytes/bits2bytes/loader.gif";
		wp_enqueue_style( 'style', $style_url );
		wp_enqueue_script( 'ajax_request', $script_ajax);
		wp_enqueue_script( 'serialization', $script_serialization);
		//Start main widget elements
	echo "<div id='main_box' class='container'>";
	echo "<div class='row'>";
	//echo "<div id='title' class='col-md-12'>Bits2Bytes</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div id='form_wrapper' class='col-md-12'>";
	echo "<form name='frm1' id='main_form'>";
	echo "<input type='text' name='amountField' id='amountField' class='field1' placeholder='". /* Translators: form input placeholder */__('Convert','bits2bytes') ."' title='".__('Enter numeric value','bits2bytes')."'/>";
	echo "<select id='unitList' name='unit' class='field2'>";
	echo "<option value='b'>".__('bits','bits2bytes')."</option>";
	echo "<option value='B'>".__('bytes','bits2bytes')."</option>";
	echo "<option value='kb'>".__('kilobits','bits2bytes')."</option>";
	echo "<option value='kB'>".__('kilobytes','bits2bytes')."</option>";
	echo "<option value='mb'>".__('megabits','bits2bytes')."</option>";
	echo "<option value='mB'>".__('megabytes','bits2bytes')."</option>";
	echo "<option value='gb'>".__('gigabits','bits2bytes')."</option>";
	echo "<option value='gB'>".__('gigabytes','bits2bytes')."</option>";
	echo "<option value='tb'>".__('terabits','bits2bytes')."</option>";
	echo "<option value='tB'>".__('terabytes','bits2bytes')."</option>";
	echo "<option value='pb'>".__('petabits','bits2bytes')."</option>";
	echo "<option value='pB'>".__('petabytes','bits2bytes')."</option>";
	echo "</select>";
	echo "<!--<button id='in_submit'>></button>-->";
	echo "<!-- <p id='in_submit' class='go'></p> -->";
	echo "<!--<img src='./btn.png' class='btn' id='in_submit'/>-->";
	echo "<input type='button' id='in_submit' title='".__('Convert','bits2bytes')."'/>";
	echo "</form>";
	echo "<div id='loader'><img src='".$loader_gif."' style='width: 190px; height: 20px;' title='".__('Please wait...','bits2bytes')."'/><small><b>".__('Please wait...','bits2bytes')."</b></small></div>";
	echo "</div>";
	echo "</div>";
	echo "<div class='row'>";
	echo "<div id='results' class='col-md-12'>";

	echo "<table id='resultTable'><tr><td width='10%'>".__('bits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('bytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('kilobits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('kilobytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('megabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('megabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('gigabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('gigabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('terabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('terabytes','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('petabits','bits2bytes').": </td><td>?</td></tr><tr><td width='10%'>".__('petabytes','bits2bytes').": </td><td>?</td></tr></table>";
	//echo "<center><small>By <a href='http://wordpress.org/extend/plugins/bits2bytes/'>Bits2Bytes</a></small></center>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
		//End main widget elements
		
		//Start Bits2Bytes.js File
		?>
		<script type="text/javascript">
/*
	@Copyright Bits2Bytes By Mostafa
	@Debug Info: this line added just for debug purposes
*/
//check for offline or online instead of using ajax request function
function checkOfflineOnline() {
    var state = navigator.onLine ? "online" : "offline";
    return state;
}
//Hide loader
document.getElementById("loader").style.display="none"; //can use css instead
document.getElementById("in_submit").onclick=function(event){
var BrowserStatus = checkOfflineOnline();
//check for number and not empty value
var input_value = document.getElementById("amountField").value;
if(input_value == "" || isNaN(input_value)){
	alert("<?php _e('Please enter a numeric value','bits2bytes'); ?>");
}
else if( BrowserStatus == "offline" ){
	alert("<?php _e('Bits2Bytes Need Active Internet Connection','bits2bytes'); ?>");
}
else
{
//Show loader
document.getElementById("loader").style.display="";
var resultTable = document.getElementById("resultTable");
resultTable.rows[0].cells[1].txtContent = "A";
var form = document.getElementById("main_form");
var results = document.getElementById("results");
var serialize_value = serialize(form);
//console.log(serialize_value);/*Debug Info*/
//var amountField = document.getElementById("amountField").value;
//var unit = document.getElementById("unitList").value;
		var XHR=new ajaxRequest();
				XHR.onreadystatechange=function(){
				if (XHR.readyState==4){
					if (XHR.status==200){ //We dont need this for addon--> || window.location.href.indexOf("http")==-1
						var response = XHR.responseText;
						//console.log(response);/*Debug Info*/
						var response = JSON.parse(response);
						//console.log(response);/*Debug Info*/
							if(response.ok == true){
							//use TextNode instead of innerHTML , because of Firefox Add On :(
							
							//now check each tr value and set new value (clean old value)
							var re = Array(12);
								re[0]=response.result.A;
								re[1]=response.result.B;
								re[2]=response.result.C;
								re[3]=response.result.D;
								re[4]=response.result.E;
								re[5]=response.result.F;
								re[6]=response.result.G;
								re[7]=response.result.H;
								re[8]=response.result.I;
								re[9]=response.result.J;
								re[10]=response.result.K;
								re[11]=response.result.L;
								//check for delete append error when user calculate new value(delete old error
								//when user calculating new value)
								for(i=0;i<results.childNodes.length;i++){
									if(results.childNodes[i].nodeType == 3){
										var NodeForDel = results.childNodes[i];
										results.removeChild(NodeForDel);
									}
								}
								//now append table tr values
							for(i=0;i<resultTable.rows.length;i++){
							//console.log(resultTable.rows[i].cells[1].txtContent);
							if(resultTable.rows[i].cells[1].textContent != ""){
								var ChildNodeForDel = resultTable.rows[i].cells[1].childNodes[0];
								resultTable.rows[i].cells[1].removeChild(ChildNodeForDel);
								resultTable.rows[i].cells[1].appendChild(document.createTextNode(re[i]));//
								//resultTable.rows[i].cells[1].innerHTML=( document.createTextNode(response.result.j) ).nodeValue;
								
								
							}
							
							}

							//console.log(document.getElementById("resultTable"));/*Debug Info*/
							//console.log(results);/*Debug Info*/
						}
						else{
						window.alert("Response is not ok.");
						for(i=0;i<results.childNodes.length;i++){
							if(results.childNodes[i].nodeType == 3) {//Node is text
								results.childNodes[i].nodeValue = ""; // we dont remove node,just empty it
							}
						}

						console.log(results);
						console.log(response);
						}
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					else if(XHR.status == "0"){
						alert("<?php _e('An error occured making request...internet issue.','bits2bytes'); ?>"+XHR.status);
						//hide loader
						document.getElementById("loader").style.display="none";
						}
					else if(XHR.status == "301"){
						alert("<?php _e('Network Connection:301-Moved Permanently.','bits2bytes'); ?>");
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					else if(XHR.status == "304"){
						alert("<?php _e('Network Connection:304-Not Modified.','bits2bytes'); ?>");
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					else if(XHR.status == "404"){
						alert("<?php _e('Network Connection:404-Not Found.','bits2bytes'); ?>");
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					else if(XHR.status == "403"){
						alert("<?php _e('Network Connection:403-Forbidden.','bits2bytes'); ?>");
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					else if(XHR.status == "401"){
						alert("<?php _e('Network Connection:401-Unauthorized.','bits2bytes'); ?>");
						//hide loader
						document.getElementById("loader").style.display="none";
					}
					
					}
				}
				XHR.ontimeout=function(){
					alert("<?php _e('Timeout making request','bits2bytes'); ?>");
					//hide loader
					document.getElementById("loader").style.display="none";
				}
	
		
		var RequestURL = "http://bit2bytes.herokuapp.com/bit2bytes.php";
		//XHR.open("GET", RequestURL+"?amt="+amountField+"&unit="+unit, true); // For GET Request
		XHR.open("POST", RequestURL, true);
		XHR.setRequestHeader("content-type","application/json");//optional
		XHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		XHR.send(serialize_value);
		
		
		}//check input else
}
</script>
		<?php
		//End Bits2Bytes.js File
		
		//We add bits2bytes.js file directly...we need translate some text on it(we add it above here)
		//wp_enqueue_script( 'bits2bytes', $script_bits2bytes);
        echo $after_widget;
    }
}
?>
