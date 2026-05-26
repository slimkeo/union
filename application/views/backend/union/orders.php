<div class="row">
    <div class="col-md-4">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Create Order</h2>
            </header>
            <div class="panel-body">
                <form method="post" action="<?php echo base_url(); ?>index.php?union/orders/create">
                    <div class="form-group">
                        <label>Member</label>
                        <select name="member_id" class="form-control" required>
                            <option value="">Select member</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>"><?php echo html_escape($member['surname'] . ' ' . $member['name'] . ' (' . $member['idnumber'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Branch</label>
                        <select name="branch_id" class="form-control" required>
                            <option value="">Select branch</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?php echo $branch['id']; ?>"><?php echo html_escape($branch['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Merchandise</label>
                        <select name="merchandise_id" class="form-control" required>
                            <option value="">Select item</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?php echo $item['id']; ?>"><?php echo html_escape($item['item_name'] . ' - ' . number_format((float)$item['price'], 2)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size</label>
                        <select name="size" class="form-control" required>
                            <option>XS</option><option>S</option><option>M</option><option>L</option>
                            <option>XL</option><option>XXL</option><option>XXXL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" min="1" name="quantity" class="form-control" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Source</label>
                        <select name="source" class="form-control">
                            <option value="member">member</option>
                            <option value="admin" selected>admin</option>
                            <option value="spreadsheet">spreadsheet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ordered By (Member ID)</label>
                        <input type="number" min="1" name="ordered_by" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </form>
            </div>
        </section>
    </div>
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Orders</h2>
            </header>
            <div class="panel-body">
                <form method="get" class="form-inline" action="<?php echo base_url(); ?>index.php?union/orders">
                    <select class="form-control input-sm" name="branch_id">
                        <option value="">All branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id']; ?>" <?php echo ($this->input->get('branch_id') == $branch['id']) ? 'selected' : ''; ?>><?php echo html_escape($branch['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-control input-sm" name="payment_status">
                        <option value="">All payments</option>
                        <option value="unpaid">unpaid</option>
                        <option value="partial">partial</option>
                        <option value="paid">paid</option>
                    </select>
                    <select class="form-control input-sm" name="collection_status">
                        <option value="">All collections</option>
                        <option value="pending">pending</option>
                        <option value="collected">collected</option>
                    </select>
                    <button class="btn btn-sm btn-default" type="submit">Filter</button>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Member</th>
                                <th>Branch</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Size</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Collection</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo html_escape($order['order_number']); ?></td>
                                    <td><?php echo html_escape(trim($order['member_surname'] . ' ' . $order['member_name'])); ?></td>
                                    <td><?php echo html_escape($order['branch_name']); ?></td>
                                    <td><?php echo html_escape($order['item_name']); ?></td>
                                    <td><?php echo (int) $order['quantity']; ?></td>
                                    <td><?php echo html_escape($order['size']); ?></td>
                                    <td><?php echo number_format((float)$order['amount'], 2); ?></td>
                                    <td><?php echo html_escape($order['payment_status']); ?></td>
                                    <td><?php echo html_escape($order['collection_status']); ?></td>
                                    <td>
                                        <a class="btn btn-xs btn-danger" onclick="return confirm('Delete order?');" href="<?php echo base_url(); ?>index.php?union/orders/delete/<?php echo $order['id']; ?>">Delete</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10">
                                        <form class="form-inline" method="post" action="<?php echo base_url(); ?>index.php?union/orders/update/<?php echo $order['id']; ?>">
                                            <select name="payment_status" class="form-control input-sm">
                                                <option value="unpaid" <?php echo ($order['payment_status'] === 'unpaid') ? 'selected' : ''; ?>>unpaid</option>
                                                <option value="partial" <?php echo ($order['payment_status'] === 'partial') ? 'selected' : ''; ?>>partial</option>
                                                <option value="paid" <?php echo ($order['payment_status'] === 'paid') ? 'selected' : ''; ?>>paid</option>
                                            </select>
                                            <select name="collection_status" class="form-control input-sm">
                                                <option value="pending" <?php echo ($order['collection_status'] === 'pending') ? 'selected' : ''; ?>>pending</option>
                                                <option value="collected" <?php echo ($order['collection_status'] === 'collected') ? 'selected' : ''; ?>>collected</option>
                                            </select>
                                            <input type="text" name="notes" class="form-control input-sm" value="<?php echo html_escape($order['notes']); ?>" placeholder="Notes">
                                            <button type="submit" class="btn btn-xs btn-success">Save</button>
                                        </form>
                                        <form class="form-inline" method="post" action="<?php echo base_url(); ?>index.php?union/orders/pay/<?php echo $order['id']; ?>" style="margin-top:6px;">
                                            <input type="number" step="0.01" min="0.01" name="amount_paid" class="form-control input-sm" placeholder="Amount paid" required>
                                            <input type="text" name="payment_method" class="form-control input-sm" placeholder="Method">
                                            <input type="text" name="transaction_ref" class="form-control input-sm" placeholder="Reference">
                                            <input type="datetime-local" name="payment_date" class="form-control input-sm">
                                            <button type="submit" class="btn btn-xs btn-primary">Add Payment</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>