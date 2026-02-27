<?php
$edit_data = $this->db->get_where('branches', array('id' => $param1))->result_array();
foreach ($edit_data as $row):

    // Fetch current managed_by member details
    $current_member = null;
    if (!empty($row['managed_by'])) {
        $current_member = $this->db->get_where('members', array('id' => $row['managed_by']))->row_array();
    }

?>

<div class="row">
    <div class="col-md-12">
        <section class="panel">

            <?php echo form_open(base_url() . 'index.php?union/branches/do_update/'.$param1, 
                array('class' => 'form-horizontal form-bordered validate','target'=>'_top', 'enctype'=>'multipart/form-data'));?>

            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-pencil-square"></i>
                    Edit Branch: <?php echo $row['name']; ?>
                </h4>
            </div>

            <div class="panel-body">

                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo get_phrase('name');?>
                    </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="name" required value="<?php echo $row['name']; ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo get_phrase('managed_by'); ?> <span class="required">*</span>
                    </label>

                    <div class="col-md-7 position-relative">

                        <!-- Hidden field – holds the ID that gets submitted -->
                        <input type="hidden" name="managed_by" id="selected_member_id" 
                               value="<?php echo htmlspecialchars($row['managed_by'] ?? ''); ?>" required>

                        <!-- Search/display field – pre-filled with current value -->
                        <input type="text" id="managed_by_search" class="form-control" 
                               placeholder="Search by surname, name, ID number, employee no..." 
                               autocomplete="off" 
                               value="<?php 
                                   echo !empty($current_member) 
                                       ? htmlspecialchars($current_member['surname'] . ' ' . $current_member['name']) 
                                       : ''; 
                               ?>" required>

                        <!-- Results container -->
                        <div id="managed_by_results" style="display:none; 
                             position:absolute; z-index:1000; width:100%; max-height:300px; 
                             overflow-y:auto; background:#fff; border:1px solid #ccc; 
                             box-shadow:0 4px 8px rgba(0,0,0,0.1); margin-top:2px;">
                        </div>

                        <!-- Selected info block – visible if there's a current value -->
                        <div id="selected_member_info" style="display:<?php echo !empty($current_member) ? 'block' : 'none'; ?>; 
                             margin-top:8px; padding:8px; background:#f8f9fa; border:1px solid #ddd;">
                            <strong>Selected:</strong> 
                            <span id="selected_name">
                                <?php echo !empty($current_member) ? htmlspecialchars($current_member['surname'] . ' ' . $current_member['name']) : ''; ?>
                            </span><br>
                            <small>
                                ID: <span id="selected_idnumber"><?php echo !empty($current_member) ? htmlspecialchars($current_member['idnumber'] ?? 'N/A') : ''; ?></span> 
                                • Cell: <span id="selected_cell"><?php echo !empty($current_member) ? htmlspecialchars($current_member['cellnumber'] ?? 'N/A') : ''; ?></span>
                            </small>
                        </div>

                    </div>
                </div>

            </div>

            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary">UPDATE BRANCH</button>
                    </div>
                </div>
            </footer>

            <?php echo form_close(); ?>
        </section>
    </div>
</div>

<script>
$(document).ready(function() {

// Hide results when clicking outside (delegated version)
$(document).on('click', function(e) {
    if (!$(e.target).closest('#managed_by_search, #managed_by_results').length) {
        $('#managed_by_results').hide();
    }
});

// Live search – delegated + both events
$(document).on('input keyup', '#managed_by_search', function() {
    var search = $(this).val().trim();

    // Hide preview if user is changing away from current selection
    if (search !== $('#selected_name').text().trim()) {
        $('#selected_member_info').hide();
        // $('#selected_member_id').val('');   ← uncomment if you want to force re-pick
    }

    if (search.length < 2) {
        $('#managed_by_results').html('').hide();
        return;
    }

    $.ajax({
        url: "<?= base_url('index.php?union/search_members') ?>",
        method: 'POST',
        data: { search: search },
        dataType: 'json',
        success: function(res) {
            $('#managed_by_results').html('');

            if (res.success && res.members && res.members.length > 0) {
                let html = '';
                res.members.forEach(function(m) {
                    html += `<div class="member-result" style="padding:10px; border-bottom:1px solid #eee; cursor:pointer;"
                                data-id="${m.id}" 
                                data-idnumber="${m.idnumber || ''}" 
                                data-name="${m.surname} ${m.name}" 
                                data-cell="${m.cellnumber || ''}">
                                <strong>${m.surname} ${m.name}</strong><br>
                                <small>ID: ${m.idnumber || 'N/A'} • Emp: ${m.employeeno || 'N/A'} • Cell: ${m.cellnumber || 'N/A'}</small>
                            </div>`;
                });
                $('#managed_by_results').html(html).show();
            } else {
                $('#managed_by_results').html('<div style="padding:12px; text-align:center; color:#777;">No members found</div>').show();
            }
        },
        error: function(xhr, status, err) {
            console.error('Search failed:', status, err, xhr.responseText);
            $('#managed_by_results').html('<div style="padding:12px; text-align:center; color:#d00;">Error – check console (F12)</div>').show();
        }
    });
});

// Select from results – also delegated
$(document).on('click', '#managed_by_results .member-result', function() {
    var name     = $(this).data('name');
    var idnumber = $(this).data('idnumber');
    var cell     = $(this).data('cell');
    var id       = $(this).data('id');

    $('#selected_member_id').val(id);
    $('#selected_name').text(name);
    $('#selected_idnumber').text(idnumber);
    $('#selected_cell').text(cell);
    $('#selected_member_info').show();

    $('#managed_by_search').val(name);
    $('#managed_by_results').hide();
});

});
</script>

<?php endforeach; ?>