<div class="row">
    <div class="col-md-12">

        <!---CONTROL TABS START-->
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#add_sasa" data-toggle="tab"><i class="fa fa-plus-circle"></i>
                        <?php echo 'Add Subscription Payment'; ?>
                    </a>
                </li>
            </ul>
        </div>
        <!---CONTROL TABS END-->

        <div class="tab-content">
            <br>

            <!-- ADD SUBSCRIPTION FORM STARTS -->
            <div class="tab-pane box active" id="add_sasa" style="padding: 15px">
                <div class="box-content">

                    <?php echo form_open(base_url() . 'index.php?union/sasa/add_sasa',
                    array('class' => 'form-horizontal form-bordered validate')); ?>

                    <!-- SEARCH MEMBER -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Search Member <span style="color: red;">*</span></label>
                        <div class="col-md-7">
                            <div style="position: relative;">
                                <input type="text" class="form-control" id="member_search" 
                                       placeholder="Search by ID Number, Name, or Employee No" required>
                                <small class="form-text text-muted">Start typing to search for member</small>
                                
                                <div id="member_search_results" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px; max-height: 300px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                </div>
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
                            <input type="hidden" id="selected_member_id" name="selected_member_id" value="">
                        </div>
                    </div>


                    <!-- AMOUNT PER MONTH -->
                    <div class="form-group" id="amount_group" style="display: none;">
                        <label class="col-md-3 control-label">Amount Per Month <span style="color: red;">*</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" id="amount_per_month" name="amount_per_month"  placeholder="0.00" readonly>
                        </div>
                    </div>
                    <!-- SUBMIT BUTTON -->
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                                <i class="fa fa-check"></i> Add SASA Member
                            </button>
                            <button type="reset" class="btn btn-default">Clear</button>
                        </div>
                    </div>

                    </form>
                </div>
            </div>
            <!-- ADD SUBSCRIPTION FORM ENDS -->

        </div>
    </div>
</div>


<script>
$(document).ready(function() {

    // ──────────────────────────────
    // Member search & selection
    // ──────────────────────────────
    $('#member_search').on('input', function() {
        const search = $(this).val().trim();
        if (search.length < 2) {
            resetUI();
            return;
        }

        $.ajax({
            url: "<?= base_url('index.php?union/search_members') ?>",
            method: 'POST',
            data: { search },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.members?.length > 0) {
                    let html = '';
                    res.members.forEach(m => {
                        html += `<div class="member-result" style="padding:10px;border-bottom:1px solid #eee;cursor:pointer;"
                                    data-id="${m.id}" data-idnumber="${m.idnumber}" data-name="${m.surname} ${m.name}" data-cell="${m.cellnumber || ''}">
                                    <strong>${m.surname} ${m.name}</strong><br>
                                    <small>ID: ${m.idnumber} • Emp: ${m.employeeno || 'N/A'}</small>
                                </div>`;
                    });
                    $('#member_search_results').html(html).show();

                    $('.member-result').click(function() {
                        const name = $(this).data('name');
                        $('#member_search').val(name);
                        $('#member_idnumber').text($(this).data('idnumber'));
                        $('#member_name').text(name);
                        $('#member_cell').text($(this).data('cell'));
                        $('#selected_member_id').val($(this).data('id'));
                        $('#member_info').show();

                        $('#year_group, #months_group, #amount_group, #total_group, #description_group, #source_group').show();

                        load_available_months($(this).data('id'));

                        $('#submit_btn').prop('disabled', false);
                        $('#member_search_results').hide();
                    });
                } else {
                    $('#member_search_results').html('<div style="padding:12px;text-align:center;color:#777;">No members found</div>').show();
                }
            }
        });
    });

    function resetUI() {
        $('#member_search_results').hide();
        $('#member_info').hide();
        $('#year_group, #months_group, #amount_group, #total_group, #description_group, #source_group').hide();
        $('#selected_member_id').val('');
        $('#months_container').html('<p style="text-align:center;color:#888;margin:40px 0;">Select a member first</p>');
        $('#submit_btn').prop('disabled', true);
    }


});
</script>