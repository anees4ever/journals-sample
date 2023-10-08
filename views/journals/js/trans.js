function toggleChildGrid(sender) {
  $(sender).parents('tr').next().toggleClass('expanded');
}


function addTransaction(data) {
  var newRow= $(transRowTemplate);
  $("#tblTrans tbody.trans").append(newRow);
  $("#tblTrans tbody.trans").append(addCostCenterTable());

  $("select.entry-trans-trans_type", this).focus();

  refreshTransTable();
}


function removeTransRow(sender) {
  if(!confirm("Delete transaction?")) return false;
  var row= $(sender).parents('tr.trans-row');
  row.next().remove();
  row.remove();

  refreshTransTable();
}



function onTrnTypeChange(sender) {
  refreshTransTable();
}

function refreshTransTable() {
  var count= 0;
  var crTotal= 0, drTotal= 0;

  $("#tblTrans tr.trans-row").each(function(){
    count++;

    $("span.trans-sl", this).text(count);

    if($("select.entry-trans-trans_type", this).val() == "Cr") {
      var val= parseFloat($("input.entry-trans-trans_amount_cr", this).val());
      if (isNaN(val)){
        val= 0;
      }
      crTotal+= val;
      $("input.entry-trans-trans_amount_dr", this).val("0").prop({disabled: true});
      $("input.entry-trans-trans_amount_cr", this).prop({disabled: false});
    } else {
      var val= parseFloat($("input.entry-trans-trans_amount_dr", this).val());
      if (isNaN(val)){
        val= 0;
      }
      drTotal+= val;
      $("input.entry-trans-trans_amount_cr", this).val("0").prop({disabled: true});
      $("input.entry-trans-trans_amount_dr", this).prop({disabled: false});
    }
  });

  $("#labelTotalDr").text(drTotal + "/-");
  $("#labelTotalCr").text(crTotal + "/-");

  if(drTotal == crTotal) {
    $("#rowDifference").addClass("d-none");
  } else {
    $("#labelDifferenceDr").text( drTotal > crTotal ? (drTotal - crTotal) + "/-" : "" );
    $("#labelDifferenceCr").text( drTotal > crTotal ? "" : (crTotal - drTotal) + "/-" );
    $("#rowDifference").removeClass("d-none");
  }
}

function formatDate(date) {
  var tempDate = new Date(date);
  return [tempDate.getMonth() + 1, tempDate.getDate(), tempDate.getFullYear()].join('/');
}

function getTransactions() {
  var transactions= [];

  $("#tblTrans tr.trans-row").each(function(){
    var row= $(this);

    if($(".entry-trans-account_id", row).val() > 0) {   
      transactions.push({
        "id": 0,
        "journal_id": 0,
        "trans_type": $(".entry-trans-trans_type", row).val(),
        "account_code": $(".entry-trans-account_code", row).val(),
        "account_id": $(".entry-trans-account_id", row).val(),
        "trans_amount": $(".entry-trans-trans_type", row).val() == "Cr" ? $(".entry-trans-trans_amount_cr", row).val() : $(".entry-trans-trans_amount_dr", row).val(),

        "cost_centers": getCostCenters(row.next()),
      });
    }
  });

  return transactions;
}