<?php

class JournalInvoicesModel extends AbstractModel
{
    // object properties
    public $id= 0;
    public $journal_id= 0;
    public $cost_center_id= 0;
    public $invoice_type= 0;
    public $invoice_no= "";
    public $invoice_date= "";
    public $invoice_amount= 0;
    
    public $type_name= "";

    // constructor with $db as database connection
    public function __construct($db)
    {
        parent::__construct($db);
        $this->table_name = "journals_invoices";
        $this->table_alias = "JI";
        $this->primary_key = "id";

        $this->selection_fields = "JI.id, JI.journal_id, JI.cost_center_id, JI.invoice_type, JI.invoice_date, JI.invoice_no, JI.invoice_amount, T.type_name";
        $this->join_sql = " LEFT JOIN invoice_types T ON T.id=JI.invoice_type ";
        $this->order_by = "JI.id ASC";
    }

    function readOne()
    {
        $row = parent::readOneRecord($this->id);

        // set values to object properties
        extract($row);

        $this->id= (int) $id;
        $this->journal_id= (int) $journal_id;
        $this->cost_center_id= (int) $cost_center_id;
        $this->invoice_type= (int) $invoice_type;
        $this->invoice_no= $invoice_no;
        $this->invoice_date= $invoice_date;
        $this->invoice_amount= (float) $invoice_amount;

        $this->type_name= $type_name;
    }

    // create product
    function create()
    {

        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                    SET journal_id= :journal_id, cost_center_id= :cost_center_id, invoice_type= :invoice_type, 
                    invoice_no= :invoice_no, invoice_date= :invoice_date, invoice_amount= :invoice_amount ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->invoice_no = htmlspecialchars(strip_tags($this->invoice_no));
        $this->invoice_date = htmlspecialchars(strip_tags($this->invoice_date));

        // bind values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_id", $this->cost_center_id, PDO::PARAM_INT);
        $stmt->bindParam(":invoice_type", $this->invoice_type, PDO::PARAM_INT);
        $stmt->bindParam(":invoice_no", $this->invoice_no, PDO::PARAM_STR);
        $stmt->bindParam(":invoice_date", $this->invoice_date, PDO::PARAM_STR);
        $stmt->bindParam(":invoice_amount", $this->invoice_amount, PDO::PARAM_STR);

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
                    SET journal_id= :journal_id, cost_center_id= :cost_center_id, invoice_type= :invoice_type, 
                    invoice_no= :invoice_no, invoice_date= :invoice_date, invoice_amount= :invoice_amount
                    WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->invoice_no = htmlspecialchars(strip_tags($this->invoice_no));
        $this->invoice_date = htmlspecialchars(strip_tags($this->invoice_date));

        // bind values
        $stmt->bindParam(":journal_id", $this->journal_id, PDO::PARAM_INT);
        $stmt->bindParam(":cost_center_id", $this->cost_center_id, PDO::PARAM_INT);
        $stmt->bindParam(":invoice_type", $this->invoice_type, PDO::PARAM_INT);
        $stmt->bindParam(":invoice_no", $this->invoice_no, PDO::PARAM_STR);
        $stmt->bindParam(":invoice_date", $this->invoice_date, PDO::PARAM_STR);
        $stmt->bindParam(":invoice_amount", $this->invoice_amount, PDO::PARAM_STR);

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
            "cost_center_id"=> (int) $cost_center_id,
            "invoice_type"=> (int) $invoice_type,
            "invoice_no"=> html_entity_decode($invoice_no),
            "invoice_date"=> html_entity_decode($invoice_date),
            "invoice_amount"=> (float) $invoice_amount,
    
            "type_name"=> html_entity_decode($type_name),
        );
    }

    public function toArray()
    {
        return array(
            "id"=> (int) $this->id,
            "journal_id"=> (int) $this->journal_id,
            "cost_center_id"=> (int) $this->cost_center_id,
            "invoice_type"=> (int) $this->invoice_type,
            "invoice_no"=> html_entity_decode($this->invoice_no),
            "invoice_date"=> html_entity_decode($this->invoice_date),
            "invoice_amount"=> (float) $this->invoice_amount,
    
            "type_name"=> html_entity_decode($this->type_name),
        );
    }
}
