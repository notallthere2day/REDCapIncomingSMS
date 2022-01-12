<?php

//hook to handle incoming text messages for the QUIT-SENSE study
//When a new SMS is sent to the QUIT-SENSE number
//
// Disable REDCap's authentication
define("NOAUTH", true);

	//set mailinglist and from addresses for notifications of new incoming SMS messages
	$admin_mailing_list="[set email address here]";
	$email_from_address="[set email address here]";


	// Call the REDCap Connect file
	 require_once "../../redcap_connect.php";
	
	//set log file path
	 $logfile=APP_PATH_TEMP."logs\twilio_log.txt";
	
	 
	 
	//ID of project where the incoming messages should be stored
	$project_id=217;
	// event name and ID to save the sms in
	//event ID hard coded as this file outside project context. 
	$sms_event_name='general';
	$sms_event_id=1404;
	
		
	 //get POST variables 	 
	 $body = $_REQUEST['Body'];
	 $fromNumber = $_REQUEST['From'];
	
		 
	  //save response in REDCap project
	 
	 //get record for the phone number specified
	 
	 //get righthand 12 digits of phone number (This works in UK, but may need to change depending on your national mobile number format)
	 $formattedFromNo=substr($fromNumber, -12,12);
	 
	 //lookup record ID for this mobile number
	  $query="select * from redcap_data
				where project_id=$project_id
				and field_name='phone_num_twilio'
				and value='$formattedFromNo';";
		
		 $result = mysqli_query($conn, $query);
		  $resultArray=mysqli_fetch_assoc($result);
		 
		 $matchingRecord=$resultArray['record'];
		 //print_array($matchingRecord);
		 $PID='1';
		 //update record
		 if (isset($matchingRecord) && $matchingRecord!=""){
			 //if number is in database, save against that record
			$PID=$matchingRecord; 
		 }
		 
//get highest instance ID for this form and record:
	$query2="select MAX(instance) AS 'instance'  from redcap_data
			where project_id=$project_id
			AND record=$PID
			and field_name='sms_complete'
			;";
		
		 $result2 = mysqli_query($conn, $query2);
		 //print_array($result2);
		 $resultarray2=mysqli_fetch_assoc($result2);
		 //print_array($resultarray2);
		 $maxInstance=$resultarray2['instance'];
		 //echo $matchingRecord;
		 if(is_numeric($maxInstance)){
			$newInstance=intval($maxInstance)+1;	  
		 }else{
			$newInstance='2';
		 }		 
		 
		
			saveSMS($project_id, $formattedFromNo, $body, $PID, $sms_event_id, $newInstance);
			
			// Send email alert to study admin
			
			//Set up variables, including link to inbox page
			$linkURL= "https://". SERVER_NAME . APP_PATH_WEBROOT ."index.php?pid=$project_id";
			$toAddress=$admin_mailing_list;
			$fromAddress=$email_from_address;
			$subject="QUIT-SENSE SMS Incoming";
			$email_text = "QUIT-SENSE Participant $PID sent you an SMS. <br/> <a href='$linkURL'>Click here to go to your SMS inbox.</a>";

			//If participant texts stop or STOP
			if(stripos($body, 'STOP') !== false){
				$subject="QUIT-SENSE Participant has texted STOP";
				$email_text = "QUIT-SENSE Participant $PID Has texted STOP. <br/> <a href='$linkURL'>Click here to go to your SMS inbox.</a>";
			}
			
			//send the email
			REDCap::email($toAddress, $fromAddress, $subject, $email_text);

	
	 //log response
	 $fullpost=implode(" ",$_POST);
	  $log  = "Date and time: ".date("F j, Y, g:i a").PHP_EOL.
			  "From: ".$fromNumber.PHP_EOL.
              
			  "Body: ".$body.PHP_EOL.
			  
			  "full post:".$fullpost.PHP_EOL.
              "-------------------------".PHP_EOL;
			
			// //$log  = "Testing";
    
     $logResult=file_put_contents($logfile, $log, FILE_APPEND);
	

function saveSMS($project_id, $number, $body, $PID, $sms_event_id, $newInstance){
	//saves the details of the incoming SMS to the appropriate repeating form in the  project
	$timestamp=date("Y-m-d H:i:s");
	$numEnding=substr($number, -3, 3);
	//***add datetime sent! Need to get this from request, as may be delayed.
	$sms_details=array();
	//create an array of data for this sms
	$sms_details[$newInstance]=array('sms_datetime'=>$timestamp, 'sms_num_ending'=>$numEnding, 'sms_from_number'=>$number, 'sms_body'=>$body,'sms_direction'=>'1');
	//create an array for saving
	$saveData = array($PID=>array('repeat_instances'=>array($sms_event_id=>array('sms'=>$sms_details))));
	//print_array($saveData);
	//save it
	$saveResponseArray=REDCAp::saveData($project_id, 'array', $saveData, 'normal', 'YMD', 'flat', NULL, TRUE, TRUE, TRUE);

	//convert message to lower case for matching
	$lowerBody=strtolower($body);

?>