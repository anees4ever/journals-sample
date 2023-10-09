<?php

abstract class AbstractModel {
    // database connection and table name
    protected $conn;
    protected $table_name = "";
    protected $table_alias = "";

    protected $primary_key = "id";
    
    protected $selection_fields = "*";
    protected $join_sql = "";
    protected $order_by = "";
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // used for paging products
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name;
    
        $stmt = $this->conn->prepare( $query );
        // execute the query
        if($stmt->execute()){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total_rows'];
        } else {
            Database::stop($stmt);
        }
    }

    // used to get all ids
    public function ids(){
        $query = "SELECT {$this->primary_key} FROM {$this->table_name} ";
        $stmt = $this->conn->prepare( $query );
        if($stmt->execute()){
            $ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return implode(',', array_column($ids, 'id'));
        } else {
            Database::stop($stmt);
        }
    }

    public function getReadQuery($whereCondition= "", $groupBy= "", $havingCondition= "", $limitStr= "") {
        return "SELECT " . $this->selection_fields . "
                FROM " . $this->table_name . " " . $this->table_alias . "
                " . $this->join_sql . "
                " . $whereCondition . "
                " . $groupBy . "
                " . $havingCondition . "
                " . ($this->order_by == "" ? "" : "ORDER BY " . $this->order_by) . "
                " . $limitStr;
    }

    // read products
    public function read(){
        return $this->readCnd();
    }

    // read products
    public function readCnd($whereCondition= ""){
        // select all query
        $query = $this->getReadQuery($whereCondition= $whereCondition, $groupBy= $this->getGroupByStr());
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute the query
        if($stmt->execute()){
            return $stmt;
        } else {
            Database::stop($stmt);
        }
    }

    public function readAsArray() {
        $this->read();
    }

    public function getGroupByStr() {
        return " GROUP BY " . ($this->table_alias==""?"":$this->table_alias.".").$this->primary_key;
    }
    // read products with pagination
    public function readPaging($from_record_num, $records_per_page){
    
        // select query
        $query = $this->getReadQuery($whereCondition= "", $groupBy= $this->getGroupByStr(), $havingCondition= "", $limitStr= "LIMIT ?, ?");
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    
        // execute the query
        if($stmt->execute()){
            // return values from database
            return $stmt;
        } else {
            Database::stop($stmt);
        }
    }

    // used when filling up the update product form
    public function readOneRecord($keyValue){
    
        // query to read single record
        $query = $query = $this->getReadQuery($whereCondition= "WHERE ".($this->table_alias==""?"":$this->table_alias.".").$this->primary_key." = ?", 
                                                $groupBy= "", $havingCondition= "", $limitStr= "LIMIT 0,1");
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        // bind id of product to be updated
        $keyValue= htmlspecialchars(strip_tags($keyValue));
        $stmt->bindParam(1, $keyValue);
    
        // execute the query
        if($stmt->execute()){
            // get retrieved row
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            Database::stop($stmt);
        }
    }

    function lid() {
        return $this->conn->lastInsertId();
    }

    // create product
    function create(){
            return false;
        
    }

    // update the product
    function update(){
            return false;
    }

    function deleteRecord($keyValue){
        return $this->deleteRecordBy($this->primary_key, $keyValue);
    }
    // delete the product
    function deleteRecordBy($keyField, $keyValue){
    
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE " . $keyField . " = ?";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $keyValue= htmlspecialchars(strip_tags($keyValue));
    
        // bind id of record to delete
        $stmt->bindParam(1, $keyValue);
    
        // execute the query
        if($stmt->execute()){
            return true;
        } else {
            Database::stop($stmt);
        }
    
        return false;
    }

    // search products
    function search($keywords){
    
    }
    
}