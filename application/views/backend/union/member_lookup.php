<?php
$lookup_results = isset($lookup_results) ? $lookup_results : [];
$not_found      = isset($not_found) ? $not_found : [];
?>
<div class="row">
    <div class="col-md-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#upload" data-toggle="tab"><i class="fa fa-upload"></i> Upload Identifier Spreadsheet</a>
                </li>
                <?php if (!empty($lookup_results) || !empty($not_found)): ?>
                <li>
                    <a href="#results" data-toggle="tab"><i class="fa fa-list"></i> Lookup Results</a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="tab-content">
                <br>

                <div class="tab-pane box active" id="upload" style="padding: 15px">
                    <div class="box-content">
                        <div class="alert alert-info">
                            <strong>CSV format:</strong> One column named <code>identifier</code> in the header row.<br>
                            Each row may contain a <strong>cell number</strong> (e.g. <code>76404197</code>, matched against <code>26876404197</code> in the database),
                            an <strong>ID number</strong> (exact match), or a <strong>member ID</strong> (exact match).
                        </div>

                        <?php echo form_open(
                            base_url() . 'index.php?union/member_lookup_do',
                            array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'memberLookupForm')
                        ); ?>

                        <div class="form-group">
                            <label class="col-md-3 control-label">CSV File</label>
                            <div class="col-md-5">
                                <input type="file" name="csv_file" accept=".csv" class="form-control" required id="csvFileInput" />
                                <p class="help-block">Export your spreadsheet as CSV (UTF-8) before uploading.</p>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fa fa-search"></i> Upload &amp; Lookup
                                </button>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>

                <?php if (!empty($lookup_results) || !empty($not_found)): ?>
                <div class="tab-pane box" id="results" style="padding: 15px">
                    <div class="box-content">
                        <?php if (!empty($lookup_results)): ?>
                        <h4>Matched Members (<?php echo count($lookup_results); ?>)</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-none" id="datatable-member-lookup">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Identifier</th>
                                        <th>Member ID</th>
                                        <th>ID Number</th>
                                        <th>Employee No</th>
                                        <th>Surname</th>
                                        <th>Name</th>
                                        <th>Cell Number</th>
                                        <th>Branch</th>
                                        <th>Last Payment Date</th>
                                        <th class="text-right">Last Payment Amount (E)</th>
                                        <th>Payment Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($lookup_results as $row): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo htmlspecialchars($row['identifier']); ?></td>
                                        <td><?php echo '058-' . (int) $row['member_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['idnumber'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['employeeno'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['surname'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['name'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['cellnumber'] ?: 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                                        <td>
                                            <?php
                                            echo !empty($row['last_payment_date'])
                                                ? htmlspecialchars(date('Y-m-d', strtotime($row['last_payment_date'])))
                                                : 'N/A';
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <?php
                                            echo $row['last_payment_amount'] !== null
                                                ? number_format((float) $row['last_payment_amount'], 2)
                                                : 'N/A';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['last_payment_status'] ?: 'N/A'); ?></td>
                                        <td>
                                            <a href="<?php echo base_url('index.php?union/member_details/' . (int) $row['member_id']); ?>"
                                               class="btn btn-xs btn-info">
                                                <i class="fa fa-user"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($not_found)): ?>
                        <h4 style="margin-top: 25px;">Identifiers Not Found (<?php echo count($not_found); ?>)</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-none" id="datatable-member-lookup-not-found">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Identifier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $nf = 1; foreach ($not_found as $identifier): ?>
                                    <tr>
                                        <td><?php echo $nf++; ?></td>
                                        <td><?php echo htmlspecialchars($identifier); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var exportAllModifier = { page: 'all', search: 'applied' };

    function buildExportButtons(exportColumns) {
        var exportOptions = {
            modifier: exportAllModifier,
            columns: exportColumns
        };

        return [
            { extend: 'copy',  text: 'Copy',  exportOptions: exportOptions },
            { extend: 'excel', text: 'Excel', exportOptions: exportOptions },
            { extend: 'pdf',   text: 'PDF',   exportOptions: exportOptions },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: exportOptions,
                action: function (e, dt, button, config) {
                    var oldLength = dt.page.len();
                    var oldPage = dt.page();

                    dt.one('draw', function () {
                        if ($.fn.dataTable.ext.buttons.print && $.fn.dataTable.ext.buttons.print.action) {
                            $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                        } else {
                            window.print();
                        }

                        setTimeout(function () {
                            dt.page.len(oldLength);
                            dt.one('draw', function () {
                                dt.page(oldPage).draw('page');
                            });
                            dt.draw(false);
                        }, 500);
                    });

                    dt.page.len(-1).draw();
                }
            }
        ];
    }

    var tableDefaults = {
        dom: 'Bfrtip',
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'All']]
    };

    if ($.fn.DataTable && $('#datatable-member-lookup').length) {
        $('#datatable-member-lookup').DataTable($.extend({}, tableDefaults, {
            buttons: buildExportButtons(':not(:last-child)')
        }));
    }

    if ($.fn.DataTable && $('#datatable-member-lookup-not-found').length) {
        $('#datatable-member-lookup-not-found').DataTable($.extend({}, tableDefaults, {
            buttons: buildExportButtons(':visible')
        }));
    }

    <?php if (!empty($lookup_results) || !empty($not_found)): ?>
    $('a[href="#results"]').tab('show');
    <?php endif; ?>

    $('#memberLookupForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
    });
});
</script>
