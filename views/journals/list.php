<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="row" style="display: block;">

      <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>Journals</h2>
            <ul class="nav navbar-right panel_toolbox">
              <li>
                <button onclick="window.location='<?=App::$config['documentRoot'];?>journals/entry';" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i>
                    New Journal
                </button>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped jambo_table" border="1">
                <thead>
                  <tr class="headings">
                    <th width="10" class="column-title">SL </th>
                    <th width="15%" class="column-title">Voucher Date </th>
                    <th width="15%" class="column-title">Voucher Number </th>
                    <th width="15%" class="column-title">Voucher Ref.No </th>
                    <th width="30%" class="column-title">Company Name </th>
                    <th width="15%" class="column-title text-right">Voucher Amount </th>
                    <th width="10%" class="column-title no-link last text-center"><span class="nobr">Action</span></th>
                  </tr>
                </thead>

                <tbody>

                <?php 
                  if(is_array($journals) && count($journals) > 0) {
                    foreach($journals as $idx => $journal) { ?>
                  <tr class="pointer">
                    <td class="gCellEx "><?=$idx+1;?></td>
                    <td class="gCellEx "><?=$journal['voucher_date'];?></td>
                    <td class="gCellEx "><?=$journal['voucher_no'];?></td>
                    <td class="gCellEx "><?=$journal['voucher_ref_no'];?></td>
                    <td class="gCellEx "><?=$journal['company_name'];?></td>
                    <td class="gCellEx text-right"><?=$journal['voucher_amt'];?>/-</td>
                    <td class="gCellEx text-center last">
                      <a href="<?= App::$config['documentRoot']; ?>journals/entry?journal_id=<?=1;?>" class="btn btn-primary btn-sm"> 
                        <i class="fa fa-edit"></i> </a>
                      <a href="#" class="btn btn-primary btn-sm" onclick="promptForDeletion(this, <?=$journal['id'];?>, '<?=$journal['voucher_no'];?>', '<?=$journal['voucher_date'];?>');"> 
                        <i class="fa fa-trash"></i> </a>
                    </td>
                  </tr>
                <?php
                    }
                  } else {
                    ?><tr class="pointer"><td colspan="7"><span class="text-danger h5">No journal records found...</span></td><?php
                  } ?>

                </tbody>
              </table>
            </div>
                    
                
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<script>
  function promptForDeletion(sender, id, voucher_no, voucher_date) {
    if(!confirm("Delete voucher: " + voucher_no + ", dt." + formatDate(voucher_date) + "?")) {
      return false;
    }

    showButtonProgress(sender);
    
    $.ajax({
      url: '<?=App::$config["documentRoot"];?>journals/delete',
      type: 'POST',
      data: {
        'id': id,    			
      },
      dataType: 'json',
      success: function(response) {
        if(response.result) {
          window.location.reload();
        } else {
          hideButtonProgress(sender);
          alert(response.message);
        }
      }

    });
  }
</script>