<?php

namespace Source\Models;

class Db {
    // Database with sheets/token tables
    private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "sheets_api";

    public function __construct(){
        if(!isset($this->db)){
            $conn = new \mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
                $this->db = $conn;
            }
        }
    }

    public function is_table_empty() {
        $result = $this->db->query("SELECT id FROM token");
        if($result->num_rows) {
            return false;
        }

        return true;
    }

    public function get_access_token() {
        $sql = $this->db->query("SELECT access_token FROM token");
        $result = $sql->fetch_assoc();
        return json_decode($result['access_token']);
    }

    public function get_refersh_token() {
        $result = $this->get_access_token();
        return $result->refresh_token;
    }

    public function update_access_token($token) {
        if($this->is_table_empty()) {
            $this->db->query("INSERT INTO token(access_token) VALUES('$token')");
        } else {
            $this->db->query("UPDATE token SET access_token = '$token' WHERE id = (SELECT id FROM token)");
        }
    }

    public function delete_token() {
        $result = $this->db->query("DELETE FROM token");
        return true;
    }

    public function new_sheet($sheetId, $sheetName) {
        $this->db->query("INSERT INTO sheet(sheet_id, sheet_name) VALUES('$sheetId', '$sheetName')");
    }

    public function get_sheets() {
        $sql = $this->db->query("SELECT * FROM sheet");
        $result = array();
        while ($row =  $sql->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }
}
