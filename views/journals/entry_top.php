<div class="well well-sm no-shadow ">

  <div class="row">
    <div class="col-md-4 col-sm-4">
      <div class="form-group row">
        <label class="col-form-label col-md-3 col-sm-6 ">Voucher No</label>
        <div class="col-md-9 col-sm-6 ">
          <input type="text" id="txtVoucherNo" class="form-control" placeholder="">
        </div>
      </div>
    </div>

    <div class="col-md-5 col-sm-4">
      <div class="form-group row">
        <label class="col-form-label col-md-3 col-sm-6 ">Voucher Date</label>
        <div class="col-md-5 col-sm-6 ">
          <input class="date-picker form-control" id="txtVoucherDate" value="<?=Date("Y-m-d");?>" type="date" required="required">
        </div>
        <div class="col-md-4 col-sm-6">
          <div class="checkbox mt-2">
            <input type="checkbox" value="" id="chEffectCB">
            <label for="chEffectCB"> Is effect Cash Book</label>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-4">
      <div class="form-group row">
        <label class="col-form-label col-md-4 col-sm-6 ">Ref. No.</label>
        <div class="col-md-8 col-sm-6 ">
          <input type="text" id="txtRefNo" class="form-control" placeholder="">
        </div>
      </div>
    </div>

  </div>

  <div class="row">

    <div class="col-md-4 col-sm-6">
      <div class="form-group row">
        <label class="col-form-label col-md-3 col-sm-6 ">Company</label>
        <div class="col-md-9 col-sm-6 ">
          <?= renderCompanyCombo("cbCompany"); ?>
        </div>
      </div>
    </div>

    <div class="col-md-5 col-sm-6">
      <div class="form-group row">
        <label class="col-form-label col-md-3 col-sm-6 ">Division</label>
        <div class="col-md-9 col-sm-6 ">
          <?= renderDivisionCombo("cbDivision"); ?>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="form-group row">
        <label class="col-form-label col-md-4 col-sm-6 ">Department</label>
        <div class="col-md-8 col-sm-6 ">
          <?= renderDepartmentCombo("cbDepartment"); ?>
        </div>
      </div>
    </div>
  </div>
</div>