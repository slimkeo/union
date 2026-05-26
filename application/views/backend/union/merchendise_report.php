<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Merchandise Report Summary</h2>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3"><strong>Total Orders:</strong> <?php echo (int)($totals['total_orders'] ?? 0); ?></div>
                    <div class="col-md-3"><strong>Total Value:</strong> <?php echo number_format((float)($totals['total_order_value'] ?? 0), 2); ?></div>
                    <div class="col-md-3"><strong>Paid Value:</strong> <?php echo number_format((float)($totals['total_paid_value'] ?? 0), 2); ?></div>
                    <div class="col-md-3"><strong>Collected Orders:</strong> <?php echo (int)($totals['collected_count'] ?? 0); ?></div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Ordered Qty</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overview as $row): ?>
                                <tr>
                                    <td><?php echo html_escape($row['item_name']); ?></td>
                                    <td><?php echo html_escape($row['item_code']); ?></td>
                                    <td><?php echo number_format((float)$row['price'], 2); ?></td>
                                    <td><?php echo (int)$row['stock_qty']; ?></td>
                                    <td><?php echo (int)$row['qty_ordered']; ?></td>
                                    <td><?php echo number_format((float)$row['total_sales'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
