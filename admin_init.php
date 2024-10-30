<?php
require_once( 'melascrivi_php_client/MelascriviPlugin.php' );

add_action('wp_ajax_update_waiting_order', 'updateWaitingOrder');
function  updateWaitingOrder(){
	$userId=$_REQUEST['userId'];
	$order=new stdClass();
	$order->status=3;
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	try{
		die($melascrivi->printOrders($userId,$order,null,null,0,15,true));
	}catch (Exception $e){
		$melascrivi=new MelascriviPlugin();
		$userId=loginMelascrivi($melascrivi);
		die($melascrivi->printOrders($userId,$order,null,null,0,15,true));
	}
}

add_action('wp_ajax_change_order_page', 'changeOrderPage');
function  changeOrderPage(){
	$userId=$_REQUEST['userId'];
	if (isset($_REQUEST['value']))
		$start=intval($_REQUEST['value']);
	else
		$start=0;
		
	if (isset($_REQUEST['orderBy']))
		$orderBy=$_REQUEST['orderBy'];
	else
		$orderBy="lastStatusDate";
		
	if (isset($_REQUEST['orderDir']))
		$orderDir=$_REQUEST['orderDir'];
	else
		$orderDir="DESC";
		
	if (isset($_REQUEST['search'])){
		$order=new stdClass();
		$order->name=$_REQUEST['search'];
	}else
		$order=null;
		
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	try{
		die($melascrivi->printOrders($userId,$order,$orderBy,$orderDir,$start*15,15,false,true));
	}catch (Exception $e){
		$melascrivi=new MelascriviPlugin();
		$userId=loginMelascrivi($melascrivi);
		die($melascrivi->printOrders($userId,$order,$orderBy,$orderDir,$start*15,15,false,true));
	}
}

add_action('wp_ajax_change_project_page', 'changeProjectPage');
function changeProjectPage(){
	$userId=$_REQUEST['userId'];
	if (isset($_REQUEST['value']))
		$start=intval($_REQUEST['value']);
	else
		$start=0;
		
	if (isset($_REQUEST['orderBy']))
		$orderBy=$_REQUEST['orderBy'];
	else
		$orderBy="id";
		
	if (isset($_REQUEST['orderDir']))
		$orderDir=$_REQUEST['orderDir'];
	else
		$orderDir="DESC";
		
	if (isset($_REQUEST['search'])){
		$project=new stdClass();
		$project->name=$_REQUEST['search'];
	}else
		$project=null;	
		
	$melascrivi=new MelascriviPlugin();
	die($melascrivi->printProjects($userId,$project,$orderBy,$orderDir,$start*15,15,true));
}

add_action('wp_ajax_create_project', 'createProject');
function  createProject(){
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	$projectName=$_REQUEST['projectName'];
	try{
		die($melascrivi->createProject($userId,$projectName));	
	}catch (Exception $e){
		die($e->getMessage());
	}
}

add_action('wp_ajax_change_guideline_page', 'changeGuidelinePage');
function changeGuidelinePage(){
	$userId=$_REQUEST['userId'];
	if (isset($_REQUEST['value']))
		$start=intval($_REQUEST['value']);
	else
		$start=0;
		
	if (isset($_REQUEST['orderBy']))
		$orderBy=$_REQUEST['orderBy'];
	else
		$orderBy="id";
		
	if (isset($_REQUEST['orderDir']))
		$orderDir=$_REQUEST['orderDir'];
	else
		$orderDir="DESC";
		
	$melascrivi=new MelascriviPlugin();
	die($melascrivi->printGuidelines($userId,null,$orderBy,$orderDir,$start*10,10,true));	
}

add_action('wp_ajax_edit_guideline', 'editGuideline');
function editGuideline(){
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	$guidelines=new stdClass();
	$guidelines->id=$_REQUEST['guideline'];
	$userId=$_REQUEST['userId'];
	
	$request=$melascrivi->getGuidelines($userId,$guidelines);
	die($melascrivi->printCreateGuideline($request->guidelines));
}

add_action('wp_ajax_save_guideline', 'saveGuideline');
function saveGuideline(){
	$melascrivi=new MelascriviPlugin();
	$guidelineId=$_REQUEST['guidelineId'];
	$categoryId=$_REQUEST['category'];
	$styleId=$_REQUEST['style'];
	$minWords=$_REQUEST['minWords'];
	$maxWords=$_REQUEST['maxWords'];
	$description=$_REQUEST['description'];
	$userId=loginMelascrivi($melascrivi);
	
	if($guidelineId>0){
		die($melascrivi->modifyGuideline($userId,$guidelineId,$categoryId,$styleId,$minWords,$maxWords,$description));
	}else{
		die($melascrivi->createGuideline($userId,$categoryId,$styleId,$minWords,$maxWords,$description));
	}
}
	
add_action('wp_ajax_create_orders', 'createOrders');
function createOrders(){
	$orderArray=array();
	for($i = 0; $i < count($_REQUEST)-1; $i++){
	
		$orderArray[$i]["projectId"]=$_REQUEST["article$i"]["projectId"];
		$orderArray[$i]["guidelineId"]=$_REQUEST["article$i"]["guidelineId"];
		$orderArray[$i]["qualityId"]=$_REQUEST["article$i"]["qualityId"];
		$orderArray[$i]["description"]=$_REQUEST["article$i"]["description"];
		$orderArray[$i]["deadline"]=$_REQUEST["article$i"]["deadline"];
		$orderArray[$i]["circle"]=$_REQUEST["article$i"]["circle"];
		$orderArray[$i]["features"]=$_REQUEST["article$i"]["features"];
		$orderArray[$i]["title"]=$_REQUEST["article$i"]["title"];
		$orderArray[$i]["special"]=$_REQUEST["article$i"]["special"];
		$orderArray[$i]["programmed"]=$_REQUEST["article$i"]["programming"];
	}
	if (count(orderArray)>0){
		$melascrivi=new MelascriviPlugin();
		$userId=loginMelascrivi($melascrivi);
		die($melascrivi->requestOrder($userId,$orderArray));
	}
}

add_action('wp_ajax_send_ticket', 'sendTicket');
function sendTicket(){
	$userId=$_REQUEST['userId'];
	$title=$_REQUEST['title'];
	$text=$_REQUEST['text'];
	$melascrivi=new MelascriviPlugin();
	loginMelascrivi($melascrivi);
	die($melascrivi->sendTicket($userId,$title,$text));	
}

add_action('wp_ajax_remove_order', 'removeOrder');
function removeOrder(){
	$melascrivi=new MelascriviPlugin();
	$userId=loginMelascrivi($melascrivi);
	$orderId=$_REQUEST['orderId'];
	die($melascrivi->deleteOrder($userId,$orderId));
}
	
add_action('wp_ajax_get_balance', 'getBalance');	
function getBalance(){
	$melascrivi=new MelascriviPlugin();
	try{
		$userId=loginMelascrivi($melascrivi);
		$echo=$melascrivi->getBalance($userId);
	}catch(Exception $e){
		$echo=__("updating","melascrivi");
	}
	die($echo);
}














?>