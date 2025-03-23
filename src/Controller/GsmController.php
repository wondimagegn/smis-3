<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;



//App::uses('SmsReceiver','Lib/sms');
//App::uses('SmsSender','Lib/sms');
class GsmController extends AppController {
        public $name = "Gsm";
        public $uses = array();
        public $menuOptions = array(
             //'title'=>'ljkasdfjklasdf',
            'exclude'=>array('index'),
            'weight'=>-10000000,
        );
         public $content,$address,$requestId,$applicationId,$encoding,
$version,$binary_header,$deliveryStatusRequest,$sourceAddress,$responseMsg;


    public $paginate =[];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {
        $this->Auth->allow('index');
        parent::beforeFilter($event);
    }
	 private function initializeConfig() {
	 	$this->applicationId = "APP_000001";
	 	$this->encoding = "0";
	 	$this->version =  "1.0";
	    	$this->password = "password";
	    	$this->sourceAddress = "77000";
	    	$this->deliveryStatusRequest = "1";
	    	$this->charging_amount = ":15.75";
	    	//$this->destinationAddresses = array("tel:251920575085");
	    	$this->binary_header = "";
	 }

	 public function index() {
		    $this->logFile("[content=start]");
		    $this->initializeConfig();

               	    $receiver = new SmsReceiver(); // Create the Receiver object
		    $this->content = $receiver->getMessage(); // get the message content
		    $this->destinationAddresses = $receiver->getAddress(); // get the sender's address
		    $this->requestId = $receiver->getRequestID(); // get the request ID
		    $this->applicationId = $receiver->getApplicationId(); // get application ID
		    $this->encoding = $receiver->getEncoding(); // get the encoding value
		    $this->version = $receiver->getVersion(); // get the version

    		   //$this->logFile("[ content=$this->content, address=$this->destinationAddresses, requestId=$this->requestId, applicationId=$this->applicationId, encoding=$this->encoding, version=$this->version ]");

		    $this->sendMessage($this->destinationAddresses);
	 }

	/*
	*   Get Mobile number and send text message again
	**/

	private function sendMessage($mobileAddress) {
		try {

		    //your logic goes here......
		    $this->responseMsg = $this->messageLogic(trim($this->content));
		    $this->logFile("responseMsg: $this->responseMsg");
		    // Create the sender object server url
		    $sender = new SmsSender("https://localhost:7443/sms/send");

		     $this->logFile("sender: $sender $mobileAddress");
		    //sending a one message
		     $res = $sender->sms($this->responseMsg, $mobileAddress, $this->password, $this->applicationId, $this->sourceAddress, $this->deliveryStatusRequest, $this->charging_amount, $this->encoding, $this->version, $this->binary_header);
                    $this->logFile("[res=$res]");
		} catch (SmsException $ex) {
		    //throws when failed sending or receiving the sms
		    $this->logFile("ERROR: {$ex->getStatusCode()} | {$ex->getStatusMessage()}");
		}

	}

        /*
	*BMI logic function
	*/
	private function messageLogic($msg)
	{
	    $responseMsg='';

	    if (sizeof($msg) > 1) {
		$responseMsg = "Invalid message content. Your message must be one word: password/grade/deadline/status/dorm'. Dont provide more than one word. ";
	    } else {
		   $formattedPhone = explode('tel:', $this->destinationAddresses);
          $checkPhoneIsValid=ClassRegistry::init('Student')->isPhoneValid($formattedPhone[1]);
		 //return $checkPhoneIsValid;
		 if($checkPhoneIsValid) {
			switch(strtolower($msg)) {
				 case "password":
					$responseMsg = ClassRegistry::init('User')->resetPasswordBySMS($formattedPhone[1]);
				      break;
				 case "grade":
				       $responseMsg = ClassRegistry::init('ExamGrade')->getMostApprovedGradeForSMS($formattedPhone[1]);
				      break;
				 case "deadline":
				       $responseMsg = ClassRegistry::init('AcademicCalendar')->getMostRecentAcademicCalenderForSMS($formattedPhone[1]); // not complete
				       break;
				 case "status":
				       $responseMsg = ClassRegistry::init('StudentExamStatus')->getMostRecentStatusForSMS($formattedPhone[1]); // not complete
				       break;
				 case "dorm":
				       $responseMsg = ClassRegistry::init('DormitoryAssignment')->getDormitoryAssignmentForSMS($formattedPhone[1]); // not complete
				       break;

				 default:
			            $responseMsg = "Your message format is wrong. It must be one of the following words(password,grade,deadline,status,dorm)";

			}
		} else {
                   //TODO display generatic information which doesnt need registration, distribution statistic
                   $responseMsg = "Your phone number not in our system. Please call to our support team (+251920575087)";
		}
	    }
	    return $responseMsg;
	}



	private function logFile($rtn){
		$f=fopen("/var/www/smis-2/app/tmp/log-sms.txt","a");
		fwrite($f, $rtn . "\n");
		fclose($f);
	}



}
?>
