<?php
$edit_data = $this->db->get_where('admin', array('id' => $param2))->result_array();
foreach ($edit_data as $row):
?>
  <div class="row">
    <div class="col-md-12">
      <section class="panel">

        <?php echo form_open(base_url() . 'index.php?union/manage_users/do_update/'.$row['id'], array('class' => 'form-horizontal form-bordered', 'target' => '_top', 'id' => 'form', 'enctype' => 'multipart/form-data'));?>

        <div class="panel-heading">
          <h4 class="panel-title">
                <i class="fa fa-pencil-square"></i>
          <?php echo get_phrase('edit_user') . ": " . htmlspecialchars($row['name']);?>
              </h4>
        </div>

        <div class="panel-body">
          <!-- NATIONAL ID -->
          <div class="form-group">
            <label class="col-md-3 control-label"><?php echo get_phrase('national_id');?></label>
            <div class="col-md-7">
              <input type="text" class="form-control" name="national_id" value="<?php echo htmlspecialchars($row['national_id'] ?? '');?>" required/>
            </div>
          </div>

          <!-- NAME (FULLNAME) -->
          <div class="form-group">
            <label class="col-md-3 control-label"><?php echo get_phrase('fullname');?></label>
            <div class="col-md-7">
              <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name'] ?? '');?>" required/>
            </div>
          </div>

          <!-- EMAIL -->
          <div class="form-group">
            <label class="col-md-3 control-label"><?php echo get_phrase('email');?></label>
            <div class="col-md-7">
              <input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email'] ?? '');?>" required/>
            </div>
          </div>

          <!-- LEVEL (ADMIN PRIVILEGES) -->
          <div class="form-group">
            <label class="col-md-3 control-label"><?php echo get_phrase('admin_previleges');?></label>
            <div class="col-md-7">
              <select name="level" data-plugin-selectTwo data-minimum-results-for-search="Infinity" data-width="100%" class="form-control populate" required>
                <option value=""><?php echo get_phrase('select');?></option>
                <option value="1" <?php echo (isset($row['level']) && $row['level'] == 1) ? 'selected' : ''; ?>><?php echo get_phrase('admin');?></option>
                <option value="2" <?php echo (isset($row['level']) && $row['level'] == 2) ? 'selected' : ''; ?>><?php echo get_phrase('clerk');?></option>
                <option value="3" <?php echo (isset($row['level']) && $row['level'] == 3) ? 'selected' : ''; ?>><?php echo get_phrase('finance');?></option>
              </select>
            </div>
          </div>

        </div>
        <footer class="panel-footer">
          <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
              <button type="submit" class="btn btn-primary"><?php echo get_phrase('update');?></button>
            </div>
          </div>
        </footer>
        <?php echo form_close();?>
      </section>
    </div>
  </div>

<?php
endforeach;
?>
