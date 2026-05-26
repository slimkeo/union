<div class="row">
    <div class="col-md-5">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Add Merchandise Item</h2>
            </header>
            <div class="panel-body">
                <form method="post" action="<?php echo base_url(); ?>index.php?union/merchendise/create">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Item Code</label>
                        <input type="text" name="item_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" min="0" name="stock_qty" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </form>
            </div>
        </section>
    </div>
    <div class="col-md-7">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Merchandise Items</h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo html_escape($item['item_name']); ?></td>
                                    <td><?php echo html_escape($item['item_code']); ?></td>
                                    <td><?php echo number_format((float)$item['price'], 2); ?></td>
                                    <td><?php echo (int)$item['stock_qty']; ?></td>
                                    <td><?php echo ((int)$item['status'] === 1) ? 'Active' : 'Inactive'; ?></td>
                                    <td>
                                        <a class="btn btn-xs btn-warning" href="<?php echo base_url(); ?>index.php?union/merchendise/toggle/<?php echo $item['id']; ?>">Toggle</a>
                                        <a class="btn btn-xs btn-danger" onclick="return confirm('Delete item?');" href="<?php echo base_url(); ?>index.php?union/merchendise/delete/<?php echo $item['id']; ?>">Delete</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <form class="form-inline" method="post" action="<?php echo base_url(); ?>index.php?union/merchendise/update/<?php echo $item['id']; ?>">
                                            <input type="text" name="item_name" class="form-control input-sm" value="<?php echo html_escape($item['item_name']); ?>" required>
                                            <input type="text" name="item_code" class="form-control input-sm" value="<?php echo html_escape($item['item_code']); ?>" required>
                                            <input type="number" step="0.01" min="0" name="price" class="form-control input-sm" value="<?php echo (float)$item['price']; ?>" required>
                                            <input type="number" min="0" name="stock_qty" class="form-control input-sm" value="<?php echo (int)$item['stock_qty']; ?>" required>
                                            <select name="status" class="form-control input-sm">
                                                <option value="1" <?php echo ((int)$item['status'] === 1) ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo ((int)$item['status'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                            <input type="text" name="description" class="form-control input-sm" value="<?php echo html_escape($item['description']); ?>" placeholder="Description">
                                            <button type="submit" class="btn btn-xs btn-success">Update</button>
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