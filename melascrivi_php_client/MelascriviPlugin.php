<?php
class MelascriviPlugin{

	const PLUGIN_NAME = "melascrivi";
	const PLUGIN_VERSION= "1.5.0";
	const  API_PATH_dev="http://dev.melascrivi.com/soap/editor_dev.wsdl";
	const SERVER_PATH_dev="http://dev.melascrivi.com";
	
	const  API_PATH_it="http://app.melascrivi.com/soap/editor_it.wsdl";
	const SERVER_PATH_it="http://app.melascrivi.com";
	
	const  API_PATH_en="http://app.hotype.co.uk/soap/editor_uk.wsdl";
	const SERVER_PATH_en="http://app.hotype.co.uk";
	
	private $client;
	
	public function getName(){
		return self::PLUGIN_NAME;
	}
	
	public function getVersion(){
		return self::PLUGIN_VERSION;
	}

	function __construct(){
        $this->client= $this->getClient();
    }
    
  	function __destruct() {
       	unset($this->client);
	}
   	
	private function getServerPath(){
		$website=get_option(WP_DB_NAME_MELASCRIVI_WEBSITE);
		switch(strtolower ($website)){
			case "it":
				return self::SERVER_PATH_it;
			break;
			case "uk":
				return self::SERVER_PATH_en;
			break;
			case "dev":
				return self::SERVER_PATH_dev;
			break;
			default:
				return "";
			break;
		}
	}
	
	function login($user,$pass){
		
		$response=$this->client->login($user,$pass);
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		return $response;
	}
	
	protected function getClient() {
		$website=get_option(WP_DB_NAME_MELASCRIVI_WEBSITE);
		switch(strtolower ($website)){
			case "it":
				return new SoapClient(self::API_PATH_it,array('trace' => 1));
			break;
			case "uk":
				return new SoapClient(self::API_PATH_en,array('trace' => 1));
			break;
			case "dev":
				return new SoapClient(self::API_PATH_dev,array('trace' => 1));
			break;
			default:
				return null;
			break;
		}
    }
	
	
	protected function getOrder($userId,$order=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		
		$response=$this->client->getOrders($userId,$order,$orderColumn,$orderType,$limitStart,$limitSize);
		return $response;
	}
	
	protected function printOrderInfoBoxes($order){
	
		$echo='<div class="right-box" style="float:left;width: 36%;">
				<!-- BOX INFO ARTICOLO -->
				<section class="portlet grid_6 leading" style="float:right;width:98%;"> 
		        	<header>
		            	<h2>'.__( "detail article",$this->getName()).'</h2> 
		            </header>
		            <div class="section" style="padding: 6px;">
						<table class="full" id="articleInfo" style="table-layout:fixed;word-wrap:break-word;"> 
							<tbody> 
								<tr> 
									<td class="attName" style="width:130px;">' .__("article id",$this->getName()).':</td> 
									<td>'.$order->id.'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("article name",$this->getName()).':</td> 
									<td>'.$order->name.'</td> 
								</tr>
								<tr> 
									<td class="attName">'. __("article title",$this->getName()).':</td> 
									<td>'. ((!empty($order->title))? $order->title : "-").'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("article description",$this->getName()).':</td> 
									<td>'. $order->description.'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("project",$this->getName()).':</td> 
									<td>'.$order->project->name.'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("style",$this->getName()).':</td> 
									<td>'. $order->guideline->style->name.'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("category",$this->getName()).':</td> 
									<td>
										'. 
											$order->guideline->category->name
										.'
									</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("quality",$this->getName()).':</td> 
									<td>
										'.
											$order->qualityName
										.'
									</td> 
								</tr>';
								
								if($order->circle==1 && !empty($orderData["deadline"])){
								
									$echo.='<tr> 
										<td class="attName">'.__("deadline",$this->getName()).':</td> 
										<td>'. date("d/m/Y H:i:s",strtotime($order->deadline)).'</td> 
									</tr>';
								
								}
								
								$echo.='<tr> 
									<td class="attName">'. __("words",$this->getName()).':</td> 
									<td>'. $order->guideline->minWords." / ".$order->guideline->maxWords.'</td> 
								</tr>
                                
                                <tr> 
									<td class="attName">'.__("real words",$this->getName()).':</td> 
									<td>'. $order->realWords.'</td> 
								</tr>
                                
								<tr> 
									<td class="attName">'.__("guideline description",$this->getName()).':</td> 
									<td>'. (($order->guideline->description === "")? "-" : $order->guideline->description).'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("max price",$this->getName()).':</td> 
									<td>'.$order->price.'</td> 
								</tr>
                                <tr> 
									<td class="attName">'.__("price",$this->getName()).':</td> 
									<td>'.$order->realCost.'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("author",$this->getName()).':</td> 
									<td>'.((empty($order->authorId))? "-" : "User-".$order->authorId).'</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("status",$this->getName()).':</td> 
									<td>
									'.__("s".$order->status,$this->getName()).'
									</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("autoPubblication",$this->getName()).':</td> 
									<td>';
										
										if(!empty($order->autopublishTime)){
											$echo.= $order->autopublishTime.__("hours",$this->getName());
										}else 
											$echo.=__("no");
									
									$echo.='</td> 
								</tr>
								<tr> 
									<td class="attName">'.__("published",$this->getName()).':</td> 
									<td>';
										
										if(!empty($order->exported) and $order->exported!=0){
											$echo.= __("yes",$this->getName());
										}else 
											$echo.=__("no",$this->getName());
									
									$echo.='</td> 
								</tr>
							</tbody> 
						</table>
					</div>
				 </section>';
			$echo.='<section class="portlet grid_6 leading" style="float:right;width:98%;margin-top:15px;"> 
		        	<header style="padding-bottom: 0px; padding-top: 0px;">
		            	<table style="width:100%;">
                            <tr>
                                <td style="width: 90%;"><h2>'.__("order info user",$this->getName()).'</h2></td>
                                <td><img  style=" width:32px;  " src="'.plugins_url().'/melascrivi/images/171.png" onclick=\'javascript:window.alert("'.__("all the information are relative to what the author has wrote for you",$this->getName()).'");\'  title="'.__("all the information are relative to what the author has wrote for you",$this->getName()).'" alt="'.__("all the information are relative to what the author has wrote for you",$this->getName()).'"  />
                                </td>
                            </tr>
                        </table>

		            </header>
					<div class="section" style="padding: 15px;">
						<table class="full" id="articleHistory"> 
							<tbody>'; 
								
								$info= $this->getOtherUserInfo($order->userId,$order->authorId);
                                
                                	$echo.='<table class="full">';
                                if (count($info)==0){
                                    	$echo.= "<tr><td colspan='2'>".__("no other info",$this->getName())."</td></tr>";
                                }else{
                                    
                                    foreach ($info as $key=>$value){
                                        if (strpos($key,"rating")!==false)
                                            	$echo.= "<tr><td class=\"attName\" style=\"width:130px;\">".__($key,$this->getName())."</td><td>".__($value,$this->getName())."</td></tr>";
                                        else
                                            	$echo.= "<tr><td class=\"attName\" style=\"width:130px;\">".__($key,$this->getName())."</td><td>$value</td></tr>";
                                    }
                                    
                                }
                                	$echo.= "</table>";
								
								$echo.='</tbody> 
						</table>
					</div>
				 </section> ';
				
			$echo.='</div>';
		return $echo;
	
	}
	
	
	function printOrder($userId,$orderId){
		$order=new stdClass();
		$order->id=$orderId;
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->getOrders($userId,$order);
        
		if ($response->order->status==6){
			$echo="<div id='articolo' class='left-box' style='float: left; width: 64%;'>
			<section class='portlet grid_6 leading' style='width: 98%'> 
				<header>
		            	<h2>".__("article",$this->getName())."</h2> 
		            	<h2 id='topStatusInfo'>
						".__("s".$response->order->status,$this->getName())."
						</h2>
		            </header>
				<h2 style='margin: 10px;'>".$response->order->title."</h2>
				<p style='margin: 10px;'>".nl2br ($response->order->text)."</p>
				<form method='get' >
					<input type=\"hidden\" name=\"page\" value=\"melascrivi-show-article\"/>
					<input name='action' type='hidden' value='saveDraft'/>
					<input name='orderId' type='hidden' value='".$response->order->id."'/>
					<button class=\"mela\" style='margin: 10px; width:120px;' >".__("save draft",$this->getName())."</button>
				</form>
				</section>
				</div>".$this->printOrderInfoBoxes($response->order)."
			";	
		}else if ($response->order->status==3 or $response->order->status==16 ) {
			$echo="<div id='articolo' class='left-box' style='float: left; width: 64%;'>
			<section class='portlet grid_6 leading' style='width: 98%'> 
				<header>
		            	<h2>".__("article",$this->getName())."</h2> 
		            	<h2 id='topStatusInfo'>
						".__("s".$response->order->status,$this->getName())."
						</h2>
		            </header>";
				if(!empty($response->order->statusComment)){
									
				$echo.= "<div id='correctMsgBox'>
							<b>".__("correction notes",$this->getName()).": </b><br>".$response->order->statusComment."
						</div>";				
				}
				$echo.= "<h2 style='margin: 10px;'>".$response->order->title."</h2>
				
				<div style='border: 5px solid #F4F4F4; height:600px; overflow-y: scroll;'>";
				
				
					$echo.="<img oncontextmenu=\"return false\" oncopy=\"return false\" src='".$this->getServerPath()."/managers/imageManager.php?action=getTextImage&orderId=".$response->order->id."&width=590&ck=".md5($response->order->id."M3l4".$response->order->userId)."'/> 
				</div>
				</section>
				</div>
				".$this->printOrderInfoBoxes($response->order)."
				<div id='revisionBtns' style=\"float:left;width:100%;margin-left:1.3%;\">
				<button class='mela' style=\"background:#D9E4AC;width:160px;\" onclick='acceptOrder(\"".$response->order->id."\")' >".__("accept",$this->getName())."</button>
				<button class='mela' style=\"background:#F9EE9C;width:160px;\" onclick='correctOrder(\"".$response->order->id."\")' >".__("correct",$this->getName())."</button>
				<button class='mela' style=\"background:#F2CACB;width:160px;\" onclick='rejectOrder(\"".$response->order->id."\")'>".__("reject",$this->getName())."</button>
				</div>";
				$url = plugins_url();
				
				$echo.='<div id="revisionStartContent" class="revisionContent">
			'. __("select a button to evaluate the order","melascrivi").'
		</div>
									
		<div id="revisionAcceptContent" class="revisionContent" style="display: none;">
			<form action="" id="revisionAcceptForm" >
				<table style="width: 100%;margin-bottom:1px; " >
					<tr>
						<td align="left" colspan="3" style="padding-bottom:10px;font-size:15px;font-weight:bold;">
							<img src="'.$url.'/melascrivi/images/navicons-small/146.png" /> '. __("ratings user","melascrivi").
						'</td>
					</tr>
					<tr align="left" style="line-height:27px;">
						<td align="right" style="width:55%;">'.  __("insufficient","melascrivi").'</td>
						<td align="right" style="width:14%;">'.  __("sufficient","melascrivi").'</td>
						<td align="right" style="width:14%;padding-right:6px;">'. __("good","melascrivi").'</td>
						<td align="right" style="width:22%;">'. __("excellent","melascrivi").'</td>
					</tr>
					<tr style="line-height:25px;">
						<td align="left" colspan="4">
							<input type="hidden" id="styleVal" name="styleVal" style="border:0; color:#f6931f; font-weight:bold;" />
							<div style="float:left;">'. __("style rating","melascrivi").':</div>
							<div id="styleSlider" class="slider">
							</div>
						</td>
					</tr>
					<tr style="line-height:25px;">
						<td align="left" colspan="4">
							<div style="float:left;">'. __("guideline rating","melascrivi").':</div>
							<input type="hidden" id="guidelineVal" name="guidelineVal" style="border:0; color:#f6931f; font-weight:bold;" />
							<div id="guidelineSlider" class="slider"></div>
						</td>
					</tr>
					<tr style="line-height:25px;">
						<td  align="left" colspan="4">
							<div style="float:left;">'. __("grammar rating","melascrivi").':</div>
							<input type="hidden" id="grammarVal" name="grammarVal" style="border:0; color:#f6931f; font-weight:bold;" />
							<div id="grammarSlider" class="slider"></div>
						</td>
					</tr>
					<tr align="left">
						<td style="width:40%;padding-top:25px;line-height:12px;font-weight:bold;">'. __("global judgment","melascrivi").':</td>
						<td colspan="3" style="padding-top: 25px; line-height: 12px;">
						
							<!--<div style="float:right;margin-right:10px;"><?php echo $orders["author_circle_placeHolder"]; ?></div> 
						-->
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="hidden" id="evaluation" name="evaluation" value="10" />
							<input type="hidden" name="orderId" value="'.$response->order->id.'" />
							<input type="hidden" name="page" value="melascrivi-show-article"/>
							<input type="hidden" name="action" value="acceptArticle" />
							<div id="overallBar" class="progress" style="width:80%;">
								<span id="overallBarPercent" style="width: 33%;"></span>
								<img src="'. $url.'/melascrivi/images/navicons-small/91.png" style="position: absolute; left: 3px; top: 24px;"/>
								<img src="'.$url.'/melascrivi/images/navicons-small/108.png" style="position: absolute; left: 46%; top: 24px;"/>
								<img src="'. $url.'/melascrivi/images/navicons-small/85.png" style="position: absolute; right: 3px; top: 24px;"/>
							</div>
						</td>
						<td align="right" colspan="3">
							<button class="mela" id="revisionAcceptBtn" style="width:66.5%;height:41px;margin-top:9px;color:#f4f4f4;text-shadow:none;"  >
								'. __("accept","melascrivi").'
							</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div id="revisionCorrectContent" class="revisionContent" style="display: none;">
			<form action="#" id="revisionCorrectForm" >
				<input type="hidden" name="action" value="correctArticle" />
				<input type="hidden" name="page" value="melascrivi-show-article"/>
				<input type="hidden" name="orderId" value="'.$response->order->id.'" />
				<input type="hidden" name="viewTime" value="'.time().'" />
				<table style="width: 100%;">
					<tr>
						<td align="left" style="padding-left:8px;font-size:15px;font-weight:bold;">
							'. __("tip for author","melascrivi").':
						</td>
					</tr>
					<tr>
						<td><textarea name="note"></textarea></td>
					</tr>
					<tr>
						<td align="right">
							<button class="mela" id="revisionCorrectBtn" style="margin-top:23px;margin-right:8px;margin-bottom:0px;width:29%;height:41px;color:#f4f4f4;text-shadow:none;">
								'. __("correct" ,"melascrivi").'
							</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
									<div id="revisionRejectContent" class="revisionContent" style="display: none;">
										<form action="#" id="revisionRejectForm" >
											<input type="hidden" name="action" value="rejectArticle" />
											<input type="hidden" name="page" value="melascrivi-show-article"/>
											<input type="hidden" name="orderId" value="'.$response->order->id.'" />
                                            <input type="hidden" name="viewTime" value="'.time().'" />
											<table style="width: 100%;">
												<tr>
													<td align="left" style="padding-left:8px;font-size:15px;font-weight:bold;">
							                            '. __("motivation","melascrivi").':
							                        </td>
							                   </tr>
							                   <tr>
													<td><textarea name="motivation"></textarea></td>
												</tr>
												<tr>
													<td align="right">
														<div style="float:left;margin-left:7px;">
															<input type="checkbox" style="margin-top:2px;float:left;" name="blackListAuthor" value="'.$response->order->authorId.'">
															<div style="float:left;margin-left:5px;">'. __("this author don't write for me anymore","melascrivi").'</div>
														</div>
														<button class="mela" id="revisionRejectBtn" style="margin-top:23px;margin-right:8px;margin-bottom:0px;width:29%;height:41px;color:#f4f4f4;text-shadow:none;">
															'. __("reject","melascrivi").'
														</button>
													</td>
												</tr>
											</table>
										</form>
									</div>
								</div>';
		
		
				
				
				
				
		}else
			$echo="<div id='articolo' class='left-box' style='float: left; width: 64%;'>
			<section class='portlet grid_6 leading' style='width: 98%'> 
				<header>
		            <h2 >".__("article",$this->getName())."</h2> 
		            <h2 id='topStatusInfo'>".__("s".$response->order->status,$this->getName())."</h2>
		        </header>
				<h2 style='margin: 10px;'>".$response->order->title."</h2>
				<p style='margin: 10px;'>".__("no text yet",$this->getName())."</p>
				</section>
				</div>".$this->printOrderInfoBoxes($response->order)."
			";	
		return $echo;
	}
	
	function returnSortingClass($name,$orderColumn,$orderType,$noSorting=false){
		if($noSorting)
			return "";
		if($orderColumn==$name){
			if (strtolower($orderType)=="desc")
				$echo="sorting_desc";
			else
				$echo="sorting_asc";
		}else
			$echo="sorting";
		return $echo;
	}
	
	function printOrders($userId,$order=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null,$noSorting=false,$hideButton=false){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$result=$this->getOrder($userId,$order,$orderColumn,$orderType,$limitStart,$limitSize);
		$echo =$this->printSortingStyle()."
		<table style='width:100%;'>
			<tr>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"id\");'")."  style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px; 'class ='".$this->returnSortingClass("id",$orderColumn,$orderType,$noSorting)."'>".__("id",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"name\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("name",$orderColumn,$orderType,$noSorting)."'>".__("title",$this->getName())."</th>
				
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"project\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("project",$orderColumn,$orderType,$noSorting)."'>".__("project",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"guideline\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("guideline",$orderColumn,$orderType,$noSorting)."'>".__("guideline",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"price\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("price",$orderColumn,$orderType,$noSorting)."'>".__("price",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"qualityId\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("qualityId",$orderColumn,$orderType,$noSorting)."'>".__("quality",$this->getName())."</th>
				
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"lastStatusDate\");'")." class ='".$this->returnSortingClass("lastStatusDate",$orderColumn,$orderType,$noSorting)."' style='  border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("last status date",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"status\");'")." class ='".$this->returnSortingClass("status",$orderColumn,$orderType,$noSorting)."' style='  border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("status",$this->getName())."</th>";
			
		if (!$noSorting){
			$echo.="<th style='  border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("action",$this->getName())."</th>";
		}
		$echo.="</tr>";
		
		if (count($result->order)==1){
		
			$order=$result->order;
			
			switch ($order->status){
			case "1": $color="#00529B";
				break;
			case "3": $color="#504030";
				break;
			case "6": $color="#4F8A10";
				break;
			case "7": $color="#D8000C";
				break;
			case "8": $color="#D8000C";
				break;
			case "13": $color="#CC0066";
				break;	
				
			default:
				$color="#00529B";
			}
			
			$echo.="<tr style='color:$color;'>";
			if (isset($order->project->proofReading)){
				if ($order->project->proofReading->level==0){
					$img='<img  style=" width:16px;  " src="'.plugins_url().'/melascrivi/images/proofRead.png"/>';
				}else{
					$img='<img  style=" width:16px;  " src="'.plugins_url().'/melascrivi/images/proofRead_low.png"/>';
				}
			}else $img=var_export($order->project->proofReading,true);
			
			$echo.="<td style='text-align:center;'>$img<b style='cursor:pointer' onclick='openOrder(\"".$order->id."\")'>".$order->id."</b></td>";
			$echo.="<td style='text-align:center;' ><b style='cursor:pointer' onclick='openOrder(\"".$order->id."\")'>".$order->name."</b></td>";
			
			$echo.="<td style='text-align:center;' >".$order->project->name."</td>";
			$echo.="<td style='text-align:center;'>".$order->guideline->category->name.",".$order->guideline->style->name.",".$order->guideline->minWords."/".$order->guideline->maxWords."</td>";
			$echo.="<td style='text-align:center;' >".$order->price."</td>";
			$echo.="<td style='text-align:center;'>".$order->qualityName."</td>";
			
			$echo.="<td style='text-align:center;'>".$order->lastStatusDate."</td>";
			
			if ($order->status==13){
				$programmed=" (".$order->date.")";
			}else{
				$programmed="";
			}
				
			$echo.="<td style='text-align:center;'>".__("s".$order->status,$this->getName())."$programmed</td>";
			
			if (!$noSorting){
				if($order->status==1 or $order->status==13 )
					$echo.="<td style='text-align:center;'>
								<div style='text-align:center; width:100%;' class=\"removeOrderBtn\" name=\"".$order->id."\" onclick=\"removeOrder(this);\">
									&nbsp;
								</div>
							</td>";
			}
			
			
			$echo.="</tr>";
		
		}else if (count($result->order)>1){
			foreach($result->order as $order){
				switch ($order->status){
				case "1": $color="#00529B";
					break;
				case "3": $color="#504030";
					break;
				case "6": $color="#4F8A10";
					break;
				case "7": $color="#D8000C";
					break;
				case "8": $color="#D8000C";
					break;
				case "13": $color="#CC0066";
				break;
				default:
					$color="#00529B";
				}
				
				$echo.="<tr style='color:$color;'>";
				
				if (isset($order->project->proofReading)){
					if ($order->project->proofReading->level==0){
						$img='<img  style=" width:16px;  " src="'.plugins_url().'/melascrivi/images/proofRead.png"/>';
					}else{
						$img='<img  style=" width:16px;  " src="'.plugins_url().'/melascrivi/images/proofRead_low.png"/>';
					}
				}else $img='';
				
				$echo.="<td style='text-align:center;'> $img<b style='cursor:pointer' onclick='openOrder(\"".$order->id."\")'>".$order->id."</b></td>";
				$echo.="<td style='text-align:center;'><b style='cursor:pointer' onclick='openOrder(\"".$order->id."\")' >".$order->name."</b></td>";
				
				$echo.="<td style='text-align:center;' >".$order->project->name."</td>";
				$echo.="<td style='text-align:center;'>".$order->guideline->category->name.",".$order->guideline->style->name.",".$order->guideline->minWords."/".$order->guideline->maxWords."</td>";
				$echo.="<td style='text-align:center;' >".$order->price."</td>";
				$echo.="<td style='text-align:center;'>".$order->qualityName."</td>";
				
				$echo.="<td style='text-align:center;'>".$order->lastStatusDate."</td>";
				if ($order->status==13){
					$programmed=" (".$order->date.")";
				}else{
					$programmed="";
				}
				
				$echo.="<td style='text-align:center;'>".__("s".$order->status,$this->getName())."$programmed</td>";
				
				if (!$noSorting){
					if($order->status==1 or $order->status==13 )
						$echo.="<td style='text-align:center;'>
									<div style='text-align:center; width:100%;' class=\"removeOrderBtn\" name=\"".$order->id."\" onclick=\"removeOrder(this);\">
										&nbsp;
									</div>
								</td>";
				}
				
				$echo.="</tr>";
			}
		}else{
			$echo.="<tr>
				<td colspan='8' style='text-align:center;'>
					".__("no record found",$this->getName())."
					
				</td>
			</tr>";
		
		}
		$echo .="</table>";
		
		
		if($hideButton==true){
			
			$echo .="<div style='padding:20px; color:grey;'>".__("total",$this->getName()).":".$result->displayed;
			if (intval($result->displayed)<intval($result->totals))
				$echo .=" (".__("filtered from",$this->getName())." " .$result->totals." ".__("total result",$this->getName()).")" ;
			$echo .="</div>";
		
			$echo.="<script>";
			if ((intval($limitStart)+intval($limitSize))<intval($result->displayed)){
				$echo.="jQuery('#mela_avanti').show();";
			}else{
				$echo.="jQuery('#mela_avanti').hide();";
			}
			
			if (intval($limitStart)>=intval($limitSize)){
				$echo.="jQuery('#mela_indietro').show();";
			}else{
				$echo.="jQuery('#mela_indietro').hide();";
			}
			$echo.="</script>";
		}
					
		return $echo;
	
	}
	
	function getBalance($userId){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->getBalance($userId);
		return $response;
	}
	
	function printBalance($userId){
		try{
			$result=$this->getBalance($userId);
			$conf=$this->getConfiguration($userId);
			if ($result>2)
				$color="green";
			else
				$color="red";
		}catch (Exception $e){
		}
		$echo ="
		<script>
			function updateBalance(){
				jQuery.post(ajaxurl, {action:\"get_balance\"}, function(data){
					var msg = jQuery.trim(data);
					jQuery('#balanceValue').html(msg);
				});
			}
			setInterval(updateBalance,15000);
		</script>
		<div id='deposits' style='color:$color;white-space: nowrap;' ><b>".__("balance",$this->getName()).":</b> <span id='balanceValue'>".$result."</span> ".$conf->valute."</div>";
		return $echo;
	}
	
	function requestOrder($userId,$orderArray){
		$orders=array();
		foreach($orderArray as $o){
			$order=new stdClass();
			$order->name=$o['title'];
			$order->description=$o['description'];
			$order->project->id=$o['projectId'];
			$order->guideline->id=$o['guidelineId'];
			$order->qualityId=$o['qualityId'];
			$order->deadline=$o['deadline'];
			$order->circle=$o['circle'];
			$order->programmed=$o['programmed'];
			//var_dump($order);
			$orders[]=$order;
		}
		$articles=new stdClass();
		$articles->order=$orders;
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->createOrder($articles,$userId);
		return $response;
	
	}
	function getQualityPrice(){
		$result=$this->getQuality();
		$echo =array();
		if (count($result->quality)==1){
			$quality=$result->quality;
				$echo[$quality->id]=$quality->price;
		}else{
			foreach($result->quality as $quality){
				$echo[$quality->id]=$quality->price;
			}
		}
		return $echo;
	}
	
	
	function printCreateOrder($userId){
		$conf=$this->getConfiguration($userId);
		$quality=$this->getQualityPrice();
		$echo=$this->printWaitGif().'
		<style>
		#createOrderForm div.selector, #modifyOrderForm div.selector, #createOrderForm input {
			width: 92%; float: left;
		}
		#createOrderForm tr td, #modifyOrderForm tr td { line-height: 37px; }
		#createOrderForm .selector { float: left; }
		#createOrderForm .createOrderBtn:hover {
			height: 15px; margin-left: 9px; margin-top: 5px; width: 15px;
		}
		#createOrderForm textarea, #modifyOrderForm textarea {
			max-height: 80px; max-width: 295px; min-height: 80px; min-width: 92%; margin-top: 13px;
		}
		.right-box section.portlet { float:right; width:100%; margin-bottom: 6px; }
		.right-box table tr { line-height: 30px; }
		#detailMain .right-box table tr { border-bottom: 1px solid #CCCCCC;	}
		#detailMain .right-box table tr:last-child { border-bottom: none; }
		#detailMain .right-box table tr td {
			line-height: 16px; padding-bottom: 6px; padding-top: 6px; vertical-align: middle;
		}
		#articlesTable tr { line-height: 17px; }
		.portlet > div.section {
			background: none repeat scroll 0 0 #FFFFFF; border-color: #CCCCCC; -moz-border-bottom-colors: none;
			-moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none;
			-moz-border-top-colors: none; border-style: solid; border-width: 0 1px 1px;
		}
		#articlesTable_length, #articlesTable_first, #articlesTable_last { display: none; }
		#articlesTable_info { width: 80%; }
		#articlesTable_filter { width: 70%; }
		#articlesTable_paginate { margin-top: 8px; width: 60px; }
		#articlesTable_previous { margin-right: 5px; }
		#articlesTable_wrapper { min-height: 285px; margin-left: -4px; }
		#articlesTable .removeWebSiteBtn { margin: 0; }
		#ordersTable_paginate { width: 290px; margin-right: 85px;}
		#articlesTable .removeOrderBtn {
			background: url("'.plugins_url().'/melascrivi/css/images/delete-icon.png") repeat-x scroll center center transparent;
			cursor: pointer; float: left; height: 13px; margin-left: 30px; width: 13px;
		}


		#emptyTableBtn {
			background: url("'.plugins_url().'/melascrivi/css/images/navicons/73.png") no-repeat scroll center center transparent;
			cursor: pointer; width: 24px; height: 24px; 
			position: absolute; 
			top: 15px; 
			left: 18px;
		}
		#emptyTableBtn:hover { top: 18px; }


		html #addArticlesBtn {
			background: url("'.plugins_url().'/melascrivi/css/images/freccia.png") no-repeat scroll center right transparent;
			float: right; font-size: 16px; font-weight: bold; line-height: 40px; box-shadow: 0 0 6px #888888 inset;
			text-align: left; vertical-align: middle; width: 220px; cursor: pointer; color: #f1f1f1; text-indent: 10px;
		}
		#createOrderForm .lines {
			margin-left: -21px; max-height: 158px; max-width: 44px; margin-right: 3px;
		}
		#createOrderForm textarea[name=\'titles\'] {
			box-shadow: none; min-width: 92%; margin-top: 0; padding: 0; min-height: 154px;
		}
		#createOrderForm .linedwrap{
			max-width: 89.5%; min-width: 89.5%; margin-top: 10px;
		}
		#createOrderForm .codelines { padding-top:0; }
		#orders .tabs > section { padding: 5px; }
		#orders .dataTables_info { margin-top: 5px; margin-left: 7px;}
		#createOrderForm select option, #modifyOrderForm select option { padding-left: 5px; }
		#articlesTable .removeArticleBtn {
			width: 13px; height: 12px; cursor: pointer; margin-left: auto; margin-right: auto;
		}
		#articlesTable .removeArticleBtn:hover {
			width: 13px; height: 12px; margin-top: 8px; margin-left: auto; margin-right: auto;
		}
		.shortText {
			overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;
		}
		#createOrderForm div.selector span { white-space: normal; }
		#articleStrContainer { padding: 20px; }
		#articleStrTitle { font-size: 19px; font-weight: bold; }
		#articleStrText {
			font-size: 15px; line-height: 21px; font-family: arial; text-align: justify;
		}
		.articleTitle:hover { text-decoration: underline; }
		#acceptedImg {
			background: url("'.plugins_url().'/melascrivi/css/images/navicons-small/92.png") no-repeat scroll center center transparent;
			float:left; width:20px; height:20px;
		}
		#rejectedImg {
			background: url("'.plugins_url().'/melascrivi/css/images/navicons-small/172.png") no-repeat scroll center center transparent;
			float:left; width:19px; height:20px;
		}

		#ordersTable tr td div.status1Label, #ordersTable tr td div.status0Label {
			color: #00529B;
		}
		#ordersTable tr td div.status2Label {
			color: #00529B;
		}
		#ordersTable tr td div.status3Label {
			color: #9F6000;
		}
		#ordersTable tr td div.status4Label {
			color: #00529B;
		}
		#ordersTable tr td div.status5Label {
			color: #D8000C;
		}
		#ordersTable tr td div.status6Label {
			color: #4F8A10;
		}
		#ordersTable tr td div.status7Label {
			color: #D8000C;
			font-weight: bold;
		}
		#ordersTable tr td div.status8Label {
			color: #D8000C;
		}


		span.customSelect {
			font-size:12px;
			//background-color: red;
			color:#7c7c7c;
			padding-left:7px;
			border:1px solid #aaa;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			border-radius: 5px 5px;
			//width:88%;
		}
	/*	span.customSelect.changed {
			background-color: #f0dea4;
		}*/
		.customSelectInner {
			background:url("'.plugins_url().'/melascrivi/images/arrow-down.png") no-repeat center right;
			white-space: nowrap;
			overflow: hidden;
		}
	#uniform-datepickerProgrammingTime{
        width: 100px !important;
        margin-left: 20px;
    }
</style>
		
		<script>
			var qualitySelectInit=0;
			function showQualitySelect(){
			
				jQuery("#deadlineTr").hide();
				jQuery("#qualityTr").show();
				if(qualitySelectInit==0){
					jQuery("#mela_quality").customSelect();
					qualitySelectInit=1;
				}
				jQuery("#circleCommissionTr").hide();
			}
			var timeSelectInit=0;
			function showUseCircle(){
				jQuery("#deadlineTr").show();
				jQuery("#qualityTr").hide();
				jQuery("#circleCommissionTr").show();
				if(timeSelectInit==0){
					jQuery("#deadlineTime").customSelect();
					timeSelectInit=1;
				}
			}
			function enableCircleCk(){
				jQuery("#circleArticles").removeAttr("disabled");   
			}
			function disableCircleCk(){
				jQuery("#circleArticles").attr("checked", false);
				showHideCircle();
				jQuery("#circleArticles").attr("disabled", true);
			}
			function showHideCircle (){
				
				if(jQuery("#circleArticles").is(\':checked\')){
					showUseCircle();
				}else{
					showQualitySelect();
				}
			}
			
			
			function changeProject(){
				var str= jQuery("#mela_project").val();
				obj=jQuery.parseJSON(str);
				
				if (obj.circleUsers==0){
					disableCircleCk();
				}else{
					enableCircleCk();
				}
				jQuery("#circleArticles").attr("checked", false);
				showHideCircle();
				jQuery("#numAuthorsProject").html("("+obj.circleUsers+" '.__("authors",$this->getName()).' )");
				if (obj.featureCost>0){
					jQuery("#featureCosts").html(obj.featureCost);
					jQuery("#featureCommissionTr").show();
				}else{
					
					jQuery("#featureCosts").html("0");
					jQuery("#featureCommissionTr").hide();
				}
		
			}
			
			function changeGuideline(){
				var selected =jQuery("#mela_guideline").find("option:selected");
				var str= jQuery(selected).data("test");
				jQuery("#mela_description").html(stripslashes(str));
				updateOrdersInfo();
			}
			function changeQuality(){
				var str= jQuery("#mela_quality").val();
				obj=jQuery.parseJSON(str);
				updateOrdersInfo();
			}
			
			/**/
			function updateOrdersInfo(){
			
				var totalCount = 0;
				var totalCircleCount = 0;
				var totalFeature = 0;
			
				for(var i=0; i < jQuery(\'#articlesTable\').dataTable().fnGetNodes().length; i++) {
					var row = jQuery(\'#articlesTable\').dataTable().fnGetData(i);
					if(parseInt(row[5]) == 1){
						totalCircleCount++;
					}else{
						totalCount++;
					}
					if(parseInt(row[6]) > 0){
						totalFeature++;
					}
				}
				
				var count = 0;
				var vals = jQuery.trim(jQuery("#createOrderForm textarea[name=\'titles\']").val());
				if(vals != ""){
					var r = vals.split(/[\r\n]/g);
					for(var i = 0; i < r.length; i++){
						if(jQuery.trim(r[i]) != ""){
							count++;
						}
					}
				}

				if(jQuery("#createOrderForm select[name=\'quality\']").is(\':visible\')){
					totalCount += count;
				}else{
					totalCircleCount += count;
				}
		
				jQuery("#ordersNumber").html(totalCount+totalCircleCount);
				jQuery("#commisionsNumber").html(totalCount+totalCircleCount);
				jQuery(".featureOrdersNumber").each(function(){
					jQuery(this).html(totalFeature+count);
				});

				jQuery("#circleNumber").html(totalCircleCount);
				updateTotalPrice(count);
			}
	
			/**/
			
			
			function updateTotalPrice(ordersNumber){
				var total = 0;
				var commission = '.$conf->commission.';
				if(ordersNumber > 0){
					var maxWords = jQuery("#createOrderForm select[name=\'guideline\'] option:selected").html();
					
					if(jQuery("#createOrderForm select[name=\'guideline\'] option:selected").val() != ""){
					
					
						maxWords = maxWords.split(",")[2];
						maxWords = maxWords.split("/")[1];
						if(maxWords.indexOf("(") > 0){
							maxWords = maxWords.substring(0, maxWords.indexOf("("));
						}
					}else maxWords = 0;
					
					
					var price = 0;
					var circleCommission = 0;
					if(jQuery("#createOrderForm select[name=\'quality\']").is(\':visible\')){
						price = jQuery("#createOrderForm select[name=\'quality\'] option:selected").html();
	
						if(jQuery("#createOrderForm select[name=\'quality\'] option:selected").val() != ""){
							price = price.split(" ")[1];
							price = price.substring(1, price.length);
						}else price = 0;
						if(parseInt(jQuery("#circleNumber").html()) > 0){
							jQuery("#circleCommissionTr").show();
						}else{
							jQuery("#circleCommissionTr").hide();
						}
					}else{
						price = parseFloat('.$quality[1].');
						if(jQuery("#project").val() != ""){
							jQuery("#circleCommissionTr").show();
						}
						if(ordersNumber > 0){
							jQuery("#circleCommissionTr").show(); 
						}
						circleCommission = (parseInt(ordersNumber) * parseFloat('.$conf->circleFee.'));
		
					}
			
					var articleCost = (parseInt(maxWords) * parseFloat(price));
					articleCost += commission;
					articleCost = Math.round((articleCost*100)+0.4) / 100;
					total = (articleCost*ordersNumber) + parseFloat(jQuery("#oldTotalValue").html()) + parseFloat(circleCommission);
					
					var tableFeature = 0;
					for(var i=0; i < jQuery("#articlesTable").dataTable().fnGetNodes().length; i++) {
						var row = jQuery("#articlesTable").dataTable().fnGetData(i);
						if(parseInt(row[6]) > 0){
							tableFeature++;
						}
					}
					
					jQuery(".featureOrdersNumber").each(function(){
						total += (parseFloat(jQuery("#featureCosts").html())*(parseInt(jQuery(this).html())-parseInt(tableFeature)));
					});
					proofReadCost=proofReadingCost(\'createOrderForm\');
					total+=parseFloat(proofReadCost);
			
					total = Math.round((total*100)+0.4) / 100;
				}else {
					total = parseFloat(jQuery("#oldTotalValue").html());
					proofReadingCost(\'createOrderForm\');
				}
				jQuery("#totalOrdersCost").html(total);
			}
			
			function setDeadline(){
				programming = jQuery("#programmingTr input[name=\'datepickerProgramming\']").val();
				programming += " "+jQuery("#datepickerProgrammingTime").val()+":00";
				var dateArray = programming.split(\' \');
				var year = dateArray[0].split(\'-\');
				var time = dateArray[1].split(\':\');
				programmingDate = new Date(year[2], year[1]-1, year[0], time[0], time[1], time[2]);
				maxProgrammingDate=new Date();
				maxProgrammingDate.setDate(programmingDate.getDate()+7);
				if (maxProgrammingDate<programmingDate)
					maxProgrammingDate.setMonth(maxProgrammingDate.getMonth()+1);
				jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "maxDate", maxProgrammingDate);
				jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "minDate", programmingDate );
			}
			
			alreadyShowProg=0;
			function showHideProgramming(elem){
				if(jQuery(elem).is(\':checked\')){
					jQuery("#programmingTr").show();
					if (alreadyShowProg==0){
						alreadyShowProg=1;
						jQuery("#datepickerProgrammingTime").customSelect();
					}
					jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "maxDate", "+5w" );
				jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "minDate", "1d" );
				}else{
					jQuery("#programmingTr").hide();
					jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "maxDate", "+1w" );
					jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "minDate", "0d" );
				}
			}
			
			jQuery(document).ready(function(){
			
				jQuery("#datepickerDeadline,#newDeadline").datepicker({ dateFormat: \'dd-mm-yy\'});
				jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "maxDate", "+1w" );
				jQuery("#datepickerDeadline,#newDeadline").datepicker( "option", "minDate", "0d" );
				
				
				jQuery("#datepickerProgramming").datepicker({ dateFormat: "dd-mm-yy"});
				jQuery("#datepickerProgramming").datepicker( "option", "maxDate", "+4w" );
				jQuery("#datepickerProgramming").datepicker( "option", "minDate", "1d" );
		
				jQuery("#articlesTable").dataTable( {
					"sPaginationType": "two_button",
					// set the initial value
					"iDisplayLength": 6,
					"bRetrieve": true,
	        
					"aoColumnDefs": [{"bVisible": false, "aTargets": [0]}, 
                            {"bVisible": false, "aTargets": [1]},
	                         {"bVisible": false, "aTargets": [2]},
                             {"bVisible": false, "aTargets": [3]},
                            {"bVisible": false, "aTargets": [4]},
                            {"bVisible": false, "aTargets": [5]},
                            {"bVisible": false, "aTargets": [6]},
                            {"bVisible": true, "aTargets": [7]},
                            {"bVisible": true, "aTargets": [8]},
                            {"bVisible": false, "aTargets": [9]},
							{"bVisible": false, "aTargets": [10]}
                            ],      
					"oLanguage": {
						"sProcessing": "'.__("charging",$this->getName()).'",
						"sLengthMenu": "'.__("num_elements",$this->getName()).'",
						"sZeroRecords": "'.__("no record found",$this->getName()).'",
						"sEmptyTable": "'.__("no_elements",$this->getName()).'",
						"sInfo": "'.__("total_results",$this->getName()).'",
						"sInfoEmpty": "'.__("zero_results",$this->getName()).'",
						"sInfoFiltered": "'.__("filtered",$this->getName()).'",
						"sInfoPostFix": "",
						"sSearch": "'.__("search",$this->getName()).'",
						"oPaginate": {
							"sFirst": "",
							"sPrevious": "",
							"sNext": "",
							"sLast": ""
						}
					},
					"width":"300px"
				} );
			});
			
		function validateOrders(){
		
			if (jQuery("#mela_project").val()==\'{"id":"0","featureCost":"","circleUsers":""}\'){
				viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("you must choose a project!",$this->getName()).'\',3000);
				return false;
			}
			if (jQuery("#mela_guideline").val()==0){
				viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("you must choose a guideline!",$this->getName()).'\',3000);
				return false;
			}
			if(!jQuery("#circleArticles").is(\':checked\')){
				if (jQuery("#mela_quality").val()==0){
					viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("you must choose a quality!",$this->getName()).'\',3000);
					return false;
				}
			}
			
			if (jQuery("#mela_description").val()==""){
				viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("you must insert a description!",$this->getName()).'\',3000);
				return false;
			}
			return true;
			
		}
			
		function addOrdersToTable(formId,tableName){
			// Per effettuare il controllo dei campi vuoti del form
			if(!validateOrders()){
				return false;
			}
		
		var commission = '.$conf->commission.';
		var specialCircle = jQuery("#superCircles").val();
		//var projectId = jQuery("#"+formId+" select[name=\'project\']").val();
		var str= jQuery("#mela_project").val();
		obj=jQuery.parseJSON(str);
		var projectId = obj.id;
		
		var guidelineId = jQuery("#"+formId+" select[name=\'guideline\']").val();
		var qualityId = 1;
		if(jQuery("#"+formId+" select[name=\'quality\']").is(\':visible\')){
			qualityId = jQuery("#"+formId+" select[name=\'quality\']").val();
		}else{
			qualityId = 0;
		}
		var description = jQuery("#"+formId+" textarea[name=\'description\']").val();
		var deadline = "";

		if(jQuery("#"+formId+" input[name=\'datepickerDeadline\']").is(\':visible\')){
			if(jQuery("#"+formId+" input[name=\'datepickerDeadline\']").val() != ""){
			
				deadline = jQuery("#"+formId+" input[name=\'datepickerDeadline\']").val();
				deadline += " "+jQuery("#deadlineTime").val()+":00";
				var now = new Date();
				now = new Date(now.getTime() + 2*60*60*1000);
				var dateArray = deadline.split(\' \');
		        var year = dateArray[0].split(\'-\');
		        var time = dateArray[1].split(\':\');
		        deadlineDate = new Date(year[2], year[1]-1, year[0], time[0], time[1], time[2]);
				if(deadlineDate < now){
					viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("the deadline should be at leat 2 hours from current time",$this->getName()).'\',3000);
					return false;
				}
			}else{
				viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("you must choose a date!",$this->getName()).'\',3000);
					return false;
			}
		}
		
		
		/** programmazione */
        programmingDate="";
        if(jQuery("#"+formId+" input[name=\'datepickerProgramming\']").is(\':visible\')){
			
			if(jQuery("#"+formId+" input[name=\'datepickerProgramming\']").val() != ""){
				programmingDate = jQuery("#"+formId+" input[name=\'datepickerProgramming\']").val();
				programmingDate += " "+jQuery("#datepickerProgrammingTime").val()+":00";
				
			}else{
				viewMessageBox(\'genericErrorMsgBox\',\''.__("error",$this->getName()).'\',\''.__("To programm the publication of an order is necessary to choose the date and hours of insertion",$this->getName()).'\',5000);
					return false;
			}
		}
        
        /** fine programmazione */
		
		var removeBtn = "<div class=\'removeArticleBtn\' onclick=\'removeOrderFromTable(this);\'>&nbsp;</div>";
		var titles = jQuery("#"+formId+" textarea[name=\'titles\']").val();
		var totalPrice = 0;
		titles = titles.split(/[\n\r]/g);
		var price = "0.008";
		var circleCommission = 0;
		if(jQuery("#"+formId+" select[name=\'quality\']").is(\':visible\')){
			price = jQuery("#"+formId+" select[name=\'quality\'] option:selected").html();
			price = price.substring(price.indexOf("(")+1,price.indexOf("(")+6);
			price = jQuery.trim(price);
		}else{
			
				price = parseFloat('. $quality[1].');
				circleCommission = parseFloat('.$conf->circleFee.');
			
		}
		var maxWords = jQuery("#"+formId+" select[name=\'guideline\'] option:selected").attr("title");
		price = parseInt(maxWords)*parseFloat(price);
		//price += (parseFloat(price) * parseFloat(taxPercent)) / 100;
		price = parseFloat(price) + parseFloat(commission) + parseFloat(circleCommission);

		jQuery(".featureOrdersNumber").each(function(){
			price += parseFloat(jQuery("#featureCosts").html());
		});

        proofReadCost=getproofReadingCost(\'createOrderForm\');
        price+=parseFloat(proofReadCost);
  
		price = Math.round((price*100)+0.4) / 100;

		var circle = 0;
		if(jQuery("#circleArticles").is(\':checked\')){
			circle = 1;
		}
		
		var title = "";
		for(var i=0; i < titles.length; i++){
			title = "";
			title = jQuery.trim(titles[i]);
			if(title != ""){
				title = "<div class=\'shortText\'>"+title+"</div>";
				jQuery(\'#\'+tableName).dataTable().fnAddData([
				                                     projectId,
				                                     guidelineId,
				                                     qualityId,
				                                     description,
				                                     deadline,
				                                     circle,
                                                     
				                                     jQuery(\'.feature\').length,
                                                     
				                                     title,
				                                     price+" '.$conf->valute.'",
                                                     specialCircle,
													 programmingDate,
				                                     removeBtn]);
			}
        }
		
		jQuery("#"+formId+" textarea[name=\'titles\']").val("");
		jQuery("#oldTotalValue").html(jQuery("#totalOrdersCost").html());
	}
	
	
	function getproofReadingCost(formId){
        var quality = 1;
		if(jQuery("#"+formId+" select[name=\'quality\']").is(\':visible\')){
			quality = jQuery("#"+formId+" select[name=\'quality\']").val();
            if(quality=="")
                quality=0;
		}else
            quality = 1;
        
        
        var str= jQuery("#mela_project").val();
		obj=jQuery.parseJSON(str); 
//alert(obj);		
        return parseFloat(obj.proofReading[quality]);
             
       

    }
	
	function proofReadingCost(formId){
        var quality = 1;
		if(jQuery("#"+formId+" select[name=\'quality\']").is(\':visible\')){
			quality = jQuery("#"+formId+" select[name=\'quality\']").val();
            if(quality=="")
                quality=0;
		}else
            quality = 1;
        
		var totalOrders = 0;
		var totalPrice=0;
		var qualityPrice=0;
        //var str= jQuery("#mela_project").val();
		//obj=jQuery.parseJSON(str);   
     
                    
            		for(var i=0; i < jQuery(\'#articlesTable\').dataTable().fnGetNodes().length; i++) {
            			var row = jQuery(\'#articlesTable\').dataTable().fnGetData(i);
						
						var str= jQuery("#mela_project option[name="+row[0]+"]").val();
						obj=jQuery.parseJSON(str);  
						
						if(Object.keys(obj.proofReading).length >0){
            				totalOrders++;
							//totalPrice=parseFloat(totalPrice)+parseFloat(obj.proofReading[row[2]]);
						}
            		}
            		var vals = jQuery.trim(jQuery("#createOrderForm textarea[name=\'titles\']").val());
            		if(vals != ""){
						var str= jQuery("#mela_project").val();
						obj=jQuery.parseJSON(str);  
						if(Object.keys(obj.proofReading).length >0){
							var r = vals.split(/[\r\n]/g);
							qualityPrice=obj.proofReading[quality];
							for(var i = 0; i < r.length; i++){
								if(jQuery.trim(r[i]) != ""){
									totalOrders++;
									totalPrice=parseFloat(totalPrice)+parseFloat(obj.proofReading[quality]);
									
								}
							}
						}
            		} 
                      
                    jQuery("#proofReadeTr").show();
                    jQuery("#proofReadCost").html(\'<div style="float:left;width:72%;font-weight:bold;" class="ProofRead">\'+totalOrders+\'</div> x <span>\'+qualityPrice+\'</span> '.$conf->valute.'\');
                    return parseFloat(totalPrice);
                
                    if (totalOrders>0){
                        jQuery("#proofReadeTr").show();
                        jQuery("#proofReadCost").html(\'<div style="float:left;width:72%;font-weight:bold;" class="ProofRead">\'+totalOrders+\'</div> x <span>\'+qualityPrice+\'</span>'.$conf->valute.'\');
                        return parseFloat(totalPrice);
                        
                    }else{
                         jQuery("#proofReadeTr").hide();
                         jQuery("#proofReadCost").html(\'<div style="float:left;width:72%;font-weight:bold;" class="ProofRead">\'+totalOrders+\'</div> x <span>\'+qualityPrice+\'</span>'.$conf->valute.'\');
                         return 0;
                    }
                    
                

    }
    
 
	
	
	
	
	
	
	
	
	
	function removeOrderFromTable(elem){
		var price = (jQuery(elem).parents("TR").children("td:eq(1)")).html();
		price = price.split(" ")[0];
		price = parseFloat(jQuery("#totalOrdersCost").html()) - parseFloat(price);
		price = Math.round((price*100)+0.4) / 100;
		jQuery(elem).parent().parent().fadeOut("slow", function () {
			var pos = jQuery("#articlesTable").dataTable().fnGetPosition(this);
			jQuery("#articlesTable").dataTable().fnDeleteRow(pos);
			jQuery("#articlesTable").dataTable().fnDraw();
			jQuery("#totalOrdersCost").html(price);
			jQuery("#oldTotalValue").html(price);
			updateOrdersInfo();
			
			
		});
		
	}
	
	function emptyTableData(){
		jQuery("#ordersNumber").html("0");
		jQuery("#totalOrdersCost").html("0");
		jQuery("#commisionsNumber").html("0");
		jQuery(".featureOrdersNumber").each(function(){
			jQuery(this).html("0");
		});
        jQuery("#circleNumber").html("0");
		jQuery("#oldTotalValue").html("0");
		jQuery("#articlesTable").dataTable().fnClearTable();
		updateOrdersInfo();
	}
		</script>
		
		<section id="createOrderTab" class="clearfix" style="min-height:304px;  min-width:1080px;">
		<table style="width:98%">
		<tr><td valign="top">
		
	        	<div class="left-box" style=" min-width:510px;margin:10px;">
	        		<!-- BOX FORM CREAZIONE ORDINE -->
					<section class="portlet grid_6 leading" style="width:100%;"> 
			        	<header>
			            	<h2>'.__("create orders",$this->getName()).'</h2> 
			            </header>
						<div class="section" style="padding-top:10px;padding-bottom:13px;min-height:483px;">
			        		<form action="#" class="validate" id="createOrderForm" style="margin-left:2%;">
								<table style="width: 100%">
                               
									<input type=\'hidden\' value=\'\' id=\'superCircles\' name=\'superCircles\'/>            
									<tr>
										<td>
				                       		<label> 
				                            	'.__("project",$this->getName()).'
				                           	</label>
				                        </td>
										<td>
                                            <select required class="melaSelect" id="mela_project" name="project" style="width:300px;" onchange="changeProject();">
                                        
												<option value=\'{"id":"0","featureCost":"","circleUsers":""}\' style=": #6D6D6D;">'.__("select project",$this->getName()).'</option>	
												'.$this->printProjectOption($userId).'
                                               
											</select>
										</td>
									</tr>
									<tr>
										<td>
				                       		<label> 
				                            	'.__("guideline",$this->getName()).'
				                           	</label>
				                        </td>
										<td>
											<select required class="melaSelect" style=" width:300px;" id="mela_guideline" name="guideline"  onchange="changeGuideline();updateOrdersInfo();/*autocompleteDescription(this);*/">
												<option title="0" value="" style="color: #6D6D6D;">'.__("select guideline",$this->getName()).'</option>	
												'.
												$this->printGuidelineOption($userId)
												.'
											</select>
											
										</td>
									</tr>
									
									<tr >
										<td><label></label></td>
										<td>
											<div style="float:left;width:270px;">
												<input id="programmingArticles" style="float:none; width:10px;" type="checkbox" value="0"  onchange="showHideProgramming(this)"/><b>'.__("set publication date",$this->getName()).'</b>
											</div>
											
										
										</td>
									</tr>
									<tr id="programmingTr" style="display:none;">
                                        <td></td>
                                        <td><input type="text" id="datepickerProgramming" name="datepickerProgramming" onchange="setDeadline();" placeholder="gg/mm/aaaa" style="color:#4F4F4F;font-size:13px;width:220px;float:left; margin-right:10px;"/>
											<select required class="" id="datepickerProgrammingTime" name="datepickerProgrammingTime" style="">';
												
												for($i = 0; $i < 24; $i+=1){
													$echo.= "<option val=\"".sprintf("%02s",$i).":00\">".sprintf("%02s",$i).":00</option>";
													//$i++;
												}
												
											$echo.='</select></td>
                                    </tr>
									
									<tr id="circleTr">
										<td><label></label></td>
										<td>
											<div style="float:left;width:270px;">
												<input id="circleArticles" style="float:none; width:10px;" type="checkbox" value="1" disabled="disabled" onchange="showHideCircle()">
												
												<b>'.__("use circle for this project",$this->getName()).'</b>
												
											</div>
											<div id="numAuthorsProject" style="float:left;font-weight:bold;"> (0 autori)</div>
										</td>
									</tr>
									<tr id="qualityTr" style="display:none;">
										<td>
				                       		<label> 
				                            	'.__("quality",$this->getName()).':
				                           	</label>
				                        </td>
										<td>
											<select required class="" id="mela_quality" name="quality"  onchange="updateOrdersInfo();">
												<option title="0" value="" style="color: #6D6D6D;">'.__("select quality",$this->getName()).'</option>	
												'.
												$this->printQualityOption()
												.'
											</select>
										</td>
									</tr>
									<tr id="deadlineTr" style="display:none;">
										<td>
				                       		<label> 
				                            	'.__("deadline",$this->getName()).':
				                           	</label>
				                        </td>
										<td>
											<input type="text" id="datepickerDeadline" name="datepickerDeadline" placeholder="gg/mm/aaaa" style="color:#4F4F4F;font-size:13px;width:220px;float:left; margin-right:10px;"/>
											&nbsp;<select required class="" id="deadlineTime" name="deadlineTime" style="">';
												 
												for($i = 0; $i < 24; $i++){
													$echo.="<option val=\"".sprintf("%02s",$i).":00\">".sprintf("%02s",$i).":00</option>";
													$echo.="<option val=\"".sprintf("%02s",$i).":30\">".sprintf("%02s",$i).":30</option>";
													
												}
												
											$echo .='</select>
										</td>
									</tr>
									<tr>
										<td style="width:16%;padding-top:10px;">
				                       		<label> 
				                            	'.__("title",$this->getName()).':
				                           	</label>
				                        </td>
										<td style="line-height:0px;">
											<textarea onKeyUp="updateOrdersInfo();" id="textAreaTitles" name="titles" class="lined" style="height:154px;"></textarea>
										</td>
									</tr>
									<tr>
										<td>
				                       		<label> 
				                            	'.__("description",$this->getName()).':
				                           	</label>
				                        </td>
										<td style="line-height:45px;"><textarea id="mela_description" name="description"></textarea></td>
									</tr>
								</table>
							</form>
							<footer class="clearfix" style="margin-right:7%;margin-top:5px;">
								<button class="mela" id="addArticlesBtn" title="'.__("add articles",$this->getName()).'" onclick="addOrdersToTable(\'createOrderForm\',\'articlesTable\');" style="background: url(\''.plugins_url().'/melascrivi/images/freccia.png\') no-repeat scroll center right transparent;height:50px;">'.__("add articles",$this->getName()).'</button>
			                </footer>
			    		</div>
			    	</section>
			    </div>
			</td>';
				
			    $echo.='
			<td valign="top" style="width:40%;" ><!-- BOX DETTAGLI ORDINE -->
				<style>
				.ar{
					text-align: right;
				}
				</style>
				<div class="right-box" style="margin:10px;/*width:45%;*/">
					<section class="portlet grid_6 leading"> 
			        	<header>
			            	<h2>'.__("order details", $this->getName()).'</h2> 
			            </header>
			            <div class="section" style="padding:10px 15px;">
			            	<div id="oldTotalValue" style="display:none ;">0</div>
							<table class="full"> 
								<tbody> 
									<tr> 
										<td>'.__("orders number", $this->getName()).':</td> 
										<td class="ar" id="ordersNumber" style="text-align: right;">0</td> 
									</tr>
									
									<tr> 
										<td>'.__("commission price", $this->getName()).':</td> 
										<td class="ar"><div style="float:left;width:72%; text-align: right;" id="commisionsNumber">0</div> x '.$conf->commission." ".$conf->valute.'</td> 
									</tr>
									<tr id="circleCommissionTr" style="display:none;"> 
										<td style="padding-bottom:10px;"><b>'.__("circle price", $this->getName()).':</b></td> 
										<td class="ar" style="padding-bottom:10px;"><div style="float:left;width:72%;font-weight:bold; text-align: right;" id="circleNumber">0</div> <b>x '.$conf->circleFee." ".$conf->valute.'</b></td> 
									</tr>
									<tr id="proofReadeTr" style="display:none;"> 
										<td style="padding-bottom:10px;"><b>'.__("proof reading Cost",$this->getName()).':</b></td> 
										<td id="proofReadCost" class="ar" style="padding-bottom:10px;"><div style="float:left;width:72%;font-weight:bold;" >0</div> <b>x 0.00 '.$conf->valute.'</b></td> 
									</tr>
									<tr id="featureCommissionTr" style="display:;"> 
										<td style="padding-bottom:10px;"><b>'.__("feature prices", $this->getName()).':</b></td> 
										<td class="ar" style="padding-bottom:10px;"> <div style="float:left;width:72%;font-weight:bold; text-align: right;" class="featureOrdersNumber" id="featureNumber">0</div><b>x <span id="featureCosts">0</span> '.$conf->valute.'</b></td> 
									</tr>
									<tr><td colspan="2">
									<hr>
									</td></tr>
									<tr style="border-top:1px solid;"> 
										<td style="font-weight:bold;width:165px;">'.__("total price", $this->getName()).'*:</td> 
										<td style="font-weight:bold;" class="ar"><div style="float: left; width:83%; text-align: right;" id="totalOrdersCost">0</div> '.$conf->valute.'</td> 
									</tr>
									<tr> 
										<td style="font-size:9px;" colspan="2">* '.__("in case that author reach max words number", $this->getName()).'</td> 
									</tr>
								</tbody> 
							</table>
						</div>
					 </section>
					 <div style="clear:both"></div>
					 
					 <section class="portlet grid_6 leading" style="min-width:445px;"> 
			        	<header>
			            	<h2>'.__("order list",$this->getName()).'</h2> 
			            </header>
						<div class="section" style="padding:15px;max-height:285px; position:relative;  ">
							<table class="display" id="articlesTable" style="width:446px;"> 
			             		<thead> 
			           				<tr style="height:20px;"> 
			                    		<th style="width:50px;/*width:0px; display:none; */">0 projectId</th>
			                          	<th style="width:50px;/*width:0px; display:none; */">1 guidelineId</th>
			                          	<th style="width:50px;/*width:0px; display:none;*/">2 qualityId</th>
			                          	<th style="width:50px;/*width:0px; display:none;*/">3 description</th>
			                          	<th style="width:50px;/*width:0px; display:none;*/">4 deadline</th>
			                          	<th style="width:50px;/*width:0px; display:none;*/">5 circle</th>
                                        <th style="width:50px;/*width:0px; display:none;*/">6 features</th>
			                          	<th style="width:50px;">'.__("title",$this->getName()).'</th>
			                          	<th style="width:50px;">'.__("price",$this->getName()).'</th>
                                        <th style="width:50px;/*width:0px; display:none;*/">9 specials</th>
										<th style="width:50px;/*width:0px; display:none;*/">10 programming</th>
			                          	<th style="width:30px;">'.__("remove",$this->getName()).'</th>
			                       	</tr> 
			                   	</thead> 
			               		<tbody></tbody> 
			              	</table>
			              	<div id="emptyTableBtn" title="'.__("empty orders list",$this->getName()).'" onclick="emptyTableData();">&nbsp;</div>
						</div>
					 </section> 
				<button id="sendOrdersBtn" type="button" class="mela" style=" height:50px; width:100%; font-size: 16px; font-weight: bold;" title="'.__("send orders",$this->getName()).'" onclick="sendActionRequest(\'createOrders\');">'.__("send orders",$this->getName()).'</button>
				</div>
        	</td>
		</tr>
	</table>			
 </section>'.$this->messageBox();
			
		return $echo;
	
	
	}
		
	function messageBox(){
		
		$echo ='
		
		<script>
			function viewMessageBox(msgBoxId,title,text,expTime){
				jQuery("#"+msgBoxId).notify();
				jQuery("#"+msgBoxId).notify("create","default-container", {
					title: title,
					text: text
				},{
					speed: 400,
					expires: expTime
				});
			}
		</script>
		<style>
			.ui-notify {
				top: 140px;
				width: 600px;
				text-align: center;
				z-index: 1000;
			}
			#genericErrorMsgBox .ui-notify-message-style {
				background: url("'.plugins_url().'/melascrivi/images/errorBox-bg.png") repeat-x center transparent;
				border: 2px solid #EFBABD;
			}
			#genericErrorMsgBox .ui-notify-message-style h1, #genericErrorMsgBox .ui-notify-message-style p {
				color: #C10005;		font-size: 16px;
				line-height: 19px;
			}
			#genericSuccessMsgBox .ui-notify-message-style {
				background: url("'.plugins_url().'/melascrivi/images/successBox-bg.png") repeat-x center transparent;
				border: 2px solid #D7E1B0;
			}
			#genericSuccessMsgBox .ui-notify-message-style h1, #genericSuccessMsgBox .ui-notify-message-style p {
				color: #39420D;
				font-size: 16px;
				line-height: 19px;
			}
		</style>
		<div id="genericErrorMsgBox" style="display:none;">
			<div id="default-container">
				<h1>#{title}</h1>
				<p>#{text}</p>
			</div>
		</div>
		<div id="genericSuccessMsgBox" style="display:none;">
			<div id="default-container">
				<h1>#{title}</h1>
				<p>#{text}</p>
				<button class="mela" id="okBtn" onclick="jQuery(\'#genericSuccessMsgBox\').hide();" style="display:none;">OK</button>
				<button class="mela" id="yesBtn" onclick="jQuery(\'#genericSuccessMsgBox\').hide();" style="display:none;">SI</button>
				<button class="mela" id="noBtn" onclick="jQuery(\'#genericSuccessMsgBox\').hide();" style="display:none;">NO</button>
			</div>
		</div>';
		return $echo;
	
	}
	
	
	function acceptOrder($orderId,$style,$guideline,$grammar){
		$rating=new stdClass();
		$rating->general=$style;
		$rating->guideline=$guideline;
		$rating->grammar=$grammar;

		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->acceptOrder($orderId,$rating,null,time());
		return $response;
	}
	
	function correctOrder($orderId,$note){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->correctOrder($orderId,$note,time());
		return $response;
	}
	
	function rejectOrder($orderId,$motivation,$authorId){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->rejectOrder($orderId,$motivation,$authorId,time());
		return $response;
	}
	
	function saveArticleInDraft($orderId,$userId){
		$order=new stdClass();
		$order->id=$orderId;
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->getOrders($userId,$order);
			// Create post object
		$my_post = array(
		  'post_title'    => $response->order->title,
		  'post_content'  => $response->order->text,
		  'post_status'   => 'draft',
		  'post_author'   => 1
		  /*,
		  'post_category' => array(8,39)*/
		);

		// Insert the post into the database
		$return=wp_insert_post( $my_post );
		try{
			$this->client->exportOrderOnWp($orderId,$userId);
		}catch(Exception $e){
		}
		return $return;
	}
	
	function printWaitGif($id=""){
	$echo ='<div id="loading-container'.$id.'" style="display:none;">
				<p id="loading-content'.$id.'" style="text-align:center;padding-top: 50px;">
					<img id="loading-graphic'.$id.'" width="40" height="45" src="'.plugins_url().'/melascrivi/images/mela.gif">
					<br> 
					'.__("loading",$this->getName()).'...
				</p>
			</div>';
		return $echo;	
	}
	
	function printSortingStyle(){
		$echo='
		<style>
			.sorting { 
				background: url("'. plugins_url().'/melascrivi/images/sort_both.png") no-repeat center right; 
			}
			.sorting_asc { background: url("'. plugins_url().'/melascrivi/images/sort_asc.png") no-repeat center right; }
			.sorting_desc { 
				background: url("'. plugins_url().'/melascrivi/images/sort_desc.png") no-repeat center right; 
			}

			.sorting_asc_disabled { background: url("'. plugins_url().'/melascrivi/images/sort_asc_disabled.png") no-repeat center right; }
			.sorting_desc_disabled { background: url("'. plugins_url().'/melascrivi/images/sort_desc_disabled.png") no-repeat center right; }
		</style>';
		return $echo;
	}
	
	function printCreateProject(){
		$echo="<div style='padding:10px;'>
			<label>".__("project name",$this->getName())." <input id='melaProject' type='text'/> </label>
			<button class='mela' onclick='createMelaProject()'>".__("add",$this->getName())."</button>
		</div>";
		return $echo;
	}
	
	
	function getProject($userId,$project=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null){
		$response=$this->client->getProjects($userId,$project,$orderColumn,$orderType,$limitStart,$limitSize);
		
		return $response;	
	}
	

	function printProjectOption($userId){
		$result=$this->getProject($userId,null,'name','asc',0,10000);
		
		if (count($result->projects)==1){
			$project=$result->projects;
			$str="";
			if ($project->circleUsers>0)
				$str=" (".__("with circle", $this->getName()).")";
			$cost=0;
			if (count($project->features)>1){
				foreach($project->features as $feature){
					$cost+=floatval($feature->price);				
				}
				if ($cost>0)
					$str.=" (".__("with features", $this->getName()).")";
			}else if(count($project->features)==1){
				$cost+=floatval($project->features->price);				
				if ($cost>0)
					$str.=" (".__("with features", $this->getName()).")";
			}
			$prArray=array();
			if(count($project->proofReading)>0){
				
				foreach($project->proofReading as $pr){
					$prArray[$pr->level]=$pr->price;
				}
			}else if (count($project->proofReading)==1){
				$prArray[$project->proofReading->level]=$project->proofReading->price;
			}
			$value='{"id":"'.$project->id.'","featureCost":"'.$cost.'","circleUsers":"'.$project->circleUsers.'","proofReading":"'.json_encode($prArray).'"}';
			$echo.="<option value='".$value."'>".$project->name.$str."</option>\n";
		}else if (count($result->projects)>1){
			foreach($result->projects as $project){
				$str="";
				if ($project->circleUsers>0)
					$str=" (".__("with circle", $this->getName()).")";
				$cost=0;
				
				if (count($project->features)>1){
					foreach($project->features as $feature){
						$cost+=floatval($feature->price);				
					}
					if ($cost>0)
						$str.=" (".__("with features", $this->getName()).")";
				}else if(count($project->features)==1){
					$cost+=floatval($project->features->price);				
					if ($cost>0)
						$str.=" (".__("with features", $this->getName()).")";
				}
				$prArray=array();
				if(count($project->proofReading)>0){
					
					foreach($project->proofReading as $pr){
						$prArray[$pr->level]=$pr->price;
					}
				}else if (count($project->proofReading)==1){
					$prArray[$project->proofReading->level]=$project->proofReading->price;
				}
				$obj=new stdClass();
				$obj->id=$project->id;
				$obj->featureCost=$cost;
				$obj->circleUsers=$project->circleUsers;
				$obj->proofReading=$prArray;
				$value=json_encode($obj);//'{"id":"'.$project->id.'","featureCost":"'.$cost.'","circleUsers":"'.$project->circleUsers.'","proofReading":"'.json_encode($prArray).'"}';
				$echo.="<option name='$project->id' value='".$value."'>".$project->name.$str."</option>\n";
			}
		}
		return $echo;
	}
		
	
	
	function createProject($userId,$projectName){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$project=new stdClass();
		$project->name=$projectName;
		$response=$this->client->createProject($project,$userId);
		return $response;
	}
	
	function printProjects($userId,$project=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null,$hideButton=false){
		
		$result=$this->getProject($userId,$project,$orderColumn,$orderType,$limitStart,$limitSize);
		$echo =$this->printSortingStyle()."
		<table style='width:100%;'>
			<thead>
			<tr>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"name\");'")."  style='width:25%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px; 'class ='".$this->returnSortingClass("name",$orderColumn,$orderType,$noSorting)."'>".__("project name",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"date\");'")." style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("date",$orderColumn,$orderType,$noSorting)."'>".__("date",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"orders_total\");'")." class ='".$this->returnSortingClass("orders_total",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("total orders",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"orders_published\");'")." class ='".$this->returnSortingClass("orders_published",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("published orders",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"orders_waiting_for_review\");'")." class ='".$this->returnSortingClass("orders_waiting_for_review",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("waiting for review",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"orders_author_is_writing\");'")." class ='".$this->returnSortingClass("orders_author_is_writing",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("writing",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"orders_accepted\");'")." class ='".$this->returnSortingClass("orders_accepted",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("accepted",$this->getName())."</th>
			</tr>
			</thead>
			";
			
		if (count($result->projects)==1){
			$project=$result->projects;
			$echo.="<tr>";
			$echo.="<td style='text-align:center;'>".$project->name."</td>";
			$echo.="<td style='text-align:center;'>".$project->date."</td>";
			$echo.="<td style='text-align:center;' >".$project->orders->total."</td>";
			$echo.="<td style='text-align:center;' >".$project->orders->published."</td>";
			$echo.="<td style='text-align:center;' >".$project->orders->waitingReview."</td>";
			$echo.="<td style='text-align:center;' >".$project->orders->writing."</td>";
			$echo.="<td style='text-align:center;' >".$project->orders->accepted."</td>";
			
			$echo.="</tr>";
		
		}else if (count($result->projects)>1){
			foreach($result->projects as $project){
				$echo.="<tr>";
				$echo.="<td style='text-align:center;'>".$project->name."</td>";
				$echo.="<td style='text-align:center;'>".$project->date."</td>";
				$echo.="<td style='text-align:center;' >".$project->orders->total."</td>";
				$echo.="<td style='text-align:center;' >".$project->orders->published."</td>";
				$echo.="<td style='text-align:center;' >".$project->orders->waitingReview."</td>";
				$echo.="<td style='text-align:center;' >".$project->orders->writing."</td>";
				$echo.="<td style='text-align:center;' >".$project->orders->accepted."</td>";
				$echo.="</tr>";
			}
		}else{
			$echo.="<tr>
				<td colspan='7' style='text-align:center;'>
					".__("no record found",$this->getName())."
				</td>
			</tr>";
		
		}
		$echo .="</table>";
	
		if($hideButton==true){
			
			$echo .="<div style='padding:20px; color:grey;'>".__("total",$this->getName()).":".$result->displayed;
			if (intval($result->displayed)<intval($result->totals))
				$echo .=" (".__("filtered from",$this->getName())." " .$result->totals." ".__("total result",$this->getName()).")" ;
			$echo .="</div>";
		
			$echo.="<script>";
			if ((intval($limitStart)+intval($limitSize))<intval($result->displayed)){
				$echo.="jQuery('#mela_avanti').show();";
			}else{
				$echo.="jQuery('#mela_avanti').hide();";
			}
			
			if (intval($limitStart)>=intval($limitSize)){
				$echo.="jQuery('#mela_indietro').show();";
			}else{
				$echo.="jQuery('#mela_indietro').hide();";
			}
			$echo.="</script>";
		}
		return $echo;	
	}
	function getCategories(){
		$response=$this->client->getCategories();
		return $response;
	}
	
	function printCategoriesOption($selected=null){
		$result=$this->getCategories();
		$echo="";
		foreach($result->category as $a){
			if ($a->visible==1){
				if ($selected==$a->id)
					$echo.="<option value='$a->id' selected>$a->name</option>";
				else
					$echo.="<option value='$a->id'>$a->name</option>";
			}
		}
		return $echo;
	}
	
	function getStyles(){
		$response=$this->client->getStyles();
		return $response;
	}
	
	function printStyleOption($selected=null){
		$result=$this->getStyles();
		$echo="";
		foreach($result->style as $a){
			if ($selected==$a->id)
				$echo.="<option value='$a->id' selected>$a->name</option>";
			else
				$echo.="<option value='$a->id'>$a->name</option>";
		}
		return $echo;
	}
	
	function printCreateGuideline($guideline=null){
		if ($guideline!=null){
			$categoryId=$guideline->category->id;
			$styleId=$guideline->style->id;
			$minWords=$guideline->minWords;
			$maxWords=$guideline->maxWords;
			$description=$guideline->description;	
			$id=$guideline->id;
			
			$disabled=" disabled='disabled' ";
			$disableBGcolor=" background-color: #eee; ";
			$edit="Edit";
			
		}else{
			$categoryId=null;
			$styleId=null;
			$minWords=null;
			$maxWords=null;
			$description=null;
			$id="";
			$disabled="";
			$disableBGcolor="";
			$edit="";
			
		}
		
		$echo ='
		<div style="padding:10px;"> 
			<table style="width: 100%">
				<tbody><tr>
					<td>
						<label> 
							'.__("category",$this->getName()).':
						</label>
					</td>
					<td>
						<select id="category'.$edit.'" required class="melaSelect" name="category" >
							<option value="" style="color: #6D6D6D;">
							'.__("choose a category",$this->getName()).'
							</option>	
							' 
							.$this->printCategoriesOption($categoryId).'
							
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label> 
							'.__("style",$this->getName()).':
						</label>
					</td>
					<td>
						
						<select id="style'.$edit.'" required="" class="melaSelect" name="style">
							<option value="" style="color: #6D6D6D;">'.__("choose a style",$this->getName()).'</option>	
							'.$this->printStyleOption($styleId).'
						</select>
					</td>
				</tr>
				<tr>
					<td style="width: 16%;">
						<label> 
							'.__("min words",$this->getName()).'
						</label>
					</td>
					<td>
						<input '.$disabled.' type="text" id="minWords'.$edit.'" name="minWords" value="'.$minWords.'" style="width:65%; '.$disableBGcolor.'"/>
						<span id="errMsg_minWords" style="color:red;margin-left:10px;"></span>
					</td>
				</tr>
				<tr>
					<td style="width: 16%;">
						<label> 
							'.__("max words",$this->getName()).'
						</label>
					</td>
					<td>
						<input '.$disabled.' type="text" id="maxWords'.$edit.'" name="maxWords" value="'.$maxWords.'" style="width:65%; '.$disableBGcolor.'"/>
						<span id="errMsg_maxWords" style="color:red;margin-left:10px;"></span>
					</td>
				</tr>
				<tr>
					<td style="width: 16%;">
						<label> 
							'.__("description",$this->getName()).'
						</label>
					</td>
					<td style="line-height: 0px;">
						<textarea id="description'.$edit.'" name="description" style="width:65%;">'.$description.'</textarea>
					</td>
				</tr>
				<tr>
					<td style="line-height: 7px;">&nbsp;</td>
					<td style="line-height: 22px;">
						<em>'.__("this description will be used as template for order description",$this->getName()).'</em>
					</td>
				</tr>
				
				
			</tbody>
				<tfoot>
					<tr><th colspan="2">
						<div style="padding:10px;">
							<button class="mela" onclick="saveGuideline('.$id.')">'.__("save",$this->getName()).'</button>
						</div>
					</th></tr>
				</tfoot>
			</table>
		</div>
		
		';
		
		return $echo;
	}
	
	function createGuideline($userId,$categoryId,$styleId,$minWords,$maxWords,$description){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$guideline=new stdClass();
		$guideline->category->id=$categoryId;
		$guideline->style->id=$styleId;
		$guideline->minWords=$minWords;
		$guideline->maxWords=$maxWords;
		$guideline->description=$description;
		
		$response=$this->client->createGuideline($guideline,$userId);
		return $response;
	}
	
	function modifyGuideline($userId,$guidelineId,$categoryId,$styleId,$minWords,$maxWords,$description){
	
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$guideline=new stdClass();
		$guideline->id=$guidelineId;
		$guideline->category->id=$categoryId;
		$guideline->style->id=$styleId;
		$guideline->minWords=$minWords;
		$guideline->maxWords=$maxWords;
		$guideline->description=$description;
		
		$response=$this->client->modifyGuideline($guideline,$userId);
		return $response;
	}
	
	
	function getGuidelines($userId,$guideline=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null){
		$response=$this->client->getGuidelines($userId,$guideline,$orderColumn,$orderType,$limitStart,$limitSize);
		return $response;	
	}
	
	function printGuidelineOption($userId){
		$result=$this->getGuidelines($userId,null,'category','asc',0,10000);
		if (count($result->guidelines)==1){
			$guidelines=$result->guidelines;
			$desc=(strlen($guidelines->description)>15?substr($guidelines->description,0,13)."...":$guidelines->description);
			$echo.="<option data-test='".addslashes($guidelines->description)."' title='".$guidelines->maxWords."' value='".$guidelines->id."'>".$guidelines->category->name.", ".$guidelines->style->name.", ".$guidelines->minWords."/".$guidelines->maxWords." (".$desc.")</option>\n";
		}else if (count($result->guidelines)>1){
			foreach($result->guidelines as $guidelines){
				$desc=(strlen($guidelines->description)>15?substr($guidelines->description,0,13)."...":$guidelines->description);
				$echo.="<option data-test=\"".addslashes($guidelines->description)."\" title='".$guidelines->maxWords."'  value='".$guidelines->id."'>".$guidelines->category->name.", ".$guidelines->style->name.", ".$guidelines->minWords."/".$guidelines->maxWords." (".$desc.")</option>\n";
			}
		}
		return $echo;
	}
	
	function printGuidelines($userId,$guideline=null,$orderColumn=null,$orderType=null,$limitStart=null,$limitSize=null,$hideButton=false){
		$result=$this->getGuidelines($userId,$guideline,$orderColumn,$orderType,$limitStart,$limitSize);
		$echo =$this->printSortingStyle()."

		<style>
			.modifyGuidelineBtn{
				background: url('".plugins_url()."/melascrivi/images/navicons-small/165.png') no-repeat center transparent;
				cursor:pointer;
			}
		</style>
		<table style='width:100%;'>
			<tr>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"category\");'")."  style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px; 'class ='".$this->returnSortingClass("category",$orderColumn,$orderType,$noSorting)."'>".__("category",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"style\");'")." style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;' class ='".$this->returnSortingClass("style",$orderColumn,$orderType,$noSorting)."'>".__("style",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"minWords\");'")." class ='".$this->returnSortingClass("minWords",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("words",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"description\");'")." class ='".$this->returnSortingClass("description",$orderColumn,$orderType,$noSorting)."' style='border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("description",$this->getName())."</th>
				<th ".($noSorting?"":"onclick='changeMelaSorting(\"date\");'")." class ='".$this->returnSortingClass("date",$orderColumn,$orderType,$noSorting)."' style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("date",$this->getName())."</th>
				<th  style='width:10%; border-bottom: 1px solid #CCC; cursor: pointer; font-weight: bold; padding: 3px 18px 3px 10px;'>".__("action",$this->getName())."</th>
				
			</tr>";
		if (count($result->guidelines)==1){
			$guideline=$result->guidelines;
			$echo.="<tr>";
			$echo.="<td style='text-align:center;'>".$guideline->category->name."</td>";
			$echo.="<td style='text-align:center;'>".$guideline->style->name."</td>";
			$echo.="<td style='text-align:center;' >".$guideline->minWords."/".$guideline->maxWords."</td>";
			$echo.="<td style='text-align:center;' >".$guideline->description."</td>";
			$echo.="<td style='text-align:center;' >".$guideline->date."</td>";
			$echo.="<td style='text-align:center;' >
				<div class='modifyGuidelineBtn'  onclick='clickModifyGuideline(".$guideline->id.");'>&nbsp;</div>
			</td>";

			$echo.="</tr>";
		
		}else if(count($result->guidelines)>1){
			foreach($result->guidelines as $guideline){
				$echo.="<tr>";
				$echo.="<td style='text-align:center;'>".$guideline->category->name."</td>";
				$echo.="<td style='text-align:center;'>".$guideline->style->name."</td>";
				$echo.="<td style='text-align:center;' >".$guideline->minWords."/".$guideline->maxWords."</td>";
				$echo.="<td style='text-align:center;' >".$guideline->description."</td>";
				$echo.="<td style='text-align:center;' >".$guideline->date."</td>";
				$echo.="<td style='text-align:center;' >
					<div class='modifyGuidelineBtn'  onclick='clickModifyGuideline(".$guideline->id.");'>&nbsp;</div>
				</td>";
			}
		}else{
			$echo.="<tr>
				<td colspan='6' style='text-align:center;'>
					".__("no record found",$this->getName())."
				</td>
			</tr>";
		
		}
		$echo .="</table>";
		if($hideButton==true){
			
			$echo .="<div style='padding:20px; color:grey;'>".__("total",$this->getName()).":".$result->displayed;
			if (intval($result->displayed)<intval($result->totals))
				$echo .=" (".__("filtered from",$this->getName())." " .$result->totals." ".__("total result",$this->getName()).")" ;
			$echo .="</div>";
		
			$echo.="<script>";
			if ((intval($limitStart)+intval($limitSize))<intval($result->displayed)){
				$echo.="jQuery('#mela_avanti').show();";
			}else{
				$echo.="jQuery('#mela_avanti').hide();";
			}
			
			if (intval($limitStart)>=intval($limitSize)){
				$echo.="jQuery('#mela_indietro').show();";
			}else{
				$echo.="jQuery('#mela_indietro').hide();";
			}
			$echo.="</script>";
		}
		
		return $echo;	
	}
	
	function getQuality(){
		$response=$this->client->getQuality();
		return $response;	
	}
	
	function printQualityOption(){
		$result=$this->getQuality();
		if (count($result->quality)==1){
			$quality=$result->quality;
			$echo.="<option value='".$quality->id."'>".$quality->name." (".$quality->price." ".__("euro/word",$this->getName()).")</option>\n";
		}else{
			foreach($result->quality as $quality){
				$echo.="<option value='".$quality->id."'>".$quality->name." (".$quality->price." ".__("euro/word",$this->getName()).")</option>\n";
			}
		}
		return $echo;
	}
	
	function getConfiguration($userId){
		$response=$this->client->getConfigurationData($userId);
		return $response;	
	}
	
	function createNewUser($email, $psw,$repsw,$returnAddress){
		$response=$this->client->createUser($email,$psw,$repsw,$returnAddress);
		return $response;	
	}
	
	function activateUSer($actKey){
		$response=$this->client->activateUser($actKey);
		return $response;	
	}
	
	function printPaypalRecharge($userId){
		$conf=$this->getConfiguration($userId);
		$current_url=$_SERVER['REQUEST_URI'];
		$tmp=explode("?",$current_url);
		$current_url=$tmp[0];
		$echo='<script>
		function submitPaypal(){
			var oldInput = jQuery("input[name=\'custom\']").val().split(";");
				jQuery("input[name=\'custom\']").val(oldInput[0]);
				iva=((((jQuery("#paypalForm #amountPayment").val())*'.$conf->iva.')/100)).toFixed(2);
				
				jQuery("input[name=\'amount\']").val( parseFloat(jQuery("#paypalForm #amountPayment").val())+parseFloat(iva));
				
				jQuery("#paypalForm").submit();
				return true;
		}
		
		jQuery(document).ready(function(){
			jQuery("#amountPayment").customSelect();
		});
		
		</script> 
		<style>
		
			span.customSelect {
				font-size:12px;
				/*background-color: red;*/
				color:#7c7c7c;
				padding-left:7px;
				padding-top:7px;
				padding-bottom:7px;
				border:1px solid #aaa;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				border-radius: 5px 5px;
				//width:88%;
			}
			
			.customSelectInner {
				background:url("'.plugins_url().'/melascrivi/images/arrow-down.png") no-repeat center right;
				white-space: nowrap;
				overflow: hidden;
			}

		</style>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypalForm" style="margin-left:auto;margin-right:auto;width:242px;">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="business" value="WWTNKSH4W5YFU">
			<input type="hidden" name="lc" value="'.strtoupper($conf->paypal->language).'">
			<input type="hidden" name="item_name" value="'.__("recharge account",$this->getName()).'">
			<input type="hidden" name="button_subtype" value="services">
			<input type="hidden" name="no_note" value="1">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="rm" value="1">
			<input type="hidden" name="return" value="http://'.$_SERVER['HTTP_HOST'].$current_url.'?page=melascrivi-balance&response=success">
			<input type="hidden" name="cancel_return" value="http://'.$_SERVER['HTTP_HOST'].$current_url.'?page=melascrivi-balance&response=error">
			<input type="hidden" name="currency_code" value="'. $conf->paypal->currency.'">
			<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
			<input type="hidden" name="amount" value="">
			<input type="hidden" name="custom" value="'.$userId.'*'.$this->getServerPath().'">
			<table style="margin-bottom:20px;width:230px;">
				<tr>
					<td align="center" style="font-weight:bold;font-size:15px;line-height:45px;">
						<input type="hidden" name="on0" value="'.__("recharge your account",$this->getName()).'">'.__("recharge your account",$this->getName()).'
					</td>
				</tr>
				<tr>
					<td align="center">
						<select id="amountPayment" style="width:150px; text-align:left;">';
						
							if ($conf->iva>0){
								$ivaString='(+'.$conf->iva.'% '.__("iva",$this->getName()).')';
							}else{
								$ivaString="";
							}
							$echo.='<option value="30">30 '.$conf->valute.' '.$ivaString.'</option>
							<option value="50">50 '.$conf->valute.' '.$ivaString.'</option>
							<option value="100">100 '.$conf->valute.' '.$ivaString.'</option>
							<option value="150">150 '.$conf->valute.' '.$ivaString.'</option>
							<option value="200">200 '.$conf->valute.' '.$ivaString.'</option>
							<option value="250">250 '.$conf->valute.' '.$ivaString.'</option>
							<option value="300">300 '.$conf->valute.' '.$ivaString.'</option>
							<option value="400">400 '.$conf->valute.' '.$ivaString.'</option>
							<option value="500">500 '.$conf->valute.' '.$ivaString.'</option>
							<option value="750">750 '.$conf->valute.' '.$ivaString.'</option>
						</select>
					</td>
				</tr>
			</table>
			
			<img src="'.__("https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif",$this->getName()).'" style="cursor:pointer;" border="0" alt="PayPal - Il sistema di pagamento online pi&ugrave; facile e sicuro!" onclick="return submitPaypal();">
	  
			<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
		</form>';
		return $echo;
	}
	
	function printTicket(){
		$echo='
		<div class="ticketContainer" style="clear:both;">
		
		<div id="" class="form has-validation"   action="" style="clear:both;width:100%;margin-left:auto;margin-right:auto; border:none;" method="">
			
			<input type="hidden" name="action" value="sendTicket">
			
			<div class="clearfix" >
				<label for="titolo" style="border-top:none;width:10%; min-width:100px;" class="form-label no-description" >'.__("title",$this->getName()).':</label>
				<div style="width:90%; border-top:none;" class="form-input">
					<input type="text" id="titolo" name="titolo" placeholder="'.__("insert here the topic",$this->getName()).'" class="placeholder" required="required" />
				</div>
			</div>
			<div class="clearfix" style="border-bottom:none;" >
				<label for="testo" style="width:10%; min-width:100px;" class="form-label">'.__("text",$this->getName()).':</label>
				<div style="width:90%;" class="form-input form-textarea">
					<textarea id="testo" name="testo" rows="10" placeholder="'.__("insert here the text",$this->getName()).'" required="required"></textarea>
				</div>
			</div>
			
			<div  class="form-action clearfix">
				<button class="mela" onclick="sendMelaTicket();"   style="margin-top:10px; margin-left:11%; width:100px;">
					'.__("send",$this->getName()).'
				</button>
			</div>
		</div>
		</div>';
		return $echo;
	}
	
	function sendTicket($userId, $title,$text){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$ticket=new stdClass();
		$ticket->userId=$userId;
		$ticket->title=$title;
		$ticket->text=$text;
		$response=$this->client->sendTicket($ticket);
		return $response;
	}
	
	function deleteOrder($userId, $orderId){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->removeOrder($orderId,$userId);
		return $response;
	}

	function getOtherUserInfo($userId,$authorId){
		@$this->client->__setCookie("credential",$this->client->_cookies['credential'][0]);
		@setCookie("credential",$this->client->_cookies['credential'][0],time()+3600);
		$response=$this->client->getOtherUserInfo($userId,$authorId);
		return json_decode($response);
	
	}
	
}

?>