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
        
        if (!$lastID)
            $sql = "SELECT `id`, `username`, `date`, `message` " .
                        "FROM `$this->tableName` ORDER BY `id` DESC LIMIT $this->messagesLimit";
        else
            $sql = "SELECT `id`, `username`, `date`, `message` " .
                    "FROM `$this->tableName` " .
                    "WHERE `id` > $lastID " .
                    "ORDER BY `id` ASC";

        $command = Yii::app()->db->createCommand($sql);
        $data = $command->query()->readAll();
        
        // Reverse
        if (!$lastID)
            $data = array_reverse ($data);
        
        echo CJSON::encode($data);
        Yii::app()->end();
    }
    
    /**
     * Action saved the message into the database and return its ID
     */
    public function actionAddmessage()
    {
        $username = htmlspecialchars($_POST['username']);
        $message = htmlspecialchars($_POST['message']);
        $date = htmlspecialchars($_POST['date']);

        $sql = "INSERT INTO `tbl_chat` (`username`, `message`, `date`) VALUES (:username, :message, :date);";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(':username', $username, PDO::PARAM_STR);
        $command->bindParam(':message', $message, PDO::PARAM_STR);
        $command->bindParam(':date', $date, PDO::PARAM_INT);
        $command->execute();
        
        $sql = "SELECT `id` FROM `tbl_chat` ORDER BY `id` DESC LIMIT 1;";
        $command = Yii::app()->db->createCommand($sql);
        echo $command->queryScalar();
        Yii::app()->end();
    }
}