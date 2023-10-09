<?php

class JournalTransModel extends AbstractModel
{
    // object properties
    public $id= 0;
    public $journal_id= 0;
    public $trans_type= "Dr";
    public $account_id= 0;
    public $trans_amount= 0;
    public $account_code= "";
    
    public $account_name= "";

    // constructor with $db as database connection
    public function __construct($db)
    {
        parent::__construct($db);
        $this->table_name = "journals_trans";
        $this->table_alias = "JT";
        $this->primary_key = "id";

        $this->selection_fields = "JT.id, JT.journal_id, JT.trans_type, JT.account_code, JT.account_id, JT.trans_amount, A.account_name ";
        $this->join_sql = " LEFT JOIN account_heads A ON A.id=JT.id ";
        $this->order_by = "JT.id ASC";
    }

    function readOne()
    {
        $row = parent::readOneRecord($this->id);

        // set values to object properties
        extract($row);

        $this->id= (int) $id;
        $this->journal_id= (int) $journal_id;
        $this->trans_type= $trans_type;
        $this->account_id= (int) $account_id;
        $this->trans_amount= (float) $trans_amount;
        $this->account_code= $account_code;

        $this->account_name= $account_name;
    }

    // create product
    function create()
    {

        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                    SET journal_id= :journal_id, trans_type= :trans_type, account_id= :account_id, 
                    trans_amount= :trans_amount, account_code= :account_code ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_type", $this->trans_type, PDO::PARAM_STR);
        $stmt->bindParam(":account_id", $this->account_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_amount", $this->trans_amount, PDO::PARAM_STR);
        $stmt->bindParam(":account_code", $this->account_code, PDO::PARAM_STR);

        // execute query
        if ($stmt->execute()) {
            return true;
        } else {
            Database::stop($stmt);
        }

        return false;
    }

    // update the product
    function update()
    {

        // update query
        $query = "UPDATE " . $this->table_name . "
                    SET journal_id= :journal_id, trans_type= :trans_type, account_id= :account_id, 
                    trans_amount= :trans_amount, account_code= :account_code, 
                    WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind new values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_type", $this->trans_type, PDO::PARAM_STR);
        $stmt->bindParam(":account_id", $this->account_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_amount", $this->trans_amount, PDO::PARAM_STR);
        $stmt->bindParam(":account_code", $this->account_code, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        // execute the query
        if ($stmt->execute()) {
            return true;
        } else {
            Database::stop($stmt);
        }

        return false;
    }

    function delete()
    {
        return parent::deleteRecord($this->id);
    }


    public function readAllFor($journal_id) {
        $stmt = $this->readCnd(" WHERE JT.journal_id=$journal_id ");
        $num = $stmt->rowCount();

        if ($num > 0) {
            return $this->toArrayList($stmt);
        } else {
            false;
        }
    }

    public function readAsArray()
    {
        $stmt = $this->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            return $this->toArrayList($stmt);
        } else {
            false;
        }
    }

    public function toArrayList($stmt)
    {
        $data_array = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $data_item = $this->toArrayEx($row);

            array_push($data_array, $data_item);
        }

        return $data_array;
    }

    public function toArrayEx($row)
    {
        extract($row);

        return array(
            "id"=> (int) $id,
            "journal_id"=> (int) $journal_id,
            "trans_type"=> $trans_type,
            "account_id"=> (int) $account_id,
            "trans_amount"=> (float) $trans_amount,
            "account_code"=> html_entity_decode($account_code),
    
            "account_name"=> html_entity_decode($account_name),
        );
    }

    public function toArray()
    {
        return array(
            "id"=> (int) $this->id,
            "journal_id"=> (int) $this->journal_id,
            "trans_type"=> $this->trans_type,
            "account_id"=> (int) $this->account_id,
            "trans_amount"=> (float) $this->trans_amount,
            "account_code"=> html_entity_decode($this->account_code),
    
            "account_name"=> html_entity_decode($this->account_name),
        );
    }
}
