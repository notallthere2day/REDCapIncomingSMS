<?php

//Hook to add custom content to the project home page AC 3/12/2019

if ($hook_event == 'redcap_project_home_page' ) {
		
		//Display Quick start guide
		$addRecordURL="DataEntry/record_home.php?pid=$project_id";
		$addRecordIcon= APP_PATH_IMAGES . "blog_pencil.gif";
		$recordStatusDBURL="DataEntry/record_status_dashboard.php?pid=$project_id";
		$recordStatusIcon=APP_PATH_IMAGES . "application_view_icons.png";
		$queriesURL="DataQuality/resolve.php?pid=$project_id&status_type=OPEN";
		$queriesIcon=APP_PATH_IMAGES . "balloons.png";
		//$sampleBiofireReport = HOOK_PATH_ROOT . "pid$project_id/sample biofire report.pdf";	
		$sampleBiofireReport = "https://norwichcrtu.uea.ac.uk/CTUDocs_public/INHALE/sample biofire report.pdf";
		$trainingWorkbook = "https://norwichcrtu.uea.ac.uk/CTUDocs_public/INHALE/INHALE WP3 database training workbook- RN Adult current.pdf";
		Print "<div class=' chklist col-xs-12' style='padding:10px 20px;margin:20px 0;float: none;  -moz-border-radius: 20px; -webkit-border-radius: 20px; border-radius: 20px;'> ";
		print "<h3>QUIT-SENSE: Quick-start guide</h3>";
		print "<h4>Welcome to the QUIT-SENSE system.</h4>";
		print "<p>Further content and instructions can be placed here <br/>";
		// print "Use it together with the training workbook to practise the following tasks:</p>";
		// print "<ul>
		
		// <li>Create a new participant record</li>
		// <li>Enter data</li>
		// <li>Randomise a participant</li>
		// <li>Upload a biofire report</li>
		// <li>Consent a participant</li>
		// <li>Withdraw the patient from the study</li>

		// </ul>";
		
		// print "<br/><div class='green'><b>To get started, please download the files below:</b>";
		// print "<br/><a href='$trainingWorkbook' target='_blank'>Training workbook</a>";
		// print "<br/>You will need to follow the instructions and enter the data from this workbook.";
		// print "<br/><a href='$sampleBiofireReport' target='_blank'>Sample biofire report </a>";
		// print "<br/>This is for practising using the file upload feature only- you do not need to enter any data from this report.</div>";
		
		// print "<br/><br/>You can navigate between records and between forms using the following links in the left hand menu:<br/>";
		
		// print "<br/><b>Add a new participant, or find an existing participant:</b> <br/><a href='$addRecordURL'><img src='$addRecordIcon'/> Add/Edit records</a><br/>";
		// print "<br/><b>View a list of participants already in the database:</b><br/><a href='$recordStatusDBURL'><img src='$recordStatusIcon'/> Record Status Dashboard</a><br/>";
		// print "<br/><b>View and answer Queries that have been raised against records at your site:</b><br/><a href='$queriesURL'><img src='$queriesIcon'/> Resolve Issues</a><br/>";
		
		// print "<br/><b>To return to this page:</b> <br/><span class='glyphicon glyphicon-home' style='text-indent:0;' aria-hidden='true'></span> Project Home </br>";
		
		
		//get incoming text messages:
		//get all messages stored at the general_arm_1 event where the datetime is not blank, and 'sms_read' is not 1
		
		$smsEventID='1404';
		$loadNewSMS = REDCap::getData('array',NULL, array('sms_body', 'sms_datetime', 'sms_fromno'), 'general_arm_1', NULL, FALSE, FALSE, FALSE, "[sms_datetime]!='' AND [sms_read] !='1' AND [sms_direction] ='1'");
		
		//print_array($loadNewSMS);
		
		$smsInboxHTML="";
		
		$newSMSs=array();
		$numNewSmss=0;
		
		//loop through SMS messages and 
		foreach($loadNewSMS AS $record=>$data){
			$smss=$data['repeat_instances'][$smsEventID]['sms'];
			foreach($smss AS $instance=>$smsData){
				
				//if($smsData['sms_direction']=='1')
					array_push($newSMSs, array('PID'=>$record, 'sms_datetime'=>$smsData['sms_datetime'], 'sms_body'=>$smsData['sms_body']));
					$smsInboxHTML.=renderSMS($record, $smsData['sms_datetime'],$smsData['sms_body'], $project_id);
					$numNewSmss+=1;
			}
			
		}
		
		
		// foreach($newSMSs AS $index=>$sms){
			
			
		// }
		
		//print_Array($newSMSs);
		
		
		
	
		
		Print "</div> ";
		
		Print "<div class=' chklist col-xs-12' style='padding:10px 20px;margin:20px 0;float: none;  -moz-border-radius: 20px; -webkit-border-radius: 20px; border-radius: 20px;'> ";
		print "<h3>SMS Inbox</h3>";
		
		
		//this bit does work, but need to find an alternative to hard-coding the SID and authentication key here. AC 4-12-2019
		//echo "looking up account balance...";
		global $twilio_account_sid, $twilio_auth_token;
		
		$SID=$twilio_account_sid;
		$authToken=$twilio_auth_token;
		
		
		
		$url = "https://api.twilio.com/2010-04-01/Accounts/$SID/Balance.json";
							//$data = array($SID => '91275f4f9412b1c66b27fd9c51cfabad');

							// use key 'http' even if you send the request to https://...
							$options = array(
								'http' => array(
									'method'  => 'GET',
									'header' => "Authorization: Basic " . base64_encode("$SID:$authToken")  
									
									
								)
							);
							
							//print_array($options);
							$context  = stream_context_create($options);
							$result = file_get_contents($url, false, $context);
							if ($result === FALSE) { /* Handle error */ }
							
							//echo $result;
							
							$APIresponse = json_decode($result);
							
							$balance=round($APIresponse->balance,2);
							
							echo "Twilio Account Balance: <b>£$balance</b>";
							
							//$activationCode= $APIresponse->ActivationCode;
		
		
		print "<p><b>You have $numNewSmss unread messages. </b><br/> Click a message to see all messages from this participant on their record home page. Click on an individual message from there to mark it as read. </p>";
		
		echo "";
		echo $smsInboxHTML;
		// print "<br/><div class='green' style='cursor:pointer;'><b>Participant 143:</b> 14-11-2019 14:22:31<br/>Hello!</div>";
		
		// print "<br/><div class='green' style='cursor:pointer;'><b>Participant 22:</b> 12-11-2019 12:04:26<br/>I have a question...</div>";
		
		// print "<br/><div class='yellow' style='cursor:pointer;'><b>Unknown number +4471234 567891:</b> 12-11-2019 12:04:26<br/>I have a new phone- how can I re-install the app?</div>";
		
		
		Print "</div> ";
		//Hide unnecessary panels unless admin user
		//ok haven't added the logic about admin users yet!		
		
		$CSSString="<style type='text/css'>
					
					.round {display:none !important;}
					
				</style>";
				
		//echo $CSSString;
		//#quick-tasks {display:none !important;}
		
		
	}
	
		function renderSMS($PID, $datetime, $body, $project_id){
			//generates the html to represent an SMS
			if($PID=="9999"){
				$class="yellow";
				$PIDLabel="unknown number";
			}else{
				$class="green";
				$PIDLabel="Participant $PID";
			}
			$linkURL=APP_PATH_WEBROOT."DataEntry/record_home.php?pid=$project_id&id=$PID&arm=1";
			$string="<a href='$linkURL' style='color:#000;text-decoration: none;'><div class='$class' style='margin:2px; cursor:pointer;' title='Go to the record home page for $PIDLabel'>";
			$string.="<b>$PIDLabel at </b>$datetime<br/>";
			$string.="$body";
			$string.="</div></a>";
			return $string;
		}