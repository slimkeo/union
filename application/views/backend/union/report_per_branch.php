<?php
$branch      = $this->db->get_where('branches', ['id' => $branch_id])->row_array();
$branch_name = $branch ? $branch['name'] : 'Unknown Branch';
?>

<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    Members in <?php echo htmlspecialchars($branch_name); ?>
                </h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-branch-members">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Number</th>
                                <th>Emp NO</th>
                                <th>Surname</th>
                                <th>Name</th>
                                <th>Cell Number</th>
                                <th>Gender</th>
                                <th>School Code</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    jQuery(function() {
        if (!jQuery.fn.DataTable || !jQuery('#datatable-branch-members').length) {
            return;
        }

        jQuery('#datatable-branch-members').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo base_url('index.php?union/get_members_by_branch'); ?>",
                type: "POST",
                data: function (d) {
                    d.branch_id = "<?php echo (int) $branch_id; ?>";
                }
            },
            pageLength: 25,
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: 'Copy' },
                { extend: 'excel', text: 'Excel' },
                { extend: 'pdf', text: 'PDF' },
                { extend: 'print', text: 'Print' }
            ]
        });
    });
</script>
