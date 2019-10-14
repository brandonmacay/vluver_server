<?php
/**
 * Created by PhpStorm.
 * User: Brandon
 * Date: 24/11/2018
 * Time: 14:18
 */

define("DB_HOST", "localhost");
define("DB_USER", "id10986463_vluver");
define("DB_PASSWORD", "@Brando2001");
define("DB_DATABASE", "id10986463_vluver");

class connect_db {
    private $conn;
    public function connect() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        return $this->conn;
    }
}
?>