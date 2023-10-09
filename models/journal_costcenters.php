<?php

class JournalCostCentersModel extends AbstractModel
{
    // object properties
    public $id= 0;
    public $journal_id= 0;
    public $trans_id= 0;
    public $cost_type= 0;
    public $cost_center_id= 0;
    public $cost_center_code= "";
    public $cost_amount= 0;
    public $remarks= "";
    
    public $type_name= "";
    public $cost_center_name= "";

    // constructor with $db as database connection
    public function __construct($db)
    {
        parent::__construct($db);
        $this->table_name = "journals_cost_centers";
        $this->table_alias = "JC";
        $this->primary_key = "id";

        $this->selection_fields = "JC.id, JC.journal_id, JC.trans_id, JC.cost_type, JC.cost_center_code, JC.cost_center_id, JC.cost_amount, 
                                    JC.remarks, CT.type_name, CC.cost_center_name ";
        $this->join_sql = "
                LEFT JOIN cost_types CT ON CT.id=JC.cost_type
                LEFT JOIN cost_centers CC ON CC.id=JC.cost_center_id
        ";
        $this->order_by = "JC.id ASC";
    }

    function readOne()
    {
        $row = parent::readOneRecord($this->id);

        // set values to object properties
        extract($row);

        $this->id= (int) $id;
        $this->journal_id= (int) $journal_id;
        $this->trans_id= (int) $trans_id;
        $this->cost_type= (int) $cost_type;
        $this->cost_center_id= (float) $cost_center_id;
        $this->cost_center_code= $cost_center_code;
        $this->cost_amount= (float) $cost_amount;
        $this->remarks= $remarks;

        $this->type_name= $type_name;
        $this->cost_center_name= $cost_center_name;
    }

    // create product
    function create()
    {

        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                    SET journal_id= :journal_id, trans_id= :trans_id, cost_type= :cost_type, 
                    cost_center_id= :cost_center_id, cost_center_code= :cost_center_code, cost_amount= :cost_amount, 
                    remarks=:remarks ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->cost_center_code = htmlspecialchars(strip_tags($this->cost_center_code));
        $this->remarks = htmlspecialchars(strip_tags($this->remarks));

        // bind values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_id", $this->trans_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_type", $this->cost_type, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_id", $this->cost_center_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_code", $this->cost_center_code, PDO::PARAM_STR);
        $stmt->bindParam(":cost_amount", $this->cost_amount, PDO::PARAM_STR);
        $stmt->bindParam(":remarks", $this->remarks, PDO::PARAM_STR);

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
                    SET journal_id= :journal_id, trans_id= :trans_id, cost_type= :cost_type, 
                    cost_center_id= :cost_center_id, cost_center_code= :cost_center_code, cost_amount= :cost_amount, 
                    remarks=:remarks
                    WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->cost_center_code = htmlspecialchars(strip_tags($this->cost_center_code));
        $this->remarks = htmlspecialchars(strip_tags($this->remarks));

        // bind values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":trans_id", $this->trans_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_type", $this->cost_type, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_id", $this->cost_center_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_code", $this->cost_center_code, PDO::PARAM_STR);
        $stmt->bindParam(":cost_amount", $this->cost_amount, PDO::PARAM_STR);
        $stmt->bindParam(":remarks", $this->remarks, PDO::PARAM_STR);

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
            "trans_id"=> (int) $trans_id,
            "cost_type"=> (int) $cost_type,
            "cost_center_id"=> (float) $cost_center_id,
            "cost_center_code"=> html_entity_decode($cost_center_code),
            "cost_amount"=> (float) $cost_amount,
            "remarks"=> $remarks,
    
            "type_name"=> html_entity_decode($type_name),
            "cost_center_name"=> html_entity_decode($cost_center_name),
        );
    }

    public function toArray()
    {
        return array(
            "id"=> (int) $this->id,
            "journal_id"=> (int) $this->journal_id,
            "trans_id"=> (int) $this->trans_id,
            "cost_type"=> (int) $this->cost_type,
            "cost_center_id"=> (float) $this->cost_center_id,
            "cost_center_code"=> html_entity_decode($this->cost_center_code),
            "cost_amount"=> (float) $this->cost_amount,
            "remarks"=> $this->remarks,
    
            "type_name"=> html_entity_decode($this->type_name),
            "cost_center_name"=> html_entity_decode($this->cost_center_name),
        );
    }
}
