<div class="row">
    <div class="col-md-5">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Upload Orders Spreadsheet</h2>
            </header>
            <div class="panel-body">
                <p class="text-muted">CSV columns: member_id, branch_id, merchandise_id, size, quantity</p>
                <form method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>index.php?union/upload_merchendise_orders">
                    <div class="form-group">
                        <label>Branch (for log)</label>
                        <select name="branch_id" class="form-control">
                            <option value="">Select branch</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?php echo $branch['id']; ?>"><?php echo html_escape($branch['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>CSV File</label>
                        <input type="file" name="orders_file" accept=".csv" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </section>
    </div>
    <div class="col-md-7">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Upload Logs</h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>File</th>
                                <th>Total Rows</th>
                                <th>Success</th>
                                <th>Failed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upload_logs as $log): ?>
                                <tr>
                                    <td><?php echo html_escape($log['created_at']); ?></td>
                                    <td><?php echo html_escape($log['file_name']); ?></td>
                                    <td><?php echo (int)$log['total_rows']; ?></td>
                                    <td><?php echo (int)$log['successful_rows']; ?></td>
                                    <td><?php echo (int)$log['failed_rows']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>