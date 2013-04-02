yiiChat
=======
Version of the software on which testing:

= Apache =======================================
version 2.2.22

using modules
	mod_rewrite
================================================

= PHP ==========================================
version 5.4.11

using extensions
	php_mysql Client API version mysqlnd 5.0.10 - 20111026 - $Id: b0b3b15c693b7f6aeb3aa66b646fee339f175e39 $
	php_mysqli Client API version mysqlnd 5.0.10 - 20111026 - $Id: b0b3b15c693b7f6aeb3aa66b646fee339f175e39 $
	php_pdo_mysql Client API version mysqlnd 5.0.10 - 20111026 - $Id: b0b3b15c693b7f6aeb3aa66b646fee339f175e39 $

================================================

= MySQL ========================================
version 5.6.10
================================================

Yii Chat

Chat displays a message box in the right side of the window.

To use the chat widget you must copy a chat folder into you application
folder. As example 'ext.yiiChat' folder. 
 
Then you must run the chat.sql to create the tbl_chat for saving messages.
 
And add to the application config follow instructions:

'controllerMap'=>array(
       'chat'=>array(
           'class'=>'ext.yiiChat.controllers.ChatController',
       ),
   )

The minimal code needed to use Chat is as follows:

$this->widget('ext.yiiChat.Chat');
