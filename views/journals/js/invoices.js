
function showInvoices(row) {
  var nextRow= $(row).parents('tr.cc-row').next();
  nextRow.addClass('expanded');
  $(".entry-invoice-invoice_type", nextRow).focus();
}

function hideInvoices(row) {
  $(row).parents('tr.invoice-expandable').removeClass('expanded');
}


function addInvoiceTable() {
  var invoiceTable= $(invoiceTemplate);
  $("button.entry-invoice-button-save", invoiceTable).on("click", function(){
    $(".entry-invoice-errors", invoiceTable).html("");

    var errors= "";
    if($(".entry-invoice-invoice_type", invoiceTable).val() == 0) {
      errors+= "Type is not selected.<br />";
    }
    if($(".entry-invoice-invoice_no", invoiceTable).val() == "") {
      errors+= "Invoice Number is not entered.<br />";
    }
    if($(".entry-invoice-invoice_date", invoiceTable).val() == 0) {
      errors+= "Invoice Date is not selected.<br />";
    }
    if($(".entry-invoice-invoice_amount", invoiceTable).val() == "" || parseFloat($(".entry-invoice-invoice_amount", invoiceTable).val()) <= 0) {
      errors+= "Invoice Amount is not entered.<br />";
    }

    if(errors != "") {
      $(".entry-invoice-errors", invoiceTable).html('<div class="alert alert-danger alert-dismissible " role="alert"> \
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> \
              <strong>' + errors + '</strong> \
      </div>');
      return false;
    }
    var data= {
      "invoice_type": $(".entry-invoice-invoice_type", invoiceTable).val(),
      "invoice_no": $(".entry-invoice-invoice_no", invoiceTable).val(),
      "invoice_date": $(".entry-invoice-invoice_date", invoiceTable).val(),
      "invoice_amount": parseFloat($(".entry-invoice-invoice_amount", invoiceTable).val()),
      "type_name": $(".entry-invoice-invoice_type option:selected", invoiceTable).text(),
    };

    addInvoice(invoiceTable, data);

    //clear entry
    $(".entry-invoice-invoice_type", invoiceTable).val("0").focus();
    $(".entry-invoice-invoice_no", invoiceTable).val("");
    $(".entry-invoice-invoice_date", invoiceTable).val(getToday());
    $(".entry-invoice-invoice_amount", invoiceTable).val("0");

  });

  return invoiceTable;
}

function addInvoice(invoiceTable, data) {
  var invoiceRow= $(invoiceRowTemplate);
  invoiceRow.data({"data": data});
  $(".view-invoice-invoice_type", invoiceRow).html(data.type_name);
  $(".view-invoice-invoice_no", invoiceRow).html(data.invoice_no);
  $(".view-invoice-invoice_date", invoiceRow).html(formatDate(data.invoice_date));
  $(".view-invoice-invoice_amount", invoiceRow).html(data.invoice_amount + "/-");

  $(".invoice-table tbody.invoice", invoiceTable).append(invoiceRow);
  $(".invoice-table", invoiceTable).removeClass("d-none");

  refreshInvoiceTable(invoiceTable);
}

function removeInvoiceRow(sender) {
  if(!confirm("Delete invoice?")) return false;
  var parent= $(sender).parents('table.invoice-table');
  var row= $(sender).parents('tr.invoice-row');
  row.remove();

  refreshInvoiceTable(parent);
}

function refreshInvoiceTable(row) {
  var count= 0;
  var total= 0;

  $("tr.invoice-row", row).each(function(){
    count++;

    $("span.invoice-sl", this).text(count);
    var val= parseFloat($(this).data("data").invoice_amount);
    if (isNaN(val)){
      val= 0;
    }
    total+= val;
  });

  $(".invoice-total", row).text(total + "/-");
}

function getInvoices(ccRow) {
  var invoices= [];

  $("tr.invoice-row", ccRow).each(function(){
    var row= $(this);
    invoices.push({
      "id": 0,
      "cost_center_id": 0,
      "invoice_type": row.data("data").invoice_type,
      "invoice_no": row.data("data").invoice_no,
      "invoice_date": row.data("data").invoice_date,
      "invoice_amount": row.data("data").invoice_amount,

      "type_name": row.data("data").type_name,
    });
  });

  return invoices;
}