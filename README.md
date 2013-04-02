yiiChat
=======

Yii Chat

Chat displays a message box in the right side of the window.
 *
 * To use the chat widget you must copy a chat folder into you application
 * folder. As example 'ext' folder. 
 * 
 * Then you must run the chat.sql to create the tbl_chat for saving messages.
 * 
 * And add to the application config follow instructions:
 * <pre>
 * 'controllerMap'=>array(
 *       'chat'=>array(
 *           'class'=>'ext.chat.controllers.ChatController',
 *       ),
 *   )
 * </pre>
 * 
 * The minimal code needed to use Chat is as follows:
 *
 * <pre>
 * $this->widget('ext.chat.Chat');
 * </pre>