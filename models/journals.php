<?php

class JournalsModel extends AbstractModel
{
    // object properties
    public $id= 0;
    public $voucher_no= "";
    public $voucher_date= "";
    public $cashbook_affected= "F";
    public $voucher_ref_no= "";
    public $company_id= 0;
    public $division_id= 0;
    public $department_id= 0;
    public $voucher_amt= 0;
    public $voucher_narr= "";
    public $voucher_narr_arabic= "";
    public $transaction_data= "";
    public $cost_center_data= "";
    public $invoice_data= "";
    public $created_at= "";
    public $modified_at= "";
    
    public $company_name= "";
    public $division_name= "";
    public $department_name= "";

    // constructor with $db as database connection
    public function __construct($db)
    {
        parent::__construct($db);
        $this->table_name = "journals";
        $this->table_alias = "JE";
        $this->primary_key = "id";

        $this->selection_fields = "JE.id, JE.voucher_no, JE.voucher_date, JE.cashbook_affected, JE.voucher_ref_no, 
                JE.company_id, JE.division_id, JE.department_id, JE.voucher_amt, JE.voucher_narr, JE.voucher_narr_arabic,
                JE.transaction_data, JE.cost_center_data, JE.invoice_data, JE.created_at, JE.modified_at,
                C.company_name, D.division_name, DP.department_name";
        $this->join_sql = "
                LEFT JOIN companies C ON C.id=JE.company_id
                LEFT JOIN divisions D ON D.id=JE.division_id
                LEFT JOIN departments DP ON DP.id=JE.department_id
        ";
        $this->order_by = "JE.voucher_date DESC, JE.voucher_no ASC";
    }

    function readOne()
    {
        $row = parent::readOneRecord($this->id);

        if(!$row) return false;
        // set values to object properties
        extract($row);

        $this->id= (int) $id;
        $this->voucher_no= $voucher_no;
        $this->voucher_date= $voucher_date;
        $this->cashbook_affected= $cashbook_affected;
        $this->voucher_ref_no= $voucher_ref_no;
        $this->company_id= (int) $company_id;
        $this->division_id= (int) $division_id;
        $this->department_id= (int) $department_id;
        $this->voucher_amt= (float) $voucher_amt;
        $this->voucher_narr= $voucher_narr;
        $this->voucher_narr_arabic= $voucher_narr_arabic;
        $this->transaction_data= $transaction_data;
        $this->cost_center_data= $cost_center_data;
        $this->invoice_data= $invoice_data;
        $this->created_at= $created_at;
        $this->modified_at= $modified_at;

        $this->company_name= $company_name;
        $this->division_name= $division_name;
        $this->department_name= $department_name;
    }

    // create product
    function create()
    {

        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                    SET voucher_no= :voucher_no, voucher_date= :voucher_date, cashbook_affected= :cashbook_affected, 
                    voucher_ref_no= :voucher_ref_no, company_id= :company_id, division_id= :division_id, department_id= :department_id, 
                    voucher_amt= 0, voucher_narr= :voucher_narr, voucher_narr_arabic= :voucher_narr_arabic, 
                    transaction_data= '', cost_center_data= '', invoice_data= '', created_at= NOW() ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->voucher_no = htmlspecialchars(strip_tags($this->voucher_no));
        $this->voucher_ref_no = htmlspecialchars(strip_tags($this->voucher_ref_no));
        $this->voucher_narr = htmlspecialchars(strip_tags($this->voucher_narr));
        $this->voucher_narr_arabic = htmlspecialchars(strip_tags($this->voucher_narr_arabic));

        // bind values
        $stmt->bindParam(":voucher_no", $this->voucher_no, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_date", $this->voucher_date, PDO::PARAM_STR);
        $stmt->bindParam(":cashbook_affected", $this->cashbook_affected, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_ref_no", $this->voucher_ref_no, PDO::PARAM_STR);
        $stmt->bindParam(":company_id", $this->company_id, PDO::PARAM_INT);
        $stmt->bindParam(":division_id", $this->division_id, PDO::PARAM_INT);
        $stmt->bindParam(":department_id", $this->department_id, PDO::PARAM_INT);
        $stmt->bindParam(":voucher_narr", $this->voucher_narr, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_narr_arabic", $this->voucher_narr_arabic, PDO::PARAM_STR);

        // $stmt->bindParam(":voucher_amt", $this->voucher_amt, PDO::PARAM_STR);
        // $stmt->bindParam(":transaction_data", $this->transaction_data, PDO::PARAM_STR);
        // $stmt->bindParam(":cost_center_data", $this->cost_center_data, PDO::PARAM_STR);
        // $stmt->bindParam(":invoice_data", $this->invoice_data, PDO::PARAM_STR);

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
                    SET voucher_date= :voucher_date, cashbook_affected= :cashbook_affected, voucher_ref_no= :voucher_ref_no, 
                    company_id= :company_id, division_id= :division_id, department_id= :department_id, 
                    voucher_amt = :voucher_amt, transaction_data= :transaction_data, cost_center_data= :cost_center_data,
                    invoice_data = :invoice_data, voucher_narr= :voucher_narr, voucher_narr_arabic= :voucher_narr_arabic, modified_at= NOW()
                    WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->voucher_ref_no = htmlspecialchars(strip_tags($this->voucher_ref_no));
        $this->voucher_narr = htmlspecialchars(strip_tags($this->voucher_narr));
        $this->voucher_narr_arabic = htmlspecialchars(strip_tags($this->voucher_narr_arabic));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(":voucher_date", $this->voucher_date, PDO::PARAM_STR);
        $stmt->bindParam(":cashbook_affected", $this->cashbook_affected, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_ref_no", $this->voucher_ref_no, PDO::PARAM_STR);
        $stmt->bindParam(":company_id", $this->company_id, PDO::PARAM_INT);
        $stmt->bindParam(":division_id", $this->division_id, PDO::PARAM_INT);
        $stmt->bindParam(":department_id", $this->department_id, PDO::PARAM_INT);
        $stmt->bindParam(":voucher_amt", $this->voucher_amt, PDO::PARAM_STR);
        $stmt->bindParam(":transaction_data", $this->transaction_data, PDO::PARAM_STR);
        $stmt->bindParam(":cost_center_data", $this->cost_center_data, PDO::PARAM_STR);
        $stmt->bindParam(":invoice_data", $this->invoice_data, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_narr", $this->voucher_narr, PDO::PARAM_STR);
        $stmt->bindParam(":voucher_narr_arabic", $this->voucher_narr_arabic, PDO::PARAM_STR);
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
            extract($row);

            $data_item = array(
                "id"=> (int) $id,
                "voucher_no"=> html_entity_decode($voucher_no),
                "voucher_date"=> $voucher_date,
                "cashbook_affected"=> $cashbook_affected,
                "voucher_ref_no"=> html_entity_decode($voucher_ref_no),
                "company_id"=> (int) $company_id,
                "division_id"=> (int) $division_id,
                "department_id"=> (int) $department_id,
                "voucher_amt"=> (float) $voucher_amt,
                "voucher_narr"=> html_entity_decode($voucher_narr),
                "voucher_narr_arabic"=> html_entity_decode($voucher_narr_arabic),
                "transaction_data"=> $transaction_data,
                "cost_center_data"=> $cost_center_data,
                "invoice_data"=> $invoice_data,
                "created_at"=> $created_at,
                "modified_at"=> $modified_at,
        
                "company_name"=> html_entity_decode($company_name),
                "division_name"=> html_entity_decode($division_name),
                "department_name"=> html_entity_decode($department_name),
            );

            array_push($data_array, $data_item);
        }

        return $data_array;
    }

    public function toArray()
    {
        return array(
            "id"=> (int) $this->id,
            "voucher_no"=> html_entity_decode($this->voucher_no),
            "voucher_date"=> $this->voucher_date,
            "cashbook_affected"=> $this->cashbook_affected,
            "voucher_ref_no"=> html_entity_decode($this->voucher_ref_no),
            "company_id"=> (int) $this->company_id,
            "division_id"=> (int) $this->division_id,
            "department_id"=> (int) $this->department_id,
            "voucher_amt"=> (float) $this->voucher_amt,
            "voucher_narr"=> html_entity_decode($this->voucher_narr),
            "voucher_narr_arabic"=> html_entity_decode($this->voucher_narr_arabic),
            "transaction_data"=> $this->transaction_data,
            "cost_center_data"=> $this->cost_center_data,
            "invoice_data"=> $this->invoice_data,
            "created_at"=> $this->created_at,
            "modified_at"=> $this->modified_at,
    
            "company_name"=> html_entity_decode($this->company_name),
            "division_name"=> html_entity_decode($this->division_name),
            "department_name"=> html_entity_decode($this->department_name),
        );
    }
}
