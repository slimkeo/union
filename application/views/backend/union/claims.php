<div class="row">
    <div class="col-md-12">

        <!---CONTROL TABS START-->
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#list" data-toggle="tab"><i class="fa fa-list"></i> 
                        <?php echo get_phrase('all_claims');?>
                    </a>
                </li>
                <li>
                    <a href="#add" data-toggle="tab"><i class="fa fa-plus-circle"></i>
                        <?php echo get_phrase('new_claim'); ?>
                    </a>
                </li>
            </ul>
        </div>
        <!---CONTROL TABS END-->

        <div class="tab-content">
            <br>

            <!-- TABLE LISTING STARTS -->
            <div class="tab-pane box active" id="list">
                <table class="table table-bordered table-striped mb-none" id="datatable-tabletools">
                    <thead>
                        <tr>
                            <th><div>#</div></th>
                            <th><div><?php echo get_phrase('member');?></div></th>
                            <th><div><?php echo get_phrase('nominee');?></div></th>
                            <th><div><?php echo get_phrase('amount');?></div></th>
                            <th><div><?php echo get_phrase('claim_date');?></div></th>
                            <th><div><?php echo get_phrase('status');?></div></th>
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        if (!empty($claims)):
                            foreach ($claims as $row): 
                                // Get member name
                                $member = $this->db->get_where('members', array('id' => $row['member_id']))->row();
                                $member_name = $member ? $member->surname . ' ' . $member->name : '-';
                                
                                // Get nominee name
                                $nominee_name = '—';
                                if (!empty($row['nominee_id'])) {
                                    $nominee = $this->db->get_where('nominee', array('id' => $row['nominee_id']))->row();
                                    $nominee_name = $nominee ? $nominee->fullname : '—';
                                }
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($member_name); ?></td>
                            <td><?php echo htmlspecialchars($nominee_name); ?></td>
                            <td><?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($row['claim_date'])); ?></td>
                            <td>
                                <span class="label label-<?php 
                                    if($row['status'] == 'PENDING') echo 'warning';
                                    elseif($row['status'] == 'APPROVED') echo 'success';
                                    elseif($row['status'] == 'REJECTED') echo 'danger';
                                    elseif($row['status'] == 'PAID') echo 'info';
                                ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo base_url(); ?>index.php?union/claims/view/<?php echo $row['id'];?>" 
                                   class="btn btn-xs btn-info" target="_blank" data-toggle="tooltip" title="<?php echo get_phrase('view_claim');?>">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <a href="<?php echo base_url(); ?>index.php?union/print_claims_details/<?php echo $row['id'];?>" 
                                   class="btn btn-xs btn-info" target="_blank" data-toggle="tooltip" title="Print Claim">
                                    <i class="fa fa-print"></i>
                                </a>

                                <a href="#" class="btn btn-xs btn-danger" data-toggle="tooltip" title="<?php echo get_phrase('delete');?>"
                                   onClick="confirm_modal('<?php echo base_url();?>index.php?union/claims/delete/<?php echo $row['id'];?>');">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <?php echo get_phrase('no_claims_found'); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- TABLE LISTING ENDS -->


            <!-- CREATION FORM STARTS -->
            <div class="tab-pane box" id="add" style="padding: 5px">
                <div class="box-content">
                    <?php echo form_open(base_url() . 'index.php?union/claims/create' , array(
                        'class' => 'form-horizontal form-bordered validate',
                        'enctype'=>'multipart/form-data'
                    )); ?>

                    <!-- SEARCH MEMBER -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Search Member <span class="required">*</span></label>
                        <div class="col-md-7">
                            <div style="position: relative;">
                                <input type="text" class="form-control" id="member_search" placeholder="Search by ID, Name, Employee No" required>
                                <small class="form-text text-muted">Start typing to search for member</small>
                                <div id="member_search_results" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SELECTED MEMBER INFO -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Selected Member</label>
                        <div class="col-md-7">
                            <div class="alert alert-info" id="member_info" style="display: none;">
                                <strong>ID Number:</strong> <span id="member_idnumber"></span><br>
                                <strong>Name:</strong> <span id="member_name"></span><br>
                                <strong>Cell Number:</strong> <span id="member_cell"></span>
                            </div>
                            <input type="hidden" id="selected_member_id" name="member_id" value="">
                        </div>
                    </div>

                    <!-- NOMINEE SELECTION (always visible now) -->
                    <div class="form-group" id="nominee_group">
                        <label class="col-md-3 control-label">Nominee <span class="required">*</span></label>
                        <div class="col-md-7">
                            <select class="form-control" id="nominee_select" name="nominee_id" required>
                                <option value="">-- Select Nominee --</option>
                            </select>
                            <small class="form-text text-muted">Select the nominee making the claim</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">National ID <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="national_id" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Mortuary <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="mortuary" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Date of Entry <span class="required">*</span></label>
                        <div class="col-md-7">
                        <div class="input-daterange input-group" data-plugin-datepicker>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="date_of_entry" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('place_of_burial');?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="place_of_burial" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('date_of_burial');?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <div class="input-daterange input-group" data-plugin-datepicker>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="date_of_burial" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('bank');?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <select name="bank" id="bank_select" class="form-control" required>
                                <option value="">-- Select Bank --</option>
                                <?php foreach ($enum_banks as $value): ?>
                                    <option value="<?= $value ?>"><?= ucfirst($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('account_number');?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="account" required min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('amount');?> <span class="required">*</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="amount" required step="0.01" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('claim_date');?></label>
                        <div class="col-md-7">
                            <div class="input-daterange input-group" data-plugin-datepicker>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="claim_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Claim Documents</label>
                        <div class="col-md-7">
                            <div id="documents_container">
                                <div class="document-upload-row">
                                    <select class="form-control" name="document_description[]" required>
                                        <option value="">-- Select Document Type --</option>
                                        <option value="ID OF UNION MEMBER">ID OF UNION MEMBER</option>
                                        <option value="ID OF NOMINEE">ID OF NOMINEE</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Death Certificate">Death Certificate</option>
                                        <option value="Payslip">Payslip</option>
                                    </select>
                                    <input type="file" class="form-control" name="document_file[]" required style="margin-top:5px;">
                                    <button type="button" class="btn btn-danger btn-xs remove-document" style="margin-top:5px; display:none;"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-info btn-xs" id="add_document_btn" style="margin-top:10px;">
                                <i class="fa fa-plus"></i> Add Document
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo get_phrase('notes');?></label>
                        <div class="col-md-7">
                            <textarea class="form-control" name="notes" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-primary"><?php echo get_phrase('add_claim');?></button>
                        </div>
                    </div>

                    </form>
                </div>
            </div>
            <!-- CREATION FORM ENDS -->

        </div>
    </div>
</div>


<script>
$(document).ready(function() {

    // Member search
    $('#member_search').keyup(function() {
        var search = $(this).val().trim();
        if (search.length < 2) {
            $('#member_search_results').hide();
            $('#member_info').hide();
            $('#selected_member_id').val('');
            $('#nominee_select').html('<option value="">-- Select Nominee --</option>');
            return;
        }

        $.ajax({
            url: '<?php echo base_url('index.php?union/search_members');?>',
            method: 'POST',
            data: {search: search},
            dataType: 'json',
            success: function(response) {
                if (response.success && response.members.length > 0) {
                    var html = '';
                    $.each(response.members, function(i, m) {
                        var name = m.surname + ' ' + m.name;
                        html += '<div class="member-search-result" style="padding:10px 15px;border-bottom:1px solid #eee;cursor:pointer;" ' +
                                'data-member-id="' + m.id + '" ' +
                                'data-idnumber="' + m.idnumber + '" ' +
                                'data-surname="' + m.surname + '" ' +
                                'data-name="' + m.name + '" ' +
                                'data-cell="' + (m.cellnumber||'—') + '">' +
                                '<strong>' + name + '</strong><br>' +
                                '<small>ID: ' + m.idnumber +'</small>' +
                                '</div>';
                    });
                    $('#member_search_results').html(html).show();

                    $('.member-search-result').click(function() {
                        var id = $(this).data('member-id');
                        $('#member_search').val($(this).find('strong').text());
                        $('#member_idnumber').text($(this).data('idnumber'));
                        $('#member_name').text($(this).data('surname') + ' ' + $(this).data('name'));
                        $('#member_cell').text($(this).data('cell'));
                        $('#selected_member_id').val(id);
                        $('#member_info').show();

                        // Load nominees
                        loadNominees(id);

                        $('#member_search_results').hide();
                    });
                } else {
                    $('#member_search_results').html('<div style="padding:12px;text-align:center;color:#999;">No members found</div>').show();
                    $('#member_info').hide();
                    $('#nominee_select').html('<option value="">-- Select Nominee --</option>');
                }
            }
        });
    });

    function loadNominees(memberId) {
        $.ajax({
            url: '<?php echo base_url('index.php?union/get_nominees');?>',
            method: 'POST',
            data: {member_id: memberId},
            dataType: 'json',
            success: function(res) {
                var opts = '<option value="">-- Select Nominee --</option>';
                if (res.success && res.nominees && res.nominees.length > 0) {
                    $.each(res.nominees, function(i, n) {
                        opts += '<option value="' + n.id + '">' + n.fullname + ' (' + (n.relationship || '?') + ')</option>';
                    });
                } else {
                    opts += '<option value="">No nominees found</option>';
                }
                $('#nominee_select').html(opts);
            },
            error: function() {
                $('#nominee_select').html('<option value="">Error loading nominees</option>');
            }
        });
    }

    // Add / remove document rows
    $('#add_document_btn').click(function() {
        var row = $('<div class="document-upload-row"></div>');
        row.append('<select class="form-control" name="document_description[]" required>' +
            '<option value="">-- Select Document Type --</option>' +
            '<option value="ID OF UNION MEMBER">ID OF UNION MEMBER</option>' +
            '<option value="ID OF NOMINEE">ID OF NOMINEE</option>' +
            '<option value="Passport">Passport</option>' +
            '<option value="Death Certificate">Death Certificate</option>' +
            '<option value="Payslip">Payslip</option>' +
            '</select>');
        row.append('<input type="file" class="form-control" name="document_file[]" required style="margin-top:5px;">');
        row.append('<button type="button" class="btn btn-danger btn-xs remove-document" style="margin-top:5px;"><i class="fa fa-trash"></i></button>');
        $('#documents_container').append(row);
        $('.remove-document').show();
    });

    $(document).on('click', '.remove-document', function() {
        $(this).closest('.document-upload-row').remove();
        if ($('#documents_container .document-upload-row').length <= 1) {
            $('.remove-document').hide();
        }
    });

    // Hide remove button on first row initially
    if ($('#documents_container .document-upload-row').length === 1) {
        $('.remove-document').hide();
    }

    // Close search dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#member_search, #member_search_results').length) {
            $('#member_search_results').hide();
        }
    });
});
</script>