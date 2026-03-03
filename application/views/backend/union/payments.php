<div class="row">
    <div class="col-md-12">

        <!---CONTROL TABS START-->
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#add_subscription" data-toggle="tab"><i class="fa fa-plus-circle"></i>
                        <?php echo 'Add Subscription Payment'; ?>
                    </a>
                </li>
            </ul>
        </div>
        <!---CONTROL TABS END-->

        <div class="tab-content">
            <br>

            <!-- ADD SUBSCRIPTION FORM STARTS -->
            <div class="tab-pane box active" id="add_subscription" style="padding: 15px">
                <div class="box-content">

                    <?php echo form_open(base_url() . 'index.php?union/add_subscription',
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

                    <!-- BULK YEAR SELECTION -->
                    <div class="form-group" id="year_group" style="display: none;">
                        <label class="col-md-3 control-label">Pay Full Year</label>
                        <div class="col-md-7">
                            <div class="radio">
                                <label><input type="radio" name="bulk_year" value="" checked> None (select individual months)</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="bulk_year" value="2025"> Pay all available months of 2025</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="bulk_year" value="2026"> Pay all available months of 2026 (current year)</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="bulk_year" value="2027"> Pay all available months of 2027 (next year)</label>
                            </div>
                            <small class="form-text text-muted">Selecting a year will automatically select all matching months of that year (if any are still unpaid).</small>
                        </div>
                    </div>

                    <!-- SELECT MONTHS -->
                    <div class="form-group" id="months_group" style="display: none;">
                        <label class="col-md-3 control-label">Select Months <span style="color: red;">*</span></label>
                        <div class="col-md-7">
                            <div style="margin-bottom: 10px;">
                                <input type="text" id="month_search" class="form-control" 
                                       placeholder="Search months (e.g., 'January 2026' or '2026-01')" 
                                       style="display: none;">
                                <small class="form-text text-muted" id="month_search_hint" style="display: none;">Type to filter months</small>
                            </div>

                            <div id="months_container" style="border: 1px solid #ddd; padding: 12px; border-radius: 4px; max-height: 320px; overflow-y: auto; background:#f9f9f9;">
                                <p style="text-align:center; color:#888; margin:30px 0;">Loading unpaid months...</p>
                            </div>
                            <small class="form-text text-muted">Only unpaid / unsubscribed months are shown.</small>
                        </div>
                    </div>

                    <!-- AMOUNT PER MONTH -->
                    <div class="form-group" id="amount_group" style="display: none;">
                        <label class="col-md-3 control-label">Amount Per Month <span style="color: red;">*</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" id="amount_per_month" name="amount_per_month"  placeholder="0.00" required>
                            <small class="form-text text-muted">Enter the amount per month (E4.167 for E50.00 per year)</small>
                        </div>
                    </div>

                    <!-- TOTAL AMOUNT -->
                    <div class="form-group" id="total_group" style="display: none;">
                        <label class="col-md-3 control-label">Total Amount</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="total_amount" readonly value="E 0.00">
                        </div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group" id="description_group" style="display: none;">
                        <label class="col-md-3 control-label">Description</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="description" value="Subscription Payment" readonly>
                        </div>
                    </div>

                    <!-- SOURCE -->
                    <div class="form-group" id="source_group" style="display: none;">
                        <label class="col-md-3 control-label">Source <span style="color: red;">*</span></label>
                        <div class="col-md-7">
                            <select name="source" id="source_select" class="form-control" required>
                                <option value="">-- Select Source --</option>
                                <?php foreach ($source_enum as $value): ?>
                                    <option value="<?= $value ?>"><?= ucfirst($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                                <i class="fa fa-check"></i> Add Subscription
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

    // ──────────────────────────────
    // Load unpaid months
    // ──────────────────────────────
    function load_available_months(member_id) {
        $.ajax({
            url: "<?= base_url('index.php?union/get_available_months') ?>",
            method: 'POST',
            data: { member_id },
            dataType: 'json',
            success: function(res) {
                $('#months_container').empty();
                $('#month_search').val('').show();
                $('#month_search_hint').show();

                if (!res.success || !res.months?.length) {
                    $('#months_container').html('<p style="text-align:center;color:#888;margin:40px 0;">No unpaid months found</p>');
                    calculate_total();
                    return;
                }

                let html = '';
                res.months.forEach(m => {
                    html += `<div class="month-item" 
                                 data-value="${m.value}" 
                                 data-label="${m.label}" 
                                 data-year="${m.year || ''}">
                                 <label style="display:block; margin:6px 0;">
                                     <input type="checkbox" name="months[]" value="${m.value}" class="month-checkbox">
                                     ${m.label}
                                 </label>
                             </div>`;
                });
                $('#months_container').html(html);

                $('#months_container').off('change', '.month-checkbox');
                $('#months_container').on('change', '.month-checkbox', function() {
                    calculate_total();
                });

                calculate_total();
            },
            error: function() {
                $('#months_container').html('<p style="text-align:center;color:#c00;margin:40px 0;">Error loading months</p>');
            }
        });
    }

    // ──────────────────────────────
    // Bulk year logic – VISUAL disable only
    // ──────────────────────────────
    $('input[name="bulk_year"]').on('change', function() {
        const year = this.value;

        // Reset all items first
        $('.month-item')
            .css({
                'opacity': '1',
                'background-color': '',
                'pointer-events': 'auto'
            })
            .find('label').css('cursor', 'default');

        $('.month-checkbox').prop('disabled', false); // always enabled

        if (!year) {
            // None selected → uncheck everything
            $('.month-checkbox').prop('checked', false);
            calculate_total();
            return;
        }

        let count = 0;
        $('.month-checkbox').each(function() {
            const $item = $(this).closest('.month-item');
            let itemYear = $item.data('year') || '';

            // Fallback: extract year from value (2026-03 → 2026)
            if (!itemYear) {
                const val = $item.data('value') || '';
                if (val.includes('-')) itemYear = val.split('-')[0];
            }
            // Fallback: extract from label
            if (!itemYear) {
                const lbl = $item.data('label') || '';
                const match = lbl.match(/\d{4}/);
                if (match) itemYear = match[0];
            }

            if (String(itemYear) === String(year)) {
                $(this).prop('checked', true);
                $item.css({
                    'opacity': '1',
                    'background-color': '#f0f8ff'
                });
                $item.find('label').css('cursor', 'pointer');
                count++;
            } else {
                $(this).prop('checked', false);
            }
        });

        calculate_total();

        if (count === 0) {
            alert(`No unpaid months available for year ${year}`);
        }
    });

    // Month search filter
    $('#month_search').on('input', function() {
        const term = $(this).val().toLowerCase().trim();
        $('.month-item').each(function() {
            const lbl = $(this).data('label')?.toLowerCase() || '';
            const val = $(this).data('value')?.toLowerCase() || '';
            $(this).toggle(lbl.includes(term) || val.includes(term));
        });
    });

    // ──────────────────────────────
    // Total calculation
    // ──────────────────────────────
    function calculate_total() {
        const count = $('.month-checkbox:checked').length;
        const amt = parseFloat($('#amount_per_month').val()) || 0;
        const total = count * amt;
        $('#total_amount').val('E ' + total.toFixed(2));
    }

    $('#amount_per_month').on('input change', calculate_total);

    // Close search dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#member_search, #member_search_results').length) {
            $('#member_search_results').hide();
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        if (!$('#selected_member_id').val()) {
            alert('Please select a member first');
            e.preventDefault();
            return;
        }
        if ($('.month-checkbox:checked').length === 0) {
            alert('Please select at least one month');
            e.preventDefault();
            return;
        }
        if (parseFloat($('#amount_per_month').val() || 0) <= 0) {
            alert('Amount per month must be greater than 0');
            e.preventDefault();
            return;
        }
        if (!$('#source_select').val()) {
            alert('Please select payment source');
            e.preventDefault();
            return;
        }
    });
});
</script>