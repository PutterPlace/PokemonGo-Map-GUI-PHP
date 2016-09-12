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
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        return $this->link;
    }
    
    public function query($sql)
    {
        if (!empty($sql)) {
            $this->sql = $sql;
            $this->result = mysqli_query($this->link, $sql);
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
        return mysqli_fetch_assoc($result);
    }
    
    public function __destruct()
    {
        mysqli_close($this->link);
    }
}

$db = new dbConnection();

?>