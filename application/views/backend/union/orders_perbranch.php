<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Orders Per Branch</h2>
    </header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Total Orders</th>
                        <th>Total Quantity</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branch_orders as $row): ?>
                        <tr>
                            <td><?php echo html_escape($row['branch_name']); ?></td>
                            <td><?php echo (int)$row['total_orders']; ?></td>
                            <td><?php echo (int)$row['total_quantity']; ?></td>
                            <td><?php echo number_format((float)$row['total_amount'], 2); ?></td>
                            <td><?php echo number_format((float)$row['paid_amount'], 2); ?></td>
                            <td><?php echo number_format((float)$row['total_amount'] - (float)$row['paid_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
