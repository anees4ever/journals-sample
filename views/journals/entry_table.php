<style>
.expandable div.container {
  transition: height, padding .3s ease;
  padding:0px;
  border-width: 1px;
  border-color: white;
  border-style: solid;
}
.expandable div.content-title {
  font-size: 1.2em;
  font-weight: bold;
}
.expandable:not(.expanded) td {
  padding:0;
  border-bottom-color:transparent;
}

.expandable:not(.expanded) div.container {
  height:0;
  overflow:hidden;
  border-style: none;
}
</style>

<script src="<?=App::$config['documentRoot'];?>/views/journals/js/trans.js"></script>
<script src="<?=App::$config['documentRoot'];?>/views/journals/js/cost-centers.js"></script>
<script src="<?=App::$config['documentRoot'];?>/views/journals/js/invoices.js"></script>

  <div class="table-responsive" id="tblTrans">
    <table class="table table-stripedx jambo_table mb-0" border="1">
      <thead>
        <tr class="headings">
          <th width="5%" class="column-title">SL </th>
          <th width="10%" class="column-title">Type </th>
          <th width="15%" class="column-title">Code </th>
          <th width="40%" class="column-title">Account </th>
          <th width="10%" class="column-title text-right">Dr </th>
          <th width="10%" class="column-title text-right">Cr </th>
          <th width="10%" class="column-title no-link last">Actions</th>
        </tr>
      </thead>

      <tbody class="trans">
      </tbody>
      <tfoot>
      <tr class="">
          <td class="gCellEx " colspan="2">
            <button type="button" id="btnAddNewTran" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> New Tran. Item</button>
          </td>
          <td class="gCellEx text-right" colspan="2">Total: </td>
          <td class="gCellEx text-right "><span id="labelTotalDr">0.00</span></td>
          <td class="gCellEx text-right "><span id="labelTotalCr">0.00</span></td>
          <td class="gCellEx text-center last"></td>
        </tr>
        <tr class="d-none" id="rowDifference">
          <td class="gCellEx text-right" colspan="4">Difference: </td>
          <td class="gCellEx text-right "><span id="labelDifferenceDr"></span></td>
          <td class="gCellEx text-right "><span id="labelDifferenceCr"></span></td>
          <td class="gCellEx text-center last"></td>
        </tr>
      </tfoot>
    </table>
  </div>

<script>

var transRowTemplate= '\
<tr class="trans-row"> \
    <td class="gCellEx"> \
      <a class="text-danger btn-sm trans-button-remove" onclick="removeTransRow(this);"> <i class="fa fa-trash"></i></a> \
      <span class="trans-sl">1</span> \
    </td> \
    <td class="gCell"> \
      <select class="form-control entry-trans-trans_type" onChange="onTrnTypeChange(this);"> \
        <option value="Dr">Dr</option> \
        <option value="Cr">Cr</option> \
      </select> \
    </td> \
    <td class="gCell"><input type="text" class="form-control entry-trans-account_code" placeholder=""></td> \
    <td class="gCell"><?= renderAccountsCombo("", 0, 'entry-trans-account_id'); ?></td> \
    <td class="gCell text-right "><input type="number" value="0" min="0" class="form-control text-right entry-trans-trans_amount_dr" onChange="refreshTransTable();"></td> \
    <td class="gCell text-right "><input type="number" value="0" min="0" class="form-control text-right entry-trans-trans_amount_cr" onChange="refreshTransTable();"></td> \
    <td class="gCell text-center last"> \
      <button type="button" class="btn btn-success btn-sm trans-button-plus" onclick="showCostCenters(this);"> <i class="fa fa-search"></i> Cost Centers</button> \
    </td> \
</tr> \
';
var costCenterTemplate= '\
<tr class="expandable  cc-expandable"> \
  <td class="" colspan="7"> \
    <div class="cost-center-list container" style="border-color:rgba(38,185,154,0.88);" > \
      <div class="alert alert-success content-title mb-0" role="alert"> \
        <span class="m-2">Cost Center Allocation</span> \
        <button type="button" class="close" onclick="hideCostCenters(this);" ><span aria-hidden="true">×</span></button> \
      </div> \
      <div class="well well-sm mb-0"> \
        <div class="row"> \
          <div class="col-md-2"> \
            <label>Type:</label> \
            <?=renderCostCenterTypeCombo("",0,"entry-cc-cost_type");?> \
          </div> \
          <div class="col-md-2"> \
            <label>Code:</label> \
            <input type="text" class="form-control entry-cc-cost_center_code" placeholder=""> \
          </div> \
          <div class="col-md-6"> \
            <label>Cost Center:</label> \
            <?=renderCostCenterCombo("",0,"entry-cc-cost_center_id");?> \
          </div> \
          <div class="col-md-2"> \
            <label>Value:</label> \
            <input type="number" value="0" min="0" class="form-control text-right entry-cc-cost_amount" placeholder=""> \
          </div> \
          \
          <div class="col-md-2 text-right"> \
            <label class="mt-2">Remarks: </label> \
          </div> \
          <div class="col-md-8"> \
            <input type="text" class="form-control entry-cc-remarks" placeholder=""> \
          </div> \
          <div class="col-md-2 "> \
          <button type="button" class="btn btn-success btn-sm entry-cc-button-save mt-1" > <i class="fa fa-save"></i> Add</button> \
          </div> \
        </div> \
      </div> \
      <div class="entry-cc-errors"></div> \
      <div class="table-responsive" > \
        <table class="table table-stripedx jambo_table mb-0 cost-center-table d-none" border="1"> \
          <thead style="background: rgba(38,185,154,0.88); color: white;"> \
            <tr class="headings"> \
              <th width="5%" class="column-title">SL </th> \
              <th width="15%" class="column-title">Code </th> \
              <th width="35%" class="column-title">Cost Center </th> \
              <th width="10%" class="column-title text-right">Amount </th> \
              <th width="25%" class="column-title">Remarks</th> \
              <th width="10%" class="column-title no-link last">Actions</th> \
            </tr> \
          </thead> \
          <tbody class="cc"> \
          </tbody> \
          <tfoot> \
          <tr class="" style="background: rgba(38,185,154,0.88); color: white;"> \
              <td class="gCellEx column-title text-right" colspan="3">Total: </td> \
              <td class="gCellEx column-title text-right cc-total ">0.00/-</td> \
              <td class="gCellEx text-center last" colspan="2"></td> \
            </tr> \
          </tfoot> \
        </table> \
      </div> \
    </div> \
  </td> \
</tr> \
';
var costCenterRowTemplate= '\
<tr class="cc-row"> \
  <td class="gCellEx"> \
    <a class="text-danger btn-sm cc-button-remove" onclick="removeCCRow(this);"> <i class="fa fa-trash"></i></a> \
    <span class="cc-sl"></span> \
  </td> \
  <td class="gCellEx view-cc-cost_center_code"></td> \
  <td class="gCellEx view-cc-cost_center_id"></td> \
  <td class="gCellEx view-cc-cost_amount text-right ">0.00/-</td> \
  <td class="gCellEx view-cc-remarks"></td> \
  <td class="gCellEx text-cc-center last"> \
    <button type="button" class="btn btn-info btn-sm cc-button-plus" onclick="showInvoices(this);"> <i class="fa fa-search"></i> Invoices</button> \
  </td> \
</tr> \
';

var invoiceTemplate= '\
<tr class="expandable invoice-expandable"> \
  <td class="" colspan="6"> \
    <div class="invoice-list container" style="border-color:rgba(52,152,219,0.88);" > \
      <div class="alert alert-info content-title mb-0" role="alert"> \
        <span class="m-2">Invoice Search</span> \
        <button type="button" class="close" onclick="hideInvoices(this);"><span aria-hidden="true">×</span></button> \
      </div> \
      <div class="well well-sm mb-0"> \
        <div class="row"> \
          <div class="col-md-2"> \
            <label>Type:</label> \
            <?=renderInvoiceTypeCombo("",0,"entry-invoice-invoice_type");?> \
          </div> \
          <div class="col-md-5"> \
            <label>Invoice / Description:</label> \
            <input type="text" class="form-control entry-invoice-invoice_no" placeholder=""> \
          </div> \
          <div class="col-md-2"> \
            <label>Date:</label> \
            <input class="date-picker form-control entry-invoice-invoice_date" value="<?=Date("Y-m-d");?>" type="date"> \
          </div> \
          <div class="col-md-2"> \
            <label>Amount:</label> \
            <input type="number" value="0" min="0" class="form-control text-right entry-invoice-invoice_amount" placeholder=""> \
          </div> \
          <div class="col-md-1 "> \
            <button type="button" class="btn btn-success btn-sm entry-invoice-button-save mt-4" > <i class="fa fa-save"></i> Add</button> \
          </div> \
        </div> \
      </div> \
      <div class="entry-invoice-errors"></div> \
      <div class="table-responsive " > \
        <table class="table table-stripedx jambo_table mb-0 invoice-table" border="1"> \
          <thead style="background: rgba(52,152,219,0.88); color: white;"> \
            <tr class="headings"> \
              <th width="5%" class="column-title">SL </th> \
              <th width="25%" class="column-title">Type </th> \
              <th width="40%" class="column-title">Invoice / Description </th> \
              <th width="15%" class="column-title">Date </th> \
              <th width="15%" class="column-title text-right">Invoice Amount</th> \
            </tr> \
          </thead> \
          <tbody class="invoice"> \
          </tbody> \
          <tfoot> \
          <tr class="" style="background: rgba(52,152,219,0.88); color: white;" > \
              <td class="gCellEx text-right column-title" colspan="4">Total: </td> \
              <td class="gCellEx text-right column-title last invoice-total ">0.00/-</td> \
            </tr> \
          </tfoot> \
        </table> \
      </div> \
    </div> \
  </td> \
</tr> \
';

var invoiceRowTemplate= '\
<tr class="invoice-row"> \
  <td class="gCellEx"> \
    <a class="text-danger btn-sm invoice-button-remove" onclick="removeInvoiceRow(this);"> <i class="fa fa-trash"></i></a> \
    <span class="invoice-sl">1</span> \
  </td> \
  <td class="gCellEx view-invoice-invoice_type"></td> \
  <td class="gCellEx view-invoice-invoice_no"></td> \
  <td class="gCellEx view-invoice-invoice_date">/td> \
  <td class="gCellEx view-invoice-invoice_amount text-right ">0.00/-</td> \
</tr> \
';
            
$(document).ready(function(){
  $("#btnAddNewTran").on("click", function(){
    addTransaction();
  });
  
  addTransaction();
});

</script>