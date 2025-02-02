<?php 
/*
App::uses('ClassRegistry', 'Utility');
App::uses('UserListener', 'Lib/Event');
App::uses('GradeListener', 'Lib/Event');
App::uses('MailerListener', 'Lib/Event');
App::uses('LoginListener', 'Lib/Event');
App::uses('CakeEventManager', 'Event');

//account notification
$User = ClassRegistry::init('User');
$User->getEventManager()->attach(new UserListener());

//login notification
$Login = ClassRegistry::init('User');
$Login->getEventManager()->attach(new LoginListener());

//exam grade notification
$Grade = ClassRegistry::init('ExamGrade');
$Grade->getEventManager()->attach(new GradeListener());

//message notification
$Mailer = ClassRegistry::init('Mailer');
$Mailer->getEventManager()->attach(new MailerListener());
*/