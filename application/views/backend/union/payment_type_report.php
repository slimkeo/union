<?php
// payment_type_report.php
// Focused ONLY on subscriptions received via the selected payment type

defined('BASEPATH') OR exit('No direct script access allowed');

// Variables from controller: $startdate, $enddate (YYYY-MM-DD), $payment_type
?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-primary">
            <?php echo htmlspecialchars($payment_type); ?> Subscriptions Report
            <small class="text-muted"> • <?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?></small>
        </h3>
        <hr>
    </div>
</div>

<?php
// ───────────────────────────────────────────────
//              MAIN QUERY – SUBSCRIPTIONS ONLY
// ───────────────────────────────────────────────

$subs = $this->db->query("
    SELECT 
        COUNT(id) AS transaction_count,
        COALESCE(SUM(amount), 0) AS total_amount
    FROM statements
    WHERE type = 'Subscription'
      AND source = ?
      AND date >= ?
      AND date <= ?
      AND amount > 0
", [$payment_type, $startdate, $enddate])->row_array();

$total_amount     = $subs ? (float)$subs['total_amount'] : 0;
$transaction_count = $subs ? (int)$subs['transaction_count'] : 0;
?>

<div class="row">
    <!-- Total Collected -->
    <div class="col-md-4">
        <section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-primary">
                            <i class="fa fa-arrow-down"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Total Collected</h4>
                            <div class="info">
                                <strong class="amount">E <?php echo number_format($total_amount, 2, '.', ' '); ?></strong>
                                <p class="text-xs text-primary mb-none">
                                    via <?php echo htmlspecialchars($payment_type); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Number of Transactions -->
    <div class="col-md-4">
        <section class="panel panel-featured-left panel-featured-success">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-success">
                            <i class="fa fa-exchange"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Transactions</h4>
                            <div class="info">
                                <strong class="amount"><?php echo number_format($transaction_count); ?></strong>
                                <p class="text-xs text-success mb-none">
                                    subscription payments
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Payment Method (recap) -->
    <div class="col-md-4">
        <section class="panel panel-featured-left panel-featured-info">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-info">
                            <i class="fa fa-credit-card"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Payment Type</h4>
                            <div class="info">
                                <strong class="amount"><?php echo htmlspecialchars($payment_type); ?></strong>
                                <p class="text-xs text-info mb-none">selected method</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    Recent <?php echo htmlspecialchars($payment_type); ?> Subscription Payments
                    (<?php echo htmlspecialchars($startdate); ?> – <?php echo htmlspecialchars($enddate); ?>)
                </h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-subscriptions">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Member</th>
                                <th>ID / Passbook</th>
                                <th>Description</th>
                                <th class="text-right">Amount (E)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $this->db->select('s.*, m.name AS member_name, m.idnumber, m.passbook_no');
                            $this->db->from('statements s');
                            $this->db->join('members m', 'm.id = s.memberid', 'left');
                            $this->db->where('s.type', 'Subscription');
                            $this->db->where('s.source', $payment_type);
                            $this->db->where('s.date >=', $startdate);
                            $this->db->where('s.date <=', $enddate);
                            $this->db->where('s.amount >', 0);
                            $this->db->order_by('s.date', 'DESC');
                            $this->db->limit(100); // adjust as needed
                            $transactions = $this->db->get()->result_array();

                            $i = 0;
                            foreach ($transactions as $row):
                                $i++;
                                $member_identifier = $row['idnumber'] ?: $row['passbook_no'] ?: '—';
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['member_name'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($member_identifier); ?></td>
                                <td><?php echo htmlspecialchars($row['description'] ?? 'Subscription'); ?></td>
                                <td class="text-right text-success">
                                    <?php echo number_format($row['amount'], 2, '.', ' '); ?>
                                </td>
                                <td><?php echo ucfirst(htmlspecialchars($row['status'] ?? '—')); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($i === 0): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No subscription payments found in this period</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Optional: small summary bar chart or just amount display -->
<div class="row mt-lg">
    <div class="col-md-12 text-center">
        <h4 class="text-muted">
            Total collected via <strong><?php echo htmlspecialchars($payment_type); ?></strong>:
            <span class="text-primary h3">E <?php echo number_format($total_amount, 2); ?></span>
        </h4>
    </div>
</div>

<script type="text/javascript">
(function() {
    if (jQuery.fn.DataTable && jQuery('#datatable-subscriptions').length) {
        jQuery('#datatable-subscriptions').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print'],
            pageLength: 15,
            order: [[1, 'desc']] // newest first
        });
    }
})();
</script>