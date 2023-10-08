
function showCostCenters(row) {
  var nextRow= $(row).parents('tr.trans-row').next();
  nextRow.addClass('expanded');
  $(".entry-cc-cost_type", nextRow).focus();
}

function hideCostCenters(row) {
  $(row).parents('tr.cc-expandable').removeClass('expanded');
}

function addCostCenterTable() {
  var costCenterTable= $(costCenterTemplate);

  $("button.entry-cc-button-save", costCenterTable).on("click", function(){
    $(".entry-cc-errors", costCenterTable).html("");

    var errors= "";
    if($(".entry-cc-cost_type", costCenterTable).val() == 0) {
      errors+= "Type is not selected.<br />";
    }
    if($(".entry-cc-cost_center_code", costCenterTable).val() == "") {
      errors+= "Code is not entered.<br />";
    }
    if($(".entry-cc-cost_center_id", costCenterTable).val() == 0) {
      errors+= "Cost Center is not selected.<br />";
    }
    if($(".entry-cc-cost_amount", costCenterTable).val() == "" || parseFloat($(".entry-cc-cost_amount", costCenterTable).val()) <= 0) {
      errors+= "Cost Center Value is not entered.<br />";
    }

    if(errors != "") {
      $(".entry-cc-errors", costCenterTable).html('<div class="alert alert-danger alert-dismissible " role="alert"> \
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> \
              <strong>' + errors + '</strong> \
      </div>');
      return false;
    }
    var data= {
      "cost_type": $(".entry-cc-cost_type", costCenterTable).val(),
      "cost_center_code": $(".entry-cc-cost_center_code", costCenterTable).val(),
      "cost_center_id": $(".entry-cc-cost_center_id", costCenterTable).val(),
      "cost_amount": parseFloat($(".entry-cc-cost_amount", costCenterTable).val()),
      "remarks": $(".entry-cc-remarks", costCenterTable).val(),
      "cost_type_name": $(".entry-cc-cost_type option:selected", costCenterTable).text(),
      "cost_center_name": $(".entry-cc-cost_center_id option:selected", costCenterTable).text(),
    };

    var costRow= $(costCenterRowTemplate);
    costRow.data({"data": data});
    $(".view-cc-cost_center_code", costRow).html(data.cost_center_code);
    $(".view-cc-cost_center_id", costRow).html(data.cost_center_name);
    $(".view-cc-cost_amount", costRow).html(data.cost_amount + "/-");
    $(".view-cc-remarks", costRow).html(data.remarks);

    $(".cost-center-table tbody.cc", costCenterTable).append(costRow);
    $(".cost-center-table tbody.cc", costCenterTable).append(addInvoiceTable());

    refreshCCTable();

    //clear entry
    $(".entry-cc-cost_type", costCenterTable).val("0").focus();
    $(".entry-cc-cost_center_code", costCenterTable).val("");
    $(".entry-cc-cost_center_id", costCenterTable).val("0");
    $(".entry-cc-cost_amount", costCenterTable).val("0");
    $(".entry-cc-remarks", costCenterTable).val("");

    $(".cost-center-table", costCenterTable).removeClass("d-none");
  });


  return costCenterTable;
}

function removeCCRow(sender) {
  if(!confirm("Delete cost center?")) return false;
  var row= $(sender).parents('tr.cc-row');
  row.next().remove();
  row.remove();

  refreshCCTable();
}

function refreshCCTable(row) {
  var count= 0;
  var total= 0;

  $("tr.cc-row", row).each(function(){
    count++;

    $("span.cc-sl", this).text(count);
    var val= parseFloat($(this).data("data").cost_amount);
    if (isNaN(val)){
      val= 0;
    }
    total+= val;
  });

  $(".cc-total", row).text(total + "/-");
}

function getCostCenters(transRow) {
  var cost_centers= [];

  $("tr.cc-row", transRow).each(function(){
    var row= $(this);
    cost_centers.push({
      "id": 0,
      "trans_id": 0,
      "cost_type": row.data("data").cost_type,
      "cost_center_code": row.data("data").cost_center_code,
      "cost_center_id": row.data("data").cost_center_id,
      "cost_amount": row.data("data").cost_amount,
      "remarks": row.data("data").remarks,

      "invoices": getInvoices(row.next()),
    });
  });

  return cost_centers;
}