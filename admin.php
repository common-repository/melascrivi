<?php
if ( !is_admin() )
	die();

require_once('melascrivi_php_client/MelascriviPlugin.php' );

define("WP_DB_NAME_MELASCRIVI_USERNAME","WP_DB_NAME_MELASCRIVI_USERNAME");

define("WP_DB_NAME_MELASCRIVI_PWD","WP_DB_NAME_MELASCRIVI_PWD");

define("WP_DB_NAME_MELASCRIVI_ID","WP_DB_NAME_MELASCRIVI_ID");

define("WP_DB_NAME_MELASCRIVI_WEBSITE","WP_DB_NAME_MELASCRIVI_WEBSITE");

define("WP_DB_NAME_MELASCRIVI_ACTKEY","WP_DB_NAME_MELASCRIVI_ACTKEY");

define("PLUGIN_VERSION","1.5.0");

function melascriviPlugin_init() {
  load_plugin_textdomain( 'melascrivi', false, dirname( plugin_basename( __FILE__ )). '/languages/' ); 	
  //melascrivi_wp_js_load();
}

function melascrivi_wp_js_load(){
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-mouse' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script('jquery-ui-datepicker');

	wp_enqueue_script('jquery-ui-notify', plugins_url()."/melascrivi/js/jquery.ui.notify.js", array('jquery'), false, false);
	wp_enqueue_script('jquery-ui-selectbox', plugins_url()."/melascrivi/js/jquery.customSelect.js", array('jquery'), false, false);
	wp_enqueue_script('jquery-ui-linedArea', plugins_url()."/melascrivi/js/jquery-linedtextarea.js", array('jquery'), false, false);	
	wp_enqueue_script('jquery-dataTables', plugins_url()."/melascrivi/js/datatables/js/jquery.dataTables.min.js", array('jquery'), false, false);
	wp_enqueue_script('TableTools-mela', plugins_url()."/melascrivi/js/datatables/js/TableTools.min.js", array('jquery'), false, false);
	
	
	wp_enqueue_style( 'jquery-ui-datepicker' );
	wp_enqueue_style('slider-admin-ui-css',
                plugins_url() . '/melascrivi/css/ui.slider.css',
                false,
                PLUGIN_VERSION,
                false);
	wp_enqueue_style('jquery-custom-admin-ui-css',
                plugins_url() . '/melascrivi/css/custom.jquery.css',
                false,
                PLUGIN_VERSION,
                false);
	wp_enqueue_style('notify-custom-admin-ui-css',
                plugins_url() . '/melascrivi/css/notification.css',
                false,
                PLUGIN_VERSION,
                false);
	
	wp_enqueue_style('linedtextarea-ui-css',
                plugins_url() . '/melascrivi/css/jquery-linedtextarea.css',
                false,
                PLUGIN_VERSION,
                false);
				
	wp_enqueue_style('dataTables-ui-css',
                plugins_url() . '/melascrivi/js/datatables/css/jquery.dataTables.css',
                false,
                PLUGIN_VERSION,
                false);			

	wp_enqueue_style('dataTables-ui-css',
                plugins_url() . '/melascrivi/js/datatables/css/TableTools.css',
                false,
                PLUGIN_VERSION,
                false);	
				
	wp_enqueue_style('dataTables-vpad-css',
                plugins_url() . '/melascrivi/js/datatables/css/vpad.css',
                false,
                PLUGIN_VERSION,
                false);	
				
	wp_enqueue_style('style-ui-css',
                plugins_url() . '/melascrivi/css/style.css',
                false,
                PLUGIN_VERSION,
                false);
	
	wp_enqueue_style('jquery-ui-1.7.3.custom.css',
                plugins_url() . '/melascrivi/css/jquery-ui-1.7.3.custom.css',
                false,
                PLUGIN_VERSION,
                false);
	
}

//add_action( 'admin_enqueue_scripts', 'melascrivi_wp_js_load' );

add_action('plugins_loaded', 'melascriviPlugin_init');

add_action( 'admin_menu', 'melascrivi_admin_menu' );

function melascrivi_admin_menu(){
	
	add_menu_page('MelaScrivi', __('Melascrivi Configuration',"melascrivi"), 'manage_options', 'melascrivi_key_config', 'melascrivi_conf', plugins_url("melascrivi"."/images/mela16x16.png") , 7.7);
	
	$isset=get_option(WP_DB_NAME_MELASCRIVI_USERNAME);
	if (trim($isset)!=""){
	add_submenu_page('melascrivi_key_config', __('manage balance',"melascrivi"), __('manage balance',"melascrivi"), 'manage_options', 'melascrivi-balance', 'melascrivi_balance_manage');
	
	add_submenu_page('melascrivi_key_config', __('create articles',"melascrivi"), __('create articles',"melascrivi"), 'manage_options', 'melascrivi-create-articles', 'melascrivi_create_articles');
	
	add_submenu_page('melascrivi_key_config', __('show articles',"melascrivi"), __('show articles',"melascrivi"), 'manage_options', 'melascrivi-show-articles', 'melascrivi_show_articles');
	
	add_submenu_page('melascrivi_key_config', __('projects',"melascrivi"), __('projects',"melascrivi"), 'manage_options', 'melascrivi-project', 'melascrivi_project');
	
	add_submenu_page('melascrivi_key_config', __('guideline',"melascrivi"), __('guideline',"melascrivi"), 'manage_options', 'melascrivi-guideline', 'melascrivi_guideline');
	
	add_submenu_page('melascrivi_key_config', __('help center',"melascrivi"), __('help center',"melascrivi"), 'manage_options', 'melascrivi-tickets', 'melascrivi_tickets');
	
	add_submenu_page(null, __('review article',"melascrivi"), __('review article',"melascrivi"), 'manage_options', 'melascrivi-show-article', 'melascrivi_show_article');
	}	
}


/** funzione per visualizzare gli articoli richiesti dall'editore */
function melascrivi_show_articles(){
melascrivi_wp_js_load();
	?>
	<div id="melaInit" style="width:100%;">
	
	<?php
	if (isset($_REQUEST['start']))
		$start=intval($_REQUEST['start']);
	else
		$start=0;
		
	if (isset($_REQUEST['orderBy']))
		$orderBy=$_REQUEST['orderBy'];
	else
		$orderBy="lastStatusDate";
		
	if (isset($_REQUEST['orderDir']))
		$orderDir=$_REQUEST['orderDir'];
	else
		$orderDir="desc";
	
	
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	
	$order=new stdClass();
	$order->status=3;
	?>
	<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("pending orders","melascrivi");?></h2> 
		</header>
		<div class="table" id="waitingOrderDiv" style="">
		</div>
	</section>
	<br/><br/>
	<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("list of orders","melascrivi");?></h2> 
		</header>
			<input type='hidden' name='page' value='melascrivi-show-articles'/>
			<input type='hidden' id='start' name='start' value='0'/>
			<input type='hidden' id='orderBy' value='<?php echo $orderBy; ?>'/>
			<input type='hidden' id='orderDir' value='<?php echo $orderDir; ?>'/>
			<script>
			
			jQuery(document).ready(function(){
				updateWaitingOrder();
				updateOrders();
			});
			
			
			function searchMela(e){
				var charCode;
				if(e && e.which){
					charCode = e.which;
				}else if(window.event){
					e = window.event;
					charCode = e.keyCode;
				}
				if(charCode != 13) {
					return false;
				}
				updateOrders();
			}
			
			
			function changeMelaSorting(orderBy){
				if (jQuery("#orderBy").val()==orderBy){
					if (jQuery("#orderDir").val()=="desc"){
						jQuery("#orderDir").val("asc");
					}else{
						jQuery("#orderDir").val("desc");
					}
				}else{
					jQuery("#orderBy").val(orderBy);
				}
				updateOrders();
			} 
			
			
			
			function openOrder(id){
				window.location.assign(location.pathname+"?page=melascrivi-show-article&orderId="+id);
			}
			
			function avanti(){
				value=parseInt(jQuery("#start").val())+1;
				jQuery("#start").val(value);
				updateOrders();
				return false;
			}
			
			function indietro(){
				value=parseInt(jQuery("#start").val())-1;
				jQuery("#start").val(value);
				updateOrders();
				return false;
			}
				
			function updateOrders(){
				jQuery("#loading-container").show();
				value=parseInt(jQuery("#start").val());
				orderBy=jQuery("#orderBy").val();
				orderDir=jQuery("#orderDir").val();
				search=jQuery("#search").val();
				jQuery.post(ajaxurl, {action:"change_order_page", userId:<?php echo $userId;?>,value:value,orderBy:orderBy,orderDir:orderDir,search:search}, function(data){
					var msg = jQuery.trim(data);
					jQuery("#allOrderList").html(msg);
					jQuery("#loading-container").hide();
				});
			}
				
			function updateWaitingOrder(){
				jQuery.post(ajaxurl, {action:"update_waiting_order", userId:<?php echo $userId;?>}, function(data){
					var msg = jQuery.trim(data);
					jQuery("#waitingOrderDiv").html(msg);
				});
			}
			setInterval(updateWaitingOrder,15000);
				
			function removeOrder(elem){
				var answer = confirm('<?php _e("do you want to delate this order?","melascrivi"); ?>');
				if(answer){
					jQuery.post(ajaxurl, {action: "remove_order", orderId: jQuery(elem).attr('name')},function(data){
						var msg = jQuery.trim(data);
						if(msg == "errorOrderTaken"){
							viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi"); ?>","<?php _e("the order couldn't be removed in the actual status","melascrivi"); ?>",3000);
						}else if(msg == "genericError"){
							viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi"); ?>","<?php _e("generic error","melascrivi"); ?>",3000);
						}else if(msg == "orderRemoved"){
							// Rimuovo ordine in tabella lista ordini
							updateOrders();
						}
					});
				}
			}
				
			</script>
			<style>
				#loading-container 
				 {
					width: 100%;
					height: 100%;
					position: absolute;
					background-color: rgba(255,255,255,0.5);
					top: 0;
					left: 0;
				}
			</style>
			<div class="table">
			<div class="section" style="width: 100%; height: 100%; position: relative;">
				<?php echo $melascrivi->printWaitGif();?>
				<table style="width:100%">
					<tr>
						<td>
							<div style="float:right;padding:5px;" ><?php _e("search","melascrivi"); ?>:<input onkeypress="searchMela(event);" type="text" id="search"/> </div>
						</td>
					</tr>
					<tr>
						<td>
							<div id="allOrderList">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="left: 75%; position: relative; padding: 10px;">
				<button class="mela" id="mela_indietro"  onClick='indietro()' style="display:none" ><?php _e("back","melascrivi");?></button>
				<button class="mela" id="mela_avanti"  onClick='avanti()' ><?php _e("next","melascrivi");?></button>
			</div>
			</div>
	</section>
	</div>
	<?php
}

function loginMelascrivi($melascrivi=null){
	if ($melascrivi==null){
		$melascrivi=new MelascriviPlugin();
		$returnUserId=false;
	}else
		$returnUserId=true;
		
	$loginResponse= $melascrivi->login(get_option(WP_DB_NAME_MELASCRIVI_USERNAME),get_option(WP_DB_NAME_MELASCRIVI_PWD));
	if(strpos($loginResponse,"okEditor")===false){
	echo $loginResponse;
		die(__('Cheatin&#8217; uh?'));
	}
	$loginResponse=explode("||",$loginResponse);
	$userId=$loginResponse[1];
	update_option(WP_DB_NAME_MELASCRIVI_ID, $userId);
	if ($returnUserId)
		return $userId;
	else
		return $melascrivi;
}


function melascrivi_show_article(){   
melascrivi_wp_js_load();

?>
	<div id="melaInit" style="width:100%;">

<section class='portlet grid_6 leading' style='width: 98%'> 
	<header>
		<a href="admin.php?page=melascrivi-show-articles"><h2><?php _e("back to list","melascrivi");?></h2> </a>
	</header>
</section>

<?php

$melascrivi=loginMelascrivi();
echo $melascrivi->messageBox();

if (isset($_REQUEST['action'])){
	switch($_REQUEST['action']){
		case "acceptArticle":
			try{
			$result= $melascrivi->acceptOrder($_REQUEST['orderId'],$_REQUEST['styleVal'],$_REQUEST['guidelineVal'],$_REQUEST['grammarVal']);
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("article accepted","melascrivi").'",5000);
						
						});
				</script>';
			
			}catch (Exception $e){
				switch($e->getMessage()){
				case "wrongStatusError":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article in wrong status","melascrivi").'",5000);
						
						});
					</script>';
				break;
				case "genericError":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("generic error","melascrivi").'",5000);
						
						});
					</script>';
				break;
				case "articleCopied":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article is copied","melascrivi").'",5000);
						
						});
					</script>';
				break;
				case "errorNoOwner":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article is not your","melascrivi").'",5000);
						
						});
					</script>';
					
				break;
				}
				
			}
		break;
		
		case "correctArticle":
			try{
			$result= $melascrivi->correctOrder($_REQUEST['orderId'],$_REQUEST['note']);
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("article in correction","melascrivi").'",false);
						
						});
				</script>';
			
			}catch (Exception $e){
				switch($e->getMessage()){
				case "wrongStatusError":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article in wrong status","melascrivi").'",5000);
						
						});
					</script>';
				break;
				case "errorNoOwner":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article is not your","melascrivi").'",5000);
						
						});
					</script>';
					
				break;
				}
			}
		break;
	
		case "rejectArticle":
			try{
			$result= $melascrivi->rejectOrder($_REQUEST['orderId'],$_REQUEST['motivation'],$_REQUEST['blackListAuthor']);
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("article rejected","melascrivi").'",false);
						
						});
				</script>';
			
			}catch (Exception $e){
				switch($e->getMessage()){
				case "errorNoOwner":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article is not your","melascrivi").'",5000);
						
						});
					</script>';
					
				break;
				
				}
				
			}
			
		break;
		case "saveDraft":
			if ($melascrivi->saveArticleInDraft($_REQUEST['orderId'],get_option(WP_DB_NAME_MELASCRIVI_ID))){
				echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("article added as draft successfully","melascrivi").'",5000);
							setTimeout(function(){
							window.location.href="admin.php?page=melascrivi-show-articles";
							},3000);
						});
				</script>';
			}else{
				echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("article not added as draft","melascrivi").'",10000);
						
						});
					</script>';
			}
		break;
	}
}

	if (isset($_REQUEST['orderId'])){
		$orderId=intval($_REQUEST['orderId']);
		$url = plugins_url();		
		?>
	<div class="wrap">
		<script>
			function acceptOrder(orderId, send){
				jQuery("#revisionAcceptContent").show();
				jQuery("#revisionCorrectContent").hide();
				jQuery("#revisionRejectContent").hide();
				jQuery("#revisionStartContent").hide();
			}
			
			function correctOrder(orderId, send){
				jQuery("#revisionCorrectContent").show();
				jQuery("#revisionAcceptContent").hide();
				jQuery("#revisionRejectContent").hide();
				jQuery("#revisionStartContent").hide();
			}
			
			function rejectOrder(orderId, send){
				jQuery("#revisionRejectContent").show();
				jQuery("#revisionCorrectContent").hide();
				jQuery("#revisionAcceptContent").hide();
				jQuery("#revisionStartContent").hide();
			}

		
			jQuery("#revisionAccept, #revisionCorrect, #revisionReject").click(function(){
				scrollWin("#revisionBtns"); 
				jQuery("#revisionBtns button.selected").toggleClass("selected");
				jQuery(this).toggleClass("selected");
				jQuery("#revisionContainer").removeClass("initMargin");
				var elem = jQuery(this);
				jQuery("div.revisionContent:visible").fadeOut("fast",function(){
					jQuery('#'+jQuery(elem).attr("id")+'Content').fadeIn("fast");
				});
			});
	
			function updateOverallBar(){
				var newWidth = (parseFloat(jQuery("#styleVal").val()) - 0.34) * 22.3;
				newWidth += (parseFloat(jQuery("#guidelineVal").val()) - 0.34) * 22.3;
				newWidth += (parseFloat(jQuery("#grammarVal").val()) - 0.34) * 22.3;
				jQuery('#overallBarPercent').css("width",  Math.round(newWidth)+"%");
				var tot = Math.round(parseFloat(jQuery("#styleVal").val())+parseFloat(jQuery("#guidelineVal").val())+parseFloat(jQuery("#grammarVal").val()));
				jQuery('#evaluation').val(tot);
			}
			
			/******/
			jQuery(document).ready(function(){
				jQuery("#genericErrorMsgBox").notify();
				jQuery("#genericSuccessMsgBox").notify();
				jQuery("#revisionAcceptBtn, #revisionCorrectBtn, #revisionRejectBtn").click(function(){
						var form = null;
						var id = null;
						if(jQuery(this).attr("id") == "revisionAcceptBtn"){
							form = jQuery("#revisionAcceptForm").serialize();
							id = "revisionAcceptBtn";
						}else if(jQuery(this).attr("id") == "revisionCorrectBtn"){
							var r=confirm("<?php _e("proceding i accept that deadline will modified","melascrivi");?>")
							if(r == true) {
								var text = jQuery("textarea[name='note']").val();
								if(text == ""){
									jQuery("textarea[name='note']").css("border","1px solid red");
									return false;
								}
								if(jQuery.trim(text).split(" ").length < 10){
									jQuery("textarea[name='note']").css("border","1px solid red");
									viewMessageBox('genericErrorMsgBox','<?php _e("error","melascrivi"); ?>','<?php _e("please insert more detail for correction","melascrivi") ?>',5000);
									return false;
								}
								form = jQuery("#revisionCorrectForm").serialize();
								id = "revisionCorrectBtn";
							}else return false;
						}else if(jQuery(this).attr("id") == "revisionRejectBtn"){
							var text = jQuery("textarea[name='motivation']").val();
							if(text == ""){
								jQuery("textarea[name='motivation']").css("border","1px solid red");
								return false;
							}
							if(jQuery.trim(text).split(" ").length < 5){
								jQuery("textarea[name='motivation']").css("border","1px solid red");
								viewMessageBox('genericErrorMsgBox','<?php _e("error","melascrivi"); ?>','<?php _e("please insert more detail for rejection","melascrivi"); ?>',5000);
								return false;
							}
							form = jQuery("#revisionRejectForm").serialize();
							id = "revisionRejectBtn";
							
						}

					});
				});
		</script>
		
		<?php 
		
		$melascrivi=loginMelascrivi();
		echo $melascrivi->printOrder(get_option(WP_DB_NAME_MELASCRIVI_ID),$orderId);	
		$url = plugins_url();
	
		?>
		
		<style>
	<!--
		#wpfooter{display:none;}

		#revisionInfo {padding: 20px; font-weight: bold; line-height: 19px;}
		
		html #revisionBtns button, html #revisionBtns button:hover, html #revisionAcceptBtn, html #revisionAcceptBtn:hover,
		html #revisionCorrectBtn, html #revisionCorrectBtn:hover, html #revisionRejectBtn, html #revisionRejectBtn:hover {
			box-shadow: none; height: 43px; margin: 2% 3.2% 2.8% 2.5%; color: #3C3C3C; text-shadow: 0 1px 0 #FFFFFF;
			width: 29.5%;
		}
		
		#revisionBtns button:hover {font-size: 14px;}
		
		#topStatusInfo {
			background: none repeat scroll 0 0 white; border: 1px solid #000; color: black; float: right;
			font-size: 12px; margin-top: -15px; padding: 3px; cursor: pointer;
		}
		
		.revisionContent {float: left;width: 532px; padding: 1.9%; position: relative; width: 94.6%;}
		
		#revisionBtns button.selected, #revisionBtns button.selected:hover {
			border-bottom: medium none; font-size: 14px; height: 61px; margin-bottom: 0; margin-top: 10px;
			padding-bottom: 19px; padding-top: 0; position: relative;
		}

		.revisionContainer {
			margin: -0.3% 0 3% 3.8%; border: 1px solid #AAAAAA; float: left; font-size: 17px; line-height: 145px;
	    	text-align: center; width: 92.4%;
		}
		
		#revisionStartContent {border: 5px solid #F4F4F4; line-height: 219px; text-align: center; height: 219px;}
		
		#revisionAcceptContent {border: 5px solid #D9E4AC;}
		
		#revisionCorrectContent {border: 5px solid #F9EE9C;}
		
		#revisionRejectContent {border: 5px solid #F2CACB;}
		
		html #revisionAcceptBtn, html #revisionAcceptBtn:hover {
			margin-bottom:0px; width:66.5%; margin-top:0px;
		}

		.initMargin {margin-top: 0;}
		
		textarea {max-width: 503px; min-width: 97%; min-height: 125px; margin-top: 10px;}
	    
	    .slider {margin-left:50.5%; width:45.8%; margin-top:7px;}

	-->
	</style>
	<script>
		
		var jQ = jQuery.noConflict();
		
		jQ(document).ready(function(){
			jQ("#styleSlider").slider({
				value: 0.84,min: 0.34,max: 1.84,step: 0.5,
				slide: function(event, ui) {
					jQ("#styleVal").val(ui.value);
					updateOverallBar();
				}
			});
			jQ("#styleVal").val(jQ("#styleSlider").slider("value"));

			jQ("#guidelineSlider").slider({
				value: 0.84,min: 0.34,max: 1.84,step: 0.5,
				slide: function(event, ui) {
					jQ("#guidelineVal").val(ui.value);
					updateOverallBar();
				}
			});
			jQ("#guidelineVal").val(jQ("#guidelineSlider").slider("value"));

			jQ("#grammarSlider").slider({
				value: 0.84,min: 0.34,max: 1.84,step: 0.5,
				slide: function(event, ui) {
					jQ("#grammarVal").val(ui.value);
					updateOverallBar();
				}
			});
			jQ("#grammarVal").val(jQ("#grammarSlider").slider("value"));
		});
	</script>
	</div>
	<?php
	}else
		echo "no order id".var_export($_REQUEST);
	?>
	</div>
	<?php
}

function melascrivi_balance_manage(){
melascrivi_wp_js_load();
?>
	<div id="melaInit" style="width:100%;">
	
	<?php
	$melascrivi=new MelascriviPlugin();
	if(isset($_REQUEST['response'])){
		if(strpos($_REQUEST['response'],"success")!==false){
			echo '<script>
					jQuery(document).ready(function(){
						jQuery("#genericSuccessMsgBox").notify();
						viewMessageBox(\'genericSuccessMsgBox\',"","'. __("paypal recharge done successfully","melascrivi").'",5000);
					});
			</script>';
		}else{
			echo '<script>
					jQuery(document).ready(function(){
						jQuery("#genericErrorMsgBox").notify();
						viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("paypal error retry later","melascrivi").'",10000);
					});
				</script>';
		}
	}
	echo $melascrivi->messageBox();
	
	?>

	<?php $isset=get_option(WP_DB_NAME_MELASCRIVI_ACTKEY);?>
	<style>
	
	</style>
	<?php if (trim($isset)!=""){?>
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header  >
			
			<?php $userId=loginMelascrivi($melascrivi);
			try{
				echo $melascrivi->printBalance($userId);
			}catch(exception $e){}	
			?>
			
		</header>
	</section>
	<?php } ?>
	
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header  >
			<h2 ><?php _e("budget management","melascrivi");?></h2>
		</header>
		<div id='paypal' class="table" style="  padding: 10px;">
		<?php echo $melascrivi->printPaypalRecharge(get_option(WP_DB_NAME_MELASCRIVI_ID));?>
		</div>
	</section>
	</div>
	<?php
}


function melascrivi_conf(){

	melascrivi_wp_js_load();
	?>

	<div id="melaInit" style="width:100%;">
	
	<?php

	if ( isset($_POST['website']) ){
		update_option(WP_DB_NAME_MELASCRIVI_WEBSITE, $_POST['website']);
	}
	$website=get_option(WP_DB_NAME_MELASCRIVI_WEBSITE);
	if(trim($website)==""){
		?>
		<style>
			body {
				font-family:Arial, Helvetica, sans-serif
			}
			span.customSelect {
				font-size:12px;
				/*background-color: #f5f0de;*/
				color:#7c7c7c;
				padding:5px 7px;
				border:1px solid #aaa;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				border-radius: 5px 5px;
			}
			span.customSelect.changed {
				background-color: #f0dea4;
			}
			.customSelectInner {
				background:url('<?php echo plugins_url();  ?>/melascrivi/images/arrow-down.png') no-repeat center right;
			}
		</style>
		<script>
			jQuery(document).ready(function (){
				jQuery("#melaWebsite").customSelect();			
			});
		</script>
	
		<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header id="melaShowRegister" onclick="showRegister();" class="closed" style="cursor:pointer; ">
			<h2><?php _e("choose the website","melascrivi");?></h2>
		</header>
		<form method="post"> 
		<div  class="table" style="padding: 10px;">
			<input type="hidden" name="page" value="melascrivi_key_config"/>
			<select id="melaWebsite" name="website" style="width:150px;">
				<option value="it">Melascrivi</option>
				<option value="uk">Hotype</option>
				<option value="dev">Dev</option>
			</select>
			<button class="mela"  type='submit' onclick="submit();" ><?php _e("save","melascrivi"); ?> </button>
		</div>
		
		</form>
		</section>
		<?php
		
		
	}else{
		
	$melascrivi=new MelascriviPlugin();
	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
		try{
			$melascrivi->login($_POST['user'],$_POST['pwd']);
			update_option(WP_DB_NAME_MELASCRIVI_USERNAME, $_POST['user']);
			update_option(WP_DB_NAME_MELASCRIVI_PWD, $_POST['pwd']);
			update_option(WP_DB_NAME_MELASCRIVI_ACTKEY, "ok");
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("user ok","melascrivi").'",5000);	
						});
				</script>';
		}catch (Exception $e){
				switch($e->getMessage()){
				case "confirmRegistration":
				case "notActiveUser":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("confirm user","melascrivi").'",10000);
						});
					</script>';
					delete_option(WP_DB_NAME_MELASCRIVI_ACTKEY); 
				break;
				case "blockedUser":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("blocked user","melascrivi").'",10000);
						});
					</script>';
				break;
				case "userNotFound":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("user not found","melascrivi").'",10000);
						});
					</script>';
				break;
				}
			}
	}else if(isset($_POST['new_user'])){
		$confirmAddress=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

		try{
			$melascrivi->createNewUser($_POST['user'],$_POST['pwd'],$_POST['repwd'],$confirmAddress);
			update_option(WP_DB_NAME_MELASCRIVI_USERNAME, $_POST['user']);
			update_option(WP_DB_NAME_MELASCRIVI_PWD, $_POST['pwd']);
			delete_option(WP_DB_NAME_MELASCRIVI_ACTKEY); 
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("user created","melascrivi").'",5000);
						
						});
				</script>';
			
			}catch (Exception $e){
				switch($e->getMessage()){
				case "wrongLenght":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("wrong password lenght","melascrivi").'",10000);
						});
					</script>';
				break;
				case "wrongConfirmPassword":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("wrong confirm password","melascrivi").'",10000);
						});
					</script>';
				break;
				case "genericError":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("generic error","melascrivi").'",10000);
						});
					</script>';
				break;
				case "isRegistered":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("user already registered","melascrivi").'",10000);
						});
					</script>';
				break;
				}
			}
	}else if (isset($_REQUEST['actKey'])){
		try{
			$melascrivi->activateUSer(trim($_REQUEST['actKey']));
			update_option("WP_DB_NAME_MELASCRIVI_ACTKEY", $_REQUEST['actKey']);
			echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericSuccessMsgBox").notify();
							viewMessageBox(\'genericSuccessMsgBox\',"","'. __("user activated","melascrivi").'",5000);
						});
				</script>';
			
			}catch (Exception $e){
				switch($e->getMessage()){
				case "alreadyActivatedUser":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("user already activated","melascrivi").'",10000);
						});
					</script>';
				break;
				case "badUser":
					echo '<script>
						jQuery(document).ready(function(){
							jQuery("#genericErrorMsgBox").notify();
							viewMessageBox(\'genericErrorMsgBox\',"'. __("error","melascrivi").'","'. __("user not registered","melascrivi").'",10000);
						});
					</script>';
				break;
				}
			}	
	}
	$isset=get_option(WP_DB_NAME_MELASCRIVI_USERNAME);
	echo $melascrivi->messageBox();
	
	?>
	
	<script>
		function showRegister(){
			jQuery("#alreadyRegister").show();
			jQuery("#notRegister").hide();
			jQuery("#melaShowRegister").removeClass('opened');
			jQuery("#melaShowNew").removeClass('closed');
			jQuery("#melaShowRegister").addClass('closed');
			jQuery("#melaShowNew").addClass('opened');
		}
		
		function showNew(){
			jQuery("#alreadyRegister").hide();
			jQuery("#notRegister").show();
			jQuery("#melaShowRegister").removeClass('closed');
			jQuery("#melaShowNew").removeClass('opened');
			jQuery("#melaShowRegister").addClass('opened');
			jQuery("#melaShowNew").addClass('closed');
		}
		<?php if(trim($isset)==""){?>
		jQuery(document).ready(function(){
			showNew();
		
		});
		
		
		<?php } ?>
		
	</script>
	<style>
	.closed{
		background-image:url('<?php echo plugins_url() ?>/melascrivi/css/images/navicons-small/64.png'); 
		background-repeat:no-repeat; 
		background-position:99%;
	}
	.opened{
		background-image:url('<?php echo plugins_url() ?>/melascrivi/css/images/navicons-small/65.png'); 
		background-repeat:no-repeat; 
		background-position:99%;
	}
	
	</style>
	<form method="post">
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header id="melaShowRegister" onclick="showRegister();" class="closed" style="cursor:pointer; ">
			<h2><?php _e("already registered","melascrivi");?></h2>
		</header>
		<div id='alreadyRegister' class="table" style="padding: 10px;">
			<table style="width:100%;">
				<tr>
					<th style="width:15%;"><?php _e("email","melascrivi"); ?></th>
					<td><input name='user' value='<?php echo get_option(WP_DB_NAME_MELASCRIVI_USERNAME);?>'/></td>
				</tr>
				<tr>
					<th style="width:15%;"><?php _e("passwd","melascrivi"); ?></th>
					<td><input name='pwd' type="password" value='<?php echo get_option(WP_DB_NAME_MELASCRIVI_PWD);?>'/></td>
				</tr>
				<tr>
					<th colspan='2'>
						<button  class="mela" type='submit' onclick="submit();" ><?php _e("save","melascrivi"); ?> </button>
						<input type="hidden" name='submit' value="true"/>
					</th>
				</tr>
			</table>
		</div>
	</section>
	</form>
	<form method="post">
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header id="melaShowNew" onclick="showNew();" class="opened" style=" cursor:pointer; " >
			<h2><?php _e("new user","melascrivi");?></h2>
		</header>
		<div id='notRegister' class="table" style="display:none; padding: 10px;">
			<table style="width:100%;">
				<tr>
					<th style="width:15%;"><?php _e("email","melascrivi"); ?></th>
					<td><input name='user' value=''/></td>
				</tr>
				<tr>
					<th style="width:15%;"><?php _e("passwd","melascrivi"); ?></th>
					<td><input type="password" name='pwd' value=''/></td>
				</tr>
				<tr>
					<th style="width:15%;"><?php _e("re passwd","melascrivi"); ?></th>
					<td><input type="password" name='repwd' value=''/></td>
				</tr>
				<tr>
					<th colspan='2'>
						<button  class="mela" onclick="submit();" type='submit'><?php _e("save","melascrivi"); ?> </button>
						<input type="hidden" name='new_user' value="true" />
					</th>
				</tr>
			</table>
		</div>
	</section>
	<input type="hidden" name="page" value="melascrivi_key_config"/>
	</form>
	
	<?php $isset=get_option(WP_DB_NAME_MELASCRIVI_ACTKEY);
	if (trim($isset)==""){?>
	<form method="post">
	
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header >
			<h2><?php _e("activate user","melascrivi");?></h2>
		</header>
		<div id='confirm' class="table" style="  padding: 10px;">
			<table style="width:100%;">
				<tr>
					<th style="width:15%;"><?php _e("activation key","melascrivi"); ?></th>
					<td><input size="100" name='actKey' value='<?php echo $_REQUEST['actKey'];?>'/></td>
				</tr>
				<tr>
					<th colspan='2'>
						<button class="mela" onclick="submit();" type='submit'><?php _e("send","melascrivi"); ?> </button>
					</th>
				</tr>
			</table>
		</div>
	</section>
	<input type="hidden" name="page" value="melascrivi_key_config"/>
	</form>
	<?php }?>
	
	</div>
	<?php
	}
}

function melascrivi_project(){
melascrivi_wp_js_load();
?>
	<div id="melaInit" style="width:100%;">
	
	<?php
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	?>
	<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("add new project","melascrivi");?></h2>
		</header>
		<div class="table">
			<?php echo $melascrivi->printCreateProject(); ?>
		</div>
	</section>
	<br/>
	<input type='hidden' name='page' value='melascrivi-project'/>
			<input type='hidden' id='start' name='start' value='0'/>
			<input type='hidden' id='orderBy' value='id'/>
			<input type='hidden' id='orderDir' value='desc'/>
	<script>
		function createMelaProject(){
		projectName=jQuery("#melaProject").val();
			jQuery("#loading-container").show();
			jQuery.post(ajaxurl, {action:"create_project", userId:<?php echo $userId;?>,projectName:projectName}, function(data){
				var msg = jQuery.trim(data);
				if(msg.indexOf("projectCreated") !== -1){
					viewMessageBox('genericSuccessMsgBox',"","<?php _e("project created successfully","melascrivi");?>",5000);
					jQuery("#orderBy").val("id");
					jQuery("#orderDir").val("desc");
					jQuery("#start").val("-1");
					avanti();
				}else{
					if (msg.indexOf("genericError") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("error during creation, retry later","melascrivi");?>",5000);
					}else if (msg.indexOf("errorEmptyName") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("please insert a name for the project","melascrivi");?>",5000);
					}else if (msg.indexOf("notAllowed") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("not allowed","melascrivi");?>",5000);
					}
					jQuery("#loading-container").hide();
				}
			});
		}
		
		
		function changeMelaSorting(orderBy){
			if (jQuery("#orderBy").val()==orderBy){
				if (jQuery("#orderDir").val()=="desc"){
					jQuery("#orderDir").val("asc");
				}else{
					jQuery("#orderDir").val("desc");
				}
			}else{
				jQuery("#orderBy").val(orderBy);
			}
			updateProjectTable();
			
		} 
		
		function searchMela(e){
			var charCode;
    
			if(e && e.which){
				charCode = e.which;
			}else if(window.event){
				e = window.event;
				charCode = e.keyCode;
			}

			if(charCode != 13) {
				return false;
			}
			updateProjectTable();
			
		}
		
		function avanti(){
			
			value=parseInt(jQuery("#start").val())+1;
			orderBy=jQuery("#orderBy").val();
			orderDir=jQuery("#orderDir").val();
			jQuery("#start").val(value);
			updateProjectTable();
			return false;
			
		}
		function indietro(){
			value=parseInt(jQuery("#start").val())-1;
			orderBy=jQuery("#orderBy").val();
			orderDir=jQuery("#orderDir").val();
			jQuery("#start").val(value);
			updateProjectTable();
			return false;
		}
		
		function updateProjectTable(){
			orderBy=jQuery("#orderBy").val();
			jQuery("#loading-container").show();
			search=jQuery("#search").val();
			value=parseInt(jQuery("#start").val());
			orderDir=jQuery("#orderDir").val();
			jQuery.post(ajaxurl, {action:"change_project_page", userId:<?php echo $userId;?>,value:value,orderBy:orderBy,orderDir:orderDir,search:search}, function(data){
				var msg = jQuery.trim(data);
				jQuery("#projectsList").html(msg);
				jQuery("#loading-container").hide();
			});
		}
		
		jQuery(document).ready(function(){
			updateProjectTable();
		});
	</script>
	<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("projects list","melascrivi");?></h2>
		</header>
		<style>
			#loading-container 
			 {
				width: 100%;
				height: 100%;
				position: absolute;
				background-color: rgba(255,255,255,0.5);
				top: 0;
				left: 0;
			}
		</style>
		<div class="table">
		<div style="width: 100%; height: 100%; position: relative;">
				<?php echo $melascrivi->printWaitGif();?>
				<table style="width:100%;">
					<tr>
						<td>
						<div style="float:right;padding:5px;" ><?php _e("search","melascrivi"); ?>:<input onkeypress="searchMela(event);" type="text" id="search"/> </div>
						</td>
					</tr>
					<tr>
						<td>
							<div id="projectsList">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="left: 75%; position: relative; padding: 10px;">
				<button class="mela" id="mela_indietro"  onClick='indietro()' style="display:none" ><?php _e("back","melascrivi");?></button>
				<button class="mela" id="mela_avanti" onClick='avanti()' ><?php _e("next","melascrivi");?></button>
			</div>
		</div>
	</section>
	<?php
	echo $melascrivi->messageBox();
	?>
	</div>
	<?php
}


function melascrivi_guideline(){
melascrivi_wp_js_load();
	?>
	<div id="melaInit" style="width:100%;">
	<?php
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	?>
	<script>
		jQuery(document).ready(function (){
			jQuery("#category").customSelect();	
			jQuery("#style").customSelect();		
		});
	
	</script>
	
	<style type="text/css">
		body {
			font-family:Arial, Helvetica, sans-serif
		}
		span.customSelect {
			font-size:12px;
			/*background-color: #f5f0de;*/
			color:#7c7c7c;
			padding:5px 7px;
			border:1px solid #aaa;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			border-radius: 5px 5px;
		}
		span.customSelect.changed {
			background-color: #f0dea4;
		}
		.customSelectInner {
			background:url('<?php echo plugins_url();  ?>/melascrivi/images/arrow-down.png') no-repeat center right;
		}
	</style>
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		
			<header onclick="showCreate();" style='cursor:pointer;'>
				<h2><?php _e("add new guideline","melascrivi");?></h2>
			</header>
		<div class="table" id='createGuideline'  >	
			<?php echo $melascrivi->printCreateGuideline(); ?>
		</div>
	</section>
	
	<section id='editGuideline' class='portlet grid_6 leading' style='width: 98%; display:none;'> 
		<header>
			<h2><?php _e("edit guideline","melascrivi");?></h2>
		</header>
		<style>
			#loading-containerGuideline 
			 {
				width: 100%;
				height: 100%;
				position: absolute;
				background-color: rgba(255,255,255,0.5);
				top: 0;
				left: 0;
			}
		</style>
		<?php echo $melascrivi->printWaitGif("Guideline"); ?>
		<div  class="table" id="editMela">
		<?php echo $melascrivi->printCreateGuideline(); ?>
		</div>
		
	</section>
	<br/>
	<input type='hidden' name='page' value='melascrivi-guidelines'/>
			<input type='hidden' id='start' name='start' value='0'/>
			<input type='hidden' id='orderBy' value='id'/>
			<input type='hidden' id='orderDir' value='desc'/>
	<script>
	
		function showCreate(){
			jQuery("#editGuideline").hide();
			jQuery("#createGuideline").show();
		}
	
		function clickModifyGuideline(id){
			jQuery("#editGuideline").show();
			jQuery("#createGuideline").hide();
			jQuery("#loading-containerGuideline").show();
			jQuery.post(ajaxurl, {action:"edit_guideline", userId:<?php echo $userId;?>,guideline:id}, function(data){
				var msg = jQuery.trim(data);
				jQuery("#editMela").html(msg);
				jQuery("#categoryEdit").customSelect();	
				jQuery("#styleEdit").customSelect();	
				jQuery("#loading-containerGuideline").hide();
			});
		}
	
		function saveGuideline(id){
		guidelineName=jQuery("#melaGuideline").val();
			jQuery("#loading-container").show();
			if(id>0){
				categoryId=jQuery("#categoryEdit").val();
				styleId=jQuery("#styleEdit").val();
				minWords=jQuery("#minWordsEdit").val();
				maxWords=jQuery("#maxWordsEdit").val();
				description=jQuery("#descriptionEdit").val();
			}else{
				categoryId=jQuery("#category").val();
				styleId=jQuery("#style").val();
				minWords=jQuery("#minWords").val();
				maxWords=jQuery("#maxWords").val();
				description=jQuery("#description").val();
			}
			
			jQuery.post(ajaxurl, {action:"save_guideline", guidelineId:id,category:categoryId,style:styleId,minWords:minWords,maxWords:maxWords,description:description}, function(data){
				var msg = jQuery.trim(data);
				
				if(msg.indexOf("guidelineCreated") !== -1){
					viewMessageBox('genericSuccessMsgBox',"","<?php _e("guideline created successfully","melascrivi");?>",5000);
					jQuery("#orderBy").val("id");
					jQuery("#orderDir").val("desc");
					jQuery("#start").val("-1");
					avanti();
				}else if(msg.indexOf("guidelineModified") !== -1){
					viewMessageBox('genericSuccessMsgBox',"","<?php _e("guideline modifyed successfully","melascrivi");?>",5000);
					jQuery("#orderBy").val("id");
					jQuery("#orderDir").val("desc");
					jQuery("#start").val("-1");
					avanti();
				}else{
					if (msg.indexOf("genericError") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("error during creation, retry later","melascrivi");?>",5000);
					}else if (msg.indexOf("errorEmptyValues") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("please insert category, style, min words, max words and description","melascrivi");?>",5000);
					}else if (msg.indexOf("notAllowed") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("not allowed","melascrivi");?>",5000);
					}else if (msg.indexOf("errorMinMaxWords") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("max words must be higher than min words","melascrivi");?>",5000);
					}else if (msg.indexOf("errorFewWords") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("min words must be at least 30 words","melascrivi");?>",5000);
					}
					
					jQuery("#loading-container").hide();
				}
			});
		}
		
		function changeMelaSorting(orderBy){
			if (jQuery("#orderBy").val()==orderBy){
				if (jQuery("#orderDir").val()=="desc"){
					jQuery("#orderDir").val("asc");
				}else{
					jQuery("#orderDir").val("desc");
				}
			}else{
				jQuery("#orderBy").val(orderBy);
			}
		
			
			value=parseInt(jQuery("#start").val());
			orderDir=jQuery("#orderDir").val();
			updateGuidelineTable();
		} 
		
		
		
		function avanti(){
			value=parseInt(jQuery("#start").val())+1;
			orderBy=jQuery("#orderBy").val();
			orderDir=jQuery("#orderDir").val();
			jQuery("#start").val(value);
			updateGuidelineTable();
			return false;
			
		}
		function indietro(){
			value=parseInt(jQuery("#start").val())-1;
			orderBy=jQuery("#orderBy").val();
			orderDir=jQuery("#orderDir").val();
			jQuery("#start").val(value);
			updateGuidelineTable();

			return false;
		}
		
		function updateGuidelineTable(){
			jQuery("#loading-container").show();
			value=jQuery("#start").val();
			orderBy=jQuery("#orderBy").val();
			orderDir=jQuery("#orderDir").val();
			jQuery.post(ajaxurl, {action:"change_guideline_page", userId:<?php echo $userId;?>,value:value,orderBy:orderBy,orderDir:orderDir}, function(data){
				var msg = jQuery.trim(data);
				jQuery("#guidelinesList").html(msg);
				jQuery("#loading-container").hide();
				if (jQuery("#start").val()==0)
					jQuery("#mela_indietro").hide();
			});
		}
		jQuery(document).ready(function(){
			updateGuidelineTable();
		});
	</script>
	<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("guideline list","melascrivi");?></h2>
		</header>
		<style>
			#loading-container {
				width: 100%;
				height: 100%;
				position: absolute;
				background-color: rgba(255,255,255,0.5);
				top: 0;
				left: 0;
			}
		</style>
		<div  class="table">
		<div style="width: 100%; height: 100%; position: relative;">
				<?php echo $melascrivi->printWaitGif();?>
				<div id="guidelinesList">
				</div>
			</div>
			<div style="left: 75%; position: relative; padding: 10px;">
				<button class="mela" id="mela_indietro"  onClick='indietro()' style="display:none" ><?php _e("back","melascrivi");?></button>
				<button class="mela" id="mela_avanti" onClick='avanti()' ><?php _e("next","melascrivi");?></button>
			</div>
		</div>
	</section>
	<?php
	echo $melascrivi->messageBox();
	?>
	</div>
	<?php
}

function melascrivi_create_articles(){
melascrivi_wp_js_load();
	?>
	<div id="melaInit" style="width:100%;">
	

	

	<script>
		jQuery(document).ready(function (){
			jQuery(".melaSelect").customSelect();
			jQuery(".lined").linedtextarea();					
		});
	</script>
	
	<style type="text/css">
		body {
			font-family:Arial, Helvetica, sans-serif
		}

		#loading-container 
		 {
			width: 100%;
			height: 100%;
			position: absolute;
			background-color: rgba(255,255,255,0.5);
			top: 0;
			left: 0;
		}
	</style>

	<script>
		function stripslashes (str) {
		  return (str + '').replace(/\\(.?)/g, function (s, n1) {
			switch (n1) {
			case '\\':
			  return '\\';
			case '0':
			  return '\u0000';
			case '':
			  return '';
			default:
			  return n1;
			}
		  });
		}
	
		function sendActionRequest(type){
		
		if(type == "createOrders"){
			jQuery("#loading-container").show();
			if(jQuery("#articlesTable").dataTable().fnGetNodes().length < 1){
				viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("error empty orders table","melascrivi");?>",5000);
				return false;
			}
			var values = {action: "create_orders"};
			for(var i=0; i < jQuery("#articlesTable").dataTable().fnGetNodes().length; i++) {
				var row = jQuery("#articlesTable").dataTable().fnGetData(i);
				values["article"+i] = {projectId: row[0], guidelineId: row[1], qualityId: row[2], description: row[3], deadline: row[4], circle: row[5], features: row[6],title: jQuery(row[7]).html(),special:row[9],programming:row[10]};
			}
			jQuery.post(ajaxurl, jQuery.param(values), function(data){
				var msg = jQuery.trim(data);
				if((msg.indexOf("orderCreated") > -1) || (msg.indexOf("orderCreatedProfile") > -1)){
					viewMessageBox("genericSuccessMsgBox","","<?php _e("orders created successfully","melascrivi");?>",5000);
					emptyTableData();
					updateOrdersInfo();
				}else{
					if (msg.indexOf("genericError") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("error during creation, retry later","melascrivi");?>",5000);
					}else if (msg.indexOf("errorEmptyName") !== -1){
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("please insert a name for the project","melascrivi");?>",5000);
					}else if (msg.indexOf("notAllowed") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("error during creation, retry later","melascrivi");?>",5000);
					}
					else if (msg.indexOf("errorEmptyOrderTable") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("error empty orders table","melascrivi");?>",5000);
					}else if (msg.indexOf("errorEmptyValues") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("some orders couldn't be created, try again later","melascrivi");?>",5000);
					}else if (msg.indexOf("errorDeadline") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("the deadline should be at leat 2 hours from current time","melascrivi");?>",5000);
					}else if (msg.indexOf("errorNotEnoughMoney") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("not enough money","melascrivi");?>",5000);
					}
					else if (msg.indexOf("errorNotCompletedProfile") !== -1){
						viewMessageBox("genericErrorMsgBox","<?php _e("error","melascrivi");?>","<?php _e("profile not complete","melascrivi");?>",5000);
					}
					
				}
				jQuery("#loading-container").hide();
			});
		}
	}
	</script>
	
	<?php
	$melascrivi=new MelascriviPlugin();
	?>
	<section  class='portlet grid_6 leading' style='width: 98%'> 
		<header  >
			
			<?php $userId=loginMelascrivi($melascrivi);
			try{
				echo $melascrivi->printBalance($userId);
			}catch(exception $e){}	
			?>
			
		</header>
	</section>
	
	<?php
	$userId=loginMelascrivi($melascrivi);
	
	echo $melascrivi->printCreateOrder($userId);
	?>
	</div>
	<?php
}

function melascrivi_tickets(){
melascrivi_wp_js_load();
	?>
	<div id="melaInit" style="width:100%;">
	
	<?php
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	echo $melascrivi->messageBox();
?>
<section class='portlet grid_6 leading' style='width: 98%'> 
		<header>
			<h2><?php _e("help center","melascrivi");?></h2>
		</header>
		<div class="infoBox messageBox" style="margin-left:10%;margin-bottom:30px;width:77%;">
		<?php _e("our team will answer soon as possible","melascrivi");?>
		</div>
		<style>
			#uniform-ticketType {
				margin-bottom: 20px;
				width: 430px;
				float: left;
			}
			#faqMsg {
			  clear: both;
			  height: 300px;
			  margin-left: auto;
			  margin-right: auto;
			  padding-top: 30px;
			  width: 695px;
			}
		</style>
		<script>
			function sendMelaTicket(){
				jQuery("#loading-container").show();
				title=jQuery("#titolo").val();
				text=jQuery("#testo").val();
				
				jQuery.post(ajaxurl, {action:"send_ticket", userId:<?php echo $userId;?>,title:title,text:text}, function(data){
					var msg = jQuery.trim(data);
					if(msg.indexOf("okSent") !== -1){
						viewMessageBox('genericSuccessMsgBox',"","<?php _e("ticket sent successfully","melascrivi");?>",5000);
					}else{
						viewMessageBox('genericErrorMsgBox',"<?php _e("error","melascrivi");?>","<?php _e("not allowed","melascrivi");?>",5000);
					}
					jQuery("#loading-container").hide();
				});
				 
			}
		</script>
		<style>
			#loading-container {
				width: 100%;
				height: 100%;
				position: absolute;
				background-color: rgba(255,255,255,0.5);
				top: 115px;
				left: 0;
				z-index: 100;
			}
		</style>
	<?php echo $melascrivi->printWaitGif();?>
<?php
	echo $melascrivi->printTicket($userId);
?>
		
		</section>
	</div>
<?php
}

include_once ("admin_init.php");



?>