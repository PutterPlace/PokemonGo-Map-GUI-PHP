<?php
class dbConnection
{
    var $host;
    var $database;
    var $username;
    var $password;
    var $link;
    var $result;
    var $sql;
    
    public function __construct()
    {
        include('config.inc.php');
        $this->host = $dbHost;
        $this->database = $dbName;
        $this->username = $dbUser;
        $this->password = $dbPass;
        $this->link = mysql_connect($this->host, $this->username, $this->password);
        mysql_select_db($this->database, $this->link);
        return $this->link;
    }
    
    public function query($sql)
    {
        if (!empty($sql)) {
            $this->sql = $sql;
            $this->result = mysql_query($sql, $this->link);
            return $this->result;
        } else {
            return false;
        }
    }
    
    public function fetch($result = "")
    {
        if (empty($result)) {
            $result = $this->result;
        }
        return mysql_fetch_assoc($result);
    }
    
    public function __destruct()
    {
        mysql_close($this->link);
    }
}

$db = new dbConnection();

?>