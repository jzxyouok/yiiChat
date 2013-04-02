<?php
/**
 * ChatController
 * 
 * The controller performs the AJAX response to the widget.
 * 
 */
class ChatController extends CExtController
{
    /**
     * 
     * @var string Name of the table
     */
    public $tableName = 'tbl_chat';
    
    /**
     *
     * @var int The number of messages received while loading widget
     */
    public $messagesLimit = 15;
    
    
    public function filters() {
        return array(
            'ajaxOnly + messsages, addmessage',
        );
    }
    
    /**
     * Action returns the messages after specified id
     */
    public function actionMessages()
    {
        $lastID = isset($_GET['lastID']) ? $_GET['lastID'] : 0;
        
        if (!$lastID) {
            $subsql = "SELECT `id`, `username`, `date`, `message` " .
                        "FROM `$this->tableName` " .
                        "ORDER BY date DESC LIMIT $this->messagesLimit";
            $sql = "SELECT `id`, `username`, `date`, `message` " .
                        "FROM ( $subsql ) as t ORDER BY date ASC";
        }
        else {
            $sql = "SELECT `id`, `username`, `date`, `message` " .
                    "FROM `$this->tableName` " .
                    "WHERE `id` > $lastID " .
                    "ORDER BY `date` ASC";
        }
        $command = Yii::app()->db->createCommand($sql);
        $dataReader = $command->query();
        
        echo CJSON::encode($dataReader->readAll());
        Yii::app()->end();
    }
    
    /**
     * Action saved the message into the database and return its ID
     */
    public function actionAddmessage()
    {
        $username = self::deteleTags($_POST['username']);
        $message = self::deteleTags($_POST['message']);
        $date = self::deteleTags($_POST['date']);

        $sql = "INSERT INTO `tbl_chat` (`username`, `message`, `date`) VALUES ('$username', '$message', '$date');";
        $command = Yii::app()->db->createCommand($sql);
        $command->execute();
        
        $sql = "SELECT `id` FROM `tbl_chat` ORDER BY `id` DESC LIMIT 1;";
        $command = Yii::app()->db->createCommand($sql);
        echo $command->queryScalar();
        Yii::app()->end();
    }
    
    protected static function deteleTags($string)
    {
        return preg_replace('/<[^<]>/', '', $string);
    }
}