<?php

class Journals extends AbstractController {
  public function index() {
    $model= $this->loadModel("journals");
    App::view("journals/list", [ "journals"=> $model->readAsArray() ]);
  }

  public function entry() {
    $journal_id= isset($_REQUEST["journal_id"]) ? (int) $_REQUEST["journal_id"] : 0;
    $journal_data= false;

    if($journal_id > 0) {
      $model= $this->loadModel("journals");
      $model->id= $journal_id;
      $model->readOne();

      if($model->id > 0) {
        $journal_data= $model->toArray();

        $transModel= $this->loadModel("journal_trans");
        $ccModel= $this->loadModel("journal_costcenters");
        $invoiceModel= $this->loadModel("journal_invoices");

        $transactions= false;
        $trans_statement = $transModel->readCnd(" WHERE JT.journal_id=$journal_id ");
        if ($trans_statement->rowCount() > 0) {
          $transactions= array();
          while ($row = $trans_statement->fetch(PDO::FETCH_ASSOC)) { 
            $trans_item = $transModel->toArrayEx($row);

            $cc_rows= false;
            $cc_statement = $ccModel->readCnd(" WHERE JC.journal_id=$journal_id AND JC.trans_id={$trans_item["id"]} ");
            if ($cc_statement->rowCount() > 0) {
              $cc_rows= array();
              while ($row = $cc_statement->fetch(PDO::FETCH_ASSOC)) { 
                $cc_item = $ccModel->toArrayEx($row);
                
                $inv_rows= false;
                $inv_statement = $invoiceModel->readCnd(" WHERE JI.journal_id=$journal_id AND JI.cost_center_id={$cc_item["id"]} ");
                if ($inv_statement->rowCount() > 0) {
                  $inv_rows= array();
                  while ($row = $inv_statement->fetch(PDO::FETCH_ASSOC)) { 
                    $inv_item = $invoiceModel->toArrayEx($row);
                    array_push($inv_rows, $inv_item);
                  }
                }

                $cc_item["invoices"]= $inv_rows;
                array_push($cc_rows, $cc_item);
              }
            }

            $trans_item["cost_centers"]= $cc_rows;
            array_push($transactions, $trans_item);
          }
        }

        $journal_data["transactions"]= $transactions;
      } else {
        $journal_id= 0;
      }
    }

    App::view("journals/entry", [ "journal_id"=> $journal_id, "journal_data"=> $journal_data]);
  }


  public function save() {
    $data= $_POST["data"] ?? false;
    if(!$data) {
      raise_error(500, "Invalid Request", 500);
      exit();
    }

    App::db()->beginTransaction();

    //Save Journal Master
    $model= $this->loadModel("journals");
    
    $model->id= (int) $data['id'];
    $model->voucher_date= $data['voucher_date'];
    $model->cashbook_affected= $data['cashbook_affected'];
    $model->voucher_ref_no= $data['voucher_ref_no'];
    $model->company_id= (int) $data['company_id'];
    $model->division_id= (int) $data['division_id'];
    $model->department_id= (int) $data['department_id'];
    $model->voucher_narr= $data['voucher_narr'];
    $model->voucher_narr_arabic= $data['voucher_narr_arabic'];
    
    $model->voucher_no= $data['voucher_no'];

    $result= $data["id"] > 0 ? $model->update() : $model->create();
    if(!$result){
      App::db()->rollback();
      response(false, "Unable to update data.", 400);
      exit();
    }

    $journalID= $data["id"] > 0 ? $data["id"] : $model->lid();
    
    $voucher_amt= 0;
    $transaction_data= "";
    $cost_center_data= "";
    $invoice_data= "";


    $transModel= $this->loadModel("journal_trans");
    $ccModel= $this->loadModel("journal_costcenters");
    $invoiceModel= $this->loadModel("journal_invoices");

    //using delete all / insert all method for detail tables
    if($data["id"] > 0) {
      if(!$transModel->deleteRecordBy("journal_id", $journalID)) {
        App::db()->rollback();
        response(false, "Unable to update data.", 400);
        exit();
      }
      if(!$ccModel->deleteRecordBy("journal_id", $journalID)) {
        App::db()->rollback();
        response(false, "Unable to update data.", 400);
        exit();
      }
      if(!$invoiceModel->deleteRecordBy("journal_id", $journalID)) {
        App::db()->rollback();
        response(false, "Unable to update data.", 400);
        exit();
      }
    }
    

    foreach($data["transactions"] as $idx => $tran) {
      $transModel->id= (int) $tran["id"];
      $transModel->journal_id= $journalID;
      $transModel->trans_type= $tran["trans_type"];
      $transModel->account_id= (int) $tran["account_id"];
      $transModel->trans_amount= (float) $tran["trans_amount"];
      $transModel->account_code= $tran["account_code"];

      $transModel->account_name= $tran["account_name"];

      if(!$transModel->create()) {
        App::db()->rollback();
        response(false, "Unable to update data.", 400);
        exit();
      }

      $transID= $transModel->lid();

      $drAmount= (float) ($tran["trans_type"] == "Dr" ? $tran["trans_amount"] : 0);
      $crAmount= (float) ($tran["trans_type"] == "Cr" ? $tran["trans_amount"] : 0);
      $voucher_amt+= $drAmount;

      $transaction_data.= "*_*{$transModel->account_code}_={$transModel->account_id}_={$drAmount}_={$crAmount}_={$model->voucher_narr}_={$transID}_={$transModel->trans_type}";
      // *_*AccCode_=AccId_=Dr_=Cr_=Narration_=rowId_=transType


      if(is_array($tran["cost_centers"]) && (count($tran["cost_centers"]) > 0)) {
        foreach($tran["cost_centers"] as $idx1 => $ccItem) {

          $ccModel->id= 0;
          $ccModel->journal_id= $journalID;
          $ccModel->trans_id= $transID;
          $ccModel->cost_type= $ccItem["cost_type"];

          $ccModel->cost_center_id= $ccItem["cost_center_id"];
          $ccModel->cost_center_code= $ccItem["cost_center_code"];
          $ccModel->cost_amount= $ccItem["cost_amount"];
          $ccModel->remarks= $ccItem["remarks"];

          if(!$ccModel->create()) {
            App::db()->rollback();
            response(false, "Unable to update data.", 400);
            exit();
          }

          $ccID= $ccModel->lid();

          $cost_center_data.= "**={$transID}_~~_{$ccID}_~~_{$ccModel->cost_type}_~~_{$ccModel->cost_amount}_~~_{$ccModel->cost_center_id}_~~_{$ccItem["cost_center_name"]}";
          // **= transRowNo _~~_rowId_~~_costcenterType_~~_Amount_~~_id_~~_name
          // **=1_~~_1_~~_1_~~_11270_~~__~~_1_~~_CORPORATE Eram Arabia ( HR1001 )

          if(is_array($ccItem["invoices"]) && (count($ccItem["invoices"]) > 0)) {
            foreach($ccItem["invoices"] as $idx2 => $invoice) {
              $invoiceModel->id= 0;
              $invoiceModel->journal_id= $journalID;
              $invoiceModel->cost_center_id= $ccID;
              $invoiceModel->invoice_type= (int) $invoice['invoice_type'];
              $invoiceModel->invoice_no= $invoice['invoice_no'];
              $invoiceModel->invoice_date= $invoice['invoice_date'];
              $invoiceModel->invoice_amount= (float) $invoice['invoice_amount'];

              if(!$invoiceModel->create()) {
                App::db()->rollback();
                response(false, "Unable to update data.", 400);
                exit();
              }

              $invID= $invoiceModel->lid();

              $invoice_data.= "**={$ccID}_~~_{$invID}_~~_{$invoiceModel->invoice_date}_~~_{$invoiceModel->invoice_amount}_~~_{$invoiceModel->invoice_no}";
              // **=costCenterRow_~~_inoviceRow_~~_invoiceDate_~~_Amount_~~_invoiceNo
            }
          }
        }
      }
    }

    $model->id= $journalID;
    $model->readOne();

    $model->voucher_amt= $voucher_amt;
    $model->transaction_data= $transaction_data;
    $model->cost_center_data= $cost_center_data;
    $model->invoice_data= $invoice_data;
    if(!$model->update()) {
      App::db()->rollback();
      response(false, "Unable to update data.", 400);
      exit();
    }

    App::db()->commit();
    response(true, "", 200);
  }

  public function delete() {
    $journalID= (int) $_POST["id"] ?? 0;
    if($journalID <= 0) {
      raise_error(500, "Invalid Request", 500);
      exit();
    }

    $model= $this->loadModel("journals");
    $transModel= $this->loadModel("journal_trans");
    $ccModel= $this->loadModel("journal_costcenters");
    $invoiceModel= $this->loadModel("journal_invoices");

    App::db()->beginTransaction();

    $model->id = $journalID;
    if(!$model->delete()){
      App::db()->rollback();
      response(false, "Unable to delete data", 400);
      exit();
    }

    if(!$transModel->deleteRecordBy("journal_id", $journalID)) {
      App::db()->rollback();
      response(false, "Unable to update data.", 400);
      exit();
    }
    if(!$ccModel->deleteRecordBy("journal_id", $journalID)) {
      App::db()->rollback();
      response(false, "Unable to update data.", 400);
      exit();
    }
    if(!$invoiceModel->deleteRecordBy("journal_id", $journalID)) {
      App::db()->rollback();
      response(false, "Unable to update data.", 400);
      exit();
    }
    
    App::db()->commit();
    response(true, "", 200);
  }

}