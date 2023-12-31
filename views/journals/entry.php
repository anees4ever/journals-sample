<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="row" style="display: block;">

      <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>Journal Entry</h2>
            <div class="nav navbar-right panel_toolbox">
              <a href="<?= App::$config['documentRoot']; ?>journals" class="btn btn-secondary btn-sm"> <i class="fa fa-close"></i> Cancel</a>
              <button type="button" id="btnSaveJournal" class="btn btn-success btn-sm"> <i class="fa fa-save"></i> Save Voucher</button>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">

            <div id="entry-errors"></div>

            <form class="form-label-left input_mask">
              
              <input type="hidden" id="journalID" value="<?=$journal_id;?>" >
              <?php include("entry_top.php"); ?>
              <?php include("entry_table.php"); ?>
              <?php include("entry_bottom.php"); ?>


            </form>

<script>
  $(document).ready(function(){
    $("#btnSaveJournal").on("click", function(){
      $("#entry-errors").html("");

      var errors= "";
      if($("#txtVoucherNo").val() == 0) {
        errors+= "Voucher number is not entered.<br />";
      }
      if($("#txtVoucherDate").val() == 0) {
        errors+= "Voucher date is not entered.<br />";
      }
      if($("#cbCompany").val() == 0) {
        errors+= "Company is not selected.<br />";
      }


      var data= {
        "id": $("#journalID").val(),
        "voucher_no": $("#txtVoucherNo").val(),
        "voucher_date": $("#txtVoucherDate").val(),
        "cashbook_affected": $("#chEffectCB").prop("checked") ? "T" : "F",
        "voucher_ref_no": $("#txtRefNo").val(),
        "company_id": $("#cbCompany").val(),
        "division_id": $("#cbDivision").val(),
        "department_id": $("#cbDepartment").val(),
        "voucher_amt": 0,//filled later, 
        "voucher_narr": $("#txtNarration").val(),
        "voucher_narr_arabic": $("#txtNarrationAr").val(),

        "transaction_data": "",//filled later at server
        "cost_center_data": "",//filled later at server
        "invoice_data": "",//filled later at server

        "transactions": getTransactions(),
      };

      if(data["transactions"].length == 0) {
        errors+= "No Journal Records entered.<br />";
      } else if($("#rowDifference").is(":visible")) {
        errors+= "Voucher totals do not match [" + $("#labelTotalDr").text() + " &NotEqual; " + $("#labelTotalCr").text() + "].<br />";
      }


      if(errors != "") {
        $("#entry-errors").html('<div class="alert alert-danger alert-dismissible " role="alert"> \
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> \
                <strong>' + errors + '</strong> \
        </div>');
        return false;
      }


      showButtonProgress("btnSaveJournal");
      $.ajax({
    		url: '<?=App::$config["documentRoot"];?>journals/save',
    		type: 'POST',
    		data: {
    			'data': data,    			
    		},
    		dataType: 'json',
    		success: function(response) {
          if(response.result) {
            window.location= "<?= App::$config['documentRoot']; ?>journals";
          } else {
            hideButtonProgress("btnSaveJournal");
            alert(response.message);
          }
    		}

    	});

    });
  });

</script>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->