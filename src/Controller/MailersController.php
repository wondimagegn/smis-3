<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class MailersController extends AppController {
	public $name = 'Mailers';
	public $uses = array('Mailer','User','ExamGrade');
	public $menuOptions = array(

	  'exclude' => array('index','send_test'),
	  'weight'=>-2,
	   'alias' => array(
	    'compose_message' => 'Send mail',
	)
	);
    public $paginate =[];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Email');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('send_test','post_notification');

    }

	/**
	*Given student id
	*/
	function __attachGradeToEmail($student_id=null,$exam_grade_id=null){
	        //find email in student table if exist else in
	        // user table if exists
	        $email=$this->ExamGrade->Student->field('email',
	        array('Student.id'=>$student_id));
	        if(!empty($email)){
	              $exam_grade_detail=$this->ExamGrade->find('all',
	              array('conditions'=>array('ExamGrade.id'=>$exam_grade_id,
	              'ExamGrade.student_id'=>$student_id),'contain'=>array('CourseRegistration'=>array('PublishedCourse'=>array('Course'=>array('course_title','course_code','credit'))))));
	              $body="";
	              $this->__sendGradeNotification($email,$subject,$body);
	        }
	}

	/**
	* send grade notification message and log to database; a private function
         */
         function __sendGradeNotification($email= null,
                 $subject = null,
                 $body = null){
             $sent=false;
             $auth = $this->Session->read('Auth.User');
             $from = $auth['id'];
             $contentOfEMail=NULL;
             if($email != null){
                    $userIdAndBatchName['user_id'] = $auth['id'];
                    if($this->_sendEmail('grade_notification',
                                     $subject,
                                     $email,
                                     $userIdAndBatchName
                                     )) {
                             $contentOfEMail="To:".$email.
                                 "\n"."Subject:".$subject."\n".
                                 $this->__getEmailReturnAddress().
                                 "\n"."--content--"."\n".$body."\n";
                             $message=array();
                             $message['from'] = $from;
                             $message['subject'] = $subject;
                             $message['content'] = $contentOfEMail;
                             $message['user_id'] = $user_id;
                             $message['model'] = 'ExamGrade';

                             $this->Mailer->logMessage($message);

                             $sent=true;
                             $contentOfEMail=null;
                         }else{
                             $sent=false;
                         }
                return $sent;

            }
         }


		/**
		* A function that takes user_id and set first name and last name
		* @ return false if the user_id is invalid
		*/
        function __attachNameToEmail($user_id){
            // if the User id is valid and get the name of the person
            // to attach to his/her name in message for personolization
               if($user_id){
                    $User=$this->User->getNameOfTheUser($user_id);

                    if(!empty($User)){

                       if(isset($User['Staff']) && !empty($User['Staff'])){
                           if (isset($User['Title']) &&
                           !empty($User['Title'])) {
                                $title=$User['Title']['title'];
                                 $this->set('titleofperson',$title);
                            }
                            $firstname=$User['Staff']['first_name'];
                            $lastname=$User['Staff']['last_name'];


                      } else {
                                $firstname=$User['first_name'];
                                $lastname=$User['last_name'];
                      }
                       $this->set('firstname',$firstname);
                       $this->set('lastname',$lastname);

                    }
                    return true;
                } else {
                    // invalid User id don't send the email
                    return false;
                }
                //return true;
        }

        function index() {
            $this->Mailer->recursive = 0;
            $this->paginate = array('order' => 'Mailer.created DESC');

            $this->set('messages', $this->paginate());
        }





         /**
         *  This function setup the template ,subject and  list of users who are
         *  receiver of this email
         * @ return true or false based on the return of send function
         */
          function _sendEmail($templateName,
                $emailSubject,$to,
                $userIdAndBatchName,
                $from = 'AMU <bugs@mereb.com.et>',
                $replyToEmail = 'noreply@mereb.com.et',
                $return = 'bugs@mereb.com.et',
                $sendAs = 'both') {

                if(!$this->__attachNameToEmail($userIdAndBatchName['user_id'])){
                        // invalid user id don't send the email
                        return false;
                }

                $this->Email->to = $to;

                $this->Email->subject = $emailSubject;
                $this->Email->replyTo = $replyToEmail;
                $this->Email->from = $from;
                // address for bounced mail
                $this->Email->return = $return;
                // additional configuration setting  to  override send mail
                // return path
                $this->Email->additionalParams = "-r $return";
                $this->Email->template = $templateName;
                $this->Email->sendAs = $sendAs;

                $this->Email->smtpOptions = array(
                        'port'=>'465',
                        'timeout'=>'30',
                        'host' => 'ssl://smtp.gmail.com',
                        'username' => 'wondetask@gmail.com',
                        'password' => '1234!QAZ',
                );
                $this->Email->delivery = 'smtp';

                return $this->Email->send();
         }


         /**
         * A function which send a test mail before actual sending to
         * a user to see how his/her message looks. We can extend this to allow a user
         * to send a message for a particular user for not all those users in role.
         */
         public function send_test(){
                $Email = new CakeEmail('gmail');


		if(!empty($this->request->data['Mailer']['message']) &&
!empty($this->request->data['Mailer']['to'])){
		        $Email->template('admin_email_template');
			$Email->emailFormat('html');
			$Email->from(array('wondetask@gmail.com'=>'SMIS'));
                        $Email->to($this->request->data['Mailer']['to']);
			$Email->subject('Testing Account');
			$Email->viewVars(array('content'=>$this->request->data['Mailer']['message']));
                       // $Email->message($this->request->data['Mailer']['message']);

                        if($Email->send()) {
                  	   $this->Session->setFlash('<span></span>'.__('Email sent successfully'),
                                 'default',array('class'=>'success-box success-message'));
		        } else {
		          $this->Session->setFlash('<span></span>'.__('
Email not sent.Check your email server is up and running',true),'default',
array('class'=>'error-box error-message'));
		        }

		}

		/*
                $from = 'AMU <bugs@mereb.com.et>';
                $replyToEmail = 'noreply@mereb.com.et';
                $return = 'bugs@mereb.com.et';
                $sendAs = 'both';
                $to=$this->request->data['Mailer']['to'];
               // $link=$this->request->data['Mailer']['link'];

                if(!empty($to)){
                    $dereja="ato";
                    $this->set('firstname',$to);
                   // $this->set('titleofperson',$dereja);
                }

                $this->Email->to = $to;

                $this->set('message',$this->request->data['Mailer']['message']);
              //  $this->set('message','TEST');
                $this->Email->replyTo = $replyToEmail;
                //$this->Email->from = $from;
                // address for bounced mail
                 $this->Email->return = $return;
                // additional configuration setting  to  override send mail
                // return path
                $templateName='admin_email_template';
                $this->Email->additionalParams = "-r $return";
                $this->Email->template = $templateName;
                $this->Email->sendAs = $sendAs;
                $this->Email->smtpOptions = array(
                        'port'=>'465',
                        'timeout'=>'30',
                        'host' => 'ssl://smtp.gmail.com',
                        'username' => 'wondetask@gmail.com',
                        'password' => '1234!QAZ',
                );
                $this->Email->delivery = 'smtp';


                if($this->Email->send()) {

                         $this->Session->setFlash(
                                 '<span></span>'.__('email sent successfully'),
                                 'default',array('class'=>'success-box success-message'));

                } else {

                          $this->Session->setFlash(
                                 '<span></span>'.__('Email not sent.Check your email server is
                                     up and running',true),'default',array('class'=>'error-box error-message'));

                }
                */

         }

		 /**
		 * This function set return email address
		 * @ return the setted email addresses
		 */
		 function __getEmailReturnAddress(){
		                 $returnAddress=null;
		                 $returnAddress="From:".$this->Email->from."\n".
		                  "Reply-To:".$this->Email->replyTo."\n".
		                  "Return-Path:".$this->Email->return."";
		                  if(isset($returnAddress)){
		                        return $returnAddress;
		                  }
		                 // return $returnAddress;
		 }

		 /**
		 * send message and log to database; a private function
         */
         function _sendMessageAndLog($emailsOfUsers = null,
                 $subject = null,
                 $body = null,$role_id=null){
             $sent=false;
             $auth = $this->Session->read('Auth.User');
             $from = $auth['full_name'];
             $contentOfEMail=NULL;
             if(!empty($emailsOfUsers) && is_array($emailsOfUsers)){

                 foreach($emailsOfUsers as $key => $value){
                     $user_id = $key;
                     $email = $value; //$emailOfUser
                     if($email != null){
                         $userIdAndBatchName=array();
                         $toname=$this->User->field('User.full_name', array(
                                'User.id'=>$user_id));
                         $userIdAndBatchName['user_id'] =$toname;

                         if($this->_sendEmail('admin_email_template',
                                     $subject,
                                     $email,
                                     $userIdAndBatchName
                                     )) {
                             $contentOfEMail="To:".$email.
                                 "\n"."Subject:".$subject."\n".
                                 $this->__getEmailReturnAddress().
                                 "\n"."--content--"."\n".$body."\n";
                             $message=array();

                             $message['from'] = $from;
                             $message['subject'] = $subject;
                             $message['content'] = $contentOfEMail;
                             $message['user_id'] = $toname;
                             $message['model'] = 'EmailBatch';

                             $this->Mailer->logMessage($message);

                             $sent=true;
                             $contentOfEMail=null;
                         }else{
                             $sent=false;
                         }

                     }
                     $this->Email->reset();
                 }
             }
             return $sent;

         }
         /**
         * a function which allows users to compose their message and send to
         * User person
         */
         function compose_message()
         {
             //

             if(isset($this->request->data['Mailer']['role_id']) &&
                     !empty($this->request->data['Mailer']['role_id'])){

                 if(isset($this->request->data['Mailer']['subject']) ||
                         isset($this->request->data['Mailer']['message'])){

                     $this->set('subject',$this->request->data['Mailer']['subject']);
                     $this->set('message',$this->request->data['Mailer']['message']);

                 }
               $emailsOfUsers = $this->User->getEmailsForRoles($this->request->data['Mailer']['role_id']);

               if(!empty($emailsOfUsers)){
                     $sendMessageAndLog =
                         $this->_sendMessageAndLog($emailsOfUsers,
                             $this->request->data['Mailer']['subject'],
                             $this->request->data['Mailer']['message'],
                             $this->request->data['Mailer']['role_id']
                             );

                     if($sendMessageAndLog){
                         $this->Session->setFlash(
                                 '<span></span>'.__('email sent successfully'),
                                 'default',array('class'=>'success-box success-message'));
                     }else{
                         $this->Session->setFlash(
                                 '<span></span>'.__('Email not sent.Check your email server is
                                     up and running',true),'default',array('class'=>'error-box error-message'));
                     }
                 } else {
                     $this->Session->setFlash(
                             '<span></span>'.__('email not sent check you have users in this role
                                in the database',true),'default',array('class'=>'error-box error-message'));
                 }

             } else{
                 $this->Session->setFlash(
                         '<span></span>'.__('Please select at least one role'),
                         'default',array('class'=>'info-box info-message'));
             }

             $roles = $this->User->Role->find('list');
             $this->set(compact('roles'));
         }

         public function post_notification()
         {

             if(isset($this->request->data['Mailer']['role_id']) && !empty($this->request->data['Mailer']['role_id'])){

                 if(isset($this->request->data['Mailer']['subject']) ||
                         isset($this->request->data['Mailer']['message'])){

                     $this->set('subject',$this->request->data['Mailer']['subject']);
                     $this->set('message',$this->request->data['Mailer']['message']);

                 }

                $message['from']=$this->Auth->user('id');
                $message['subject'] = $this->request->data['Mailer']['subject'];
                $message['content'] = $this->request->data['Mailer']['message'];
                $message['model']='EmailBatch';
                $message['user_id'] = $this->Auth->user('id');

                $message['role_ids'] = $this->request->data['Mailer']['role_id'];

                if(!empty($this->department_ids)){
                  $message['department_ids'] = $this->department_ids;
                } else if(!empty($this->college_ids)){
                   $message['college_ids'] = $this->college_ids;
                } else if(!empty($this->department_id)) {

                    $message['department_ids'] = $this->department_id;
                } else if(!empty($this->college_id)) {
                     $message['college_ids'] = $this->college_id;
                }
               $message['program_id'] = $this->program_id;
               $message['program_type_id'] = $this->program_type_id;
               $scheduled=$this->Mailer->logMessage($message);

                 $scheduled=ClassRegistry::init('AutoMessage')->postMessageToGroup($this->request->data['Mailer']['role_id'], $this->request->data['Mailer']['subject'], $this->request->data['Mailer']['message']);

               if(!empty($scheduled)){
                      $this->Session->setFlash(
                                 '<span></span>'.__('Message posted  on dashboard '),
                                 'default',array('class'=>'success-box success-message'));

                 } else {
                     $this->Session->setFlash(
                             '<span></span>'.__('Message not posted  on dashboard',true),'default',array('class'=>'error-box error-message'));
                 }

             } else{
                 $this->Session->setFlash(
                         '<span></span>'.__('Please select at least one role'),
                         'default',array('class'=>'info-box info-message'));
             }

             $roles = $this->User->Role->find('list');
             $this->set(compact('roles'));
         }

}
?>
