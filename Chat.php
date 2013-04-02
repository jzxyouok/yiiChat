<?php

/**
 * Chat class file.
 *
 * @author Palenov Maxim <MAX_ELEK@mail.ru>
 * @copyright Copyright &copy; 2013
 * 
 * Chat displays a message box in the right side of the window.
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
 * 
 */
class Chat extends CWidget
{
    /**
     * @var string the id of the widget
     */
    public $id = 'chat';
    /*
     * @var boolean Determines how it will be shown a message box.
     * If $bOpen = false (by default) the message box is close, else
     * if $bOpen = true the message box is open.
     */
    public $bOpen = false;
    /**
     *
     * @var int The update time interval for message box list in ms 
     */
    public $updateInterval = 5000;
    
    /**
     *
     * @var string Content container ID 
     */
    private $contentID = 'chat_content';
    /**
     *
     * @var string ID of the send button
     */
    private $sendButtonID = 'send_button';
    /**
     *
     * @var string ID of the text field for messages 
     */
    private $textFieldID = 'chat_text';
    /**
     *
     * @var string ID of thetag for open or close message box
     */
    private $openCloseTagID = 'chat_open_button';
    
    /**
	 * @var array the HTML options for the view container tag.
	 */
	public $htmlOptions=array();
    /**
	 * Renders the view.
	 * This is the main entry of the whole view rendering.
	 */
	public function run()
	{
		$this->publishAssets();

		echo CHtml::openTag('div', array('id' => 'chat') + $this->htmlOptions)."\n";

		$this->renderContent();
        
		echo CHtml::closeTag('div');
    }
    
    /**
     * Renders the content inside of the widget.
     * Is is consists of the open / close Tag, container for messages, text field
     * for input messages and send button.
     */
    public function renderContent()
    {
        echo CHtml::openTag('div', array('id' => $this->openCloseTagID));
        echo CHtml::closeTag('div');
        
        echo CHtml::openTag('div', array('id' => $this->contentID));
        echo CHtml::closeTag('div');
        
        echo CHtml::textField('chat_text', '', array('id' => $this->textFieldID));
        
        echo CHtml::button('send', array('id' => $this->sendButtonID));
    }
    
    /**
	 * Publises and registers the required CSS and Javascript
     * @throws CHttpException if the assets folder was not found
	 */
	public function publishAssets()
	{
        $assets = dirname(__FILE__).'/assets';
        $baseUrl = Yii::app() -> assetManager -> publish($assets);
        if (is_dir($assets)) {
            // Register jQuery
            Yii::app() -> clientScript -> registerCoreScript('jquery');
            // Register CSS
            Yii::app() -> clientScript -> registerCssFile($baseUrl . '/css/chat.css');
            // Register js
            Yii::app() -> clientScript -> registerScriptFile($baseUrl . '/js/chat.js', CClientScript::POS_END);
            
            $options=array(
                'updateUrl' => 'index.php/chat/messages',
                'sendMessageUrl' => 'index.php/chat/addmessage',
                'username' => Yii::app()->user->isGuest ? Yii::app()->user->guestName : Yii::app()->user->name,
                'openCloseTagID' => $this->openCloseTagID,
                'contentID' => $this->contentID,
                'sendButtonID' => $this->sendButtonID,
                'textFieldID' => $this->textFieldID,
                'bOpen' => $this->bOpen,
                'updateInterval' => $this->updateInterval,
            );
            $options = CJavaScript::encode($options);
            Yii::app() -> clientScript -> registerScript(__CLASS__.'#'.$this->id, "jQuery('#$this->id').chat($options);");
        } else {
            throw new CHttpException(500, __CLASS__ . ' - Error: Couldn\'t find assets to publish.');
        }
	}
}