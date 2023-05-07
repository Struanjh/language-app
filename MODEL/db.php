<?php
class Db
{
    protected static function connectDB () {
        try {
            return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }  
    }
}  