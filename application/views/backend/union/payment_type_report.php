<?php
// payment_type_report.php
// Shows summary + breakdown for the selected payment type in the given date range

defined('BASEPATH') OR exit('No direct script access allowed');

// Variables coming from controller
// $startdate, $enddate (YYYY-MM-DD), $payment_type
?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-primary">
            Payment Type Report: <strong><?php echo htmlspecialchars($payment_type); ?></strong>
            <small class="text-muted"> • From <?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?></small>
        </h3>
        <hr>
    </div>
</div>

<?php
// ───────────────────────────────────────────────
//              QUERIES – PAYMENT TYPE BASED
// ───────────────────────────────────────────────

// Total received (deposits/subscriptions) via this payment type in range
$received = $this->db->query("
    SELECT 
        COUNT(id) AS count_received,
        COALESCE(SUM(amount), 0) AS total_received
    FROM subscriptions
    WHERE type = 'Subscription'           -- adjust if your table uses different values
      AND source = ?
      AND date >= ?
      AND date <= ?
      AND amount > 0
", [$payment_type, $startdate, $enddate])->row_array();

$total_received     = $received ? (float)$received['total_received'] : 0;
$count_received     = $received ? (int)$received['count_received'] : 0;


// Total claims paid out (if your system pays claims via subscriptions too)
// If claims are NOT recorded in subscriptions → keep this 0 or remove
$paid_out = $this->db->query("
    SELECT 
        COUNT(id) AS count_paid,
        COALESCE(SUM(amount), 0) AS total_paid
    FROM subscriptions
    WHERE type = 'Claim'                  -- adjust if different
      AND source = ?
      AND date >= ?
      AND date <= ?
      AND amount < 0                        -- assuming claims are negative
", [$payment_type, $startdate, $enddate])->row_array();

$total_paid         = $paid_out ? (float)abs($paid_out['total_paid']) : 0;  // make positive for display
$count_paid         = $paid_out ? (int)$paid_out['count_paid'] : 0;

$variance           = $total_received - $total_paid;
?>

<div class="row">
    <!-- Card 1 -->
    <div class="col-md-3">
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
                            <h4 class="title">Received (<?php echo htmlspecialchars($payment_type); ?>)</h4>
                            <div class="info">
                                <strong class="amount">E <?php echo number_format($total_received, 2, '.', ' '); ?></strong>
                                <p class="text-xs text-primary mb-none">
                                    <?php echo $count_received; ?> transactions
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Card 2 -->
    <div class="col-md-3">
        <section class="panel panel-featured-left panel-featured-danger">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-danger">
                            <i class="fa fa-arrow-up"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Paid Out</h4>
                            <div class="info">
                                <strong class="amount">E <?php echo number_format($total_paid, 2, '.', ' '); ?></strong>
                                <p class="text-xs text-danger mb-none">
                                    <?php echo $count_paid; ?> transactions
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Card 3 -->
    <div class="col-md-3">
        <section class="panel panel-featured-left panel-featured-success">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-success">
                            <i class="fa fa-balance-scale"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Net Variance</h4>
                            <div class="info">
                                <strong class="amount <?php echo $variance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    E <?php echo number_format($variance, 2, '.', ' '); ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Card 4 – Optional summary / info -->
    <div class="col-md-3">
        <section class="panel panel-featured-left panel-featured-warning">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-warning">
                            <i class="fa fa-credit-card"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Payment Method</h4>
                            <div class="info">
                                <strong class="amount"><?php echo htmlspecialchars($payment_type); ?></strong>
                                <p class="text-xs mb-none">Selected filter</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    Received vs Paid – <?php echo htmlspecialchars($payment_type); ?>
                    (<?php echo htmlspecialchars($startdate); ?> → <?php echo htmlspecialchars($enddate); ?>)
                </h2>
            </header>
            <div class="panel-body">
                <div class="chart chart-md" id="paymentDonut"></div>
                <hr class="solid short mt-lg">
                <div class="row text-center">
                    <div class="col-xs-4">
                        <h4 class="text-weight-bold text-success">E <?php echo number_format($total_received, 2); ?></h4>
                        <p class="text-xs text-muted">Received</p>
                    </div>
                    <div class="col-xs-4">
                        <h4 class="text-weight-bold text-danger">E <?php echo number_format($total_paid, 2); ?></h4>
                        <p class="text-xs text-muted">Paid</p>
                    </div>
                    <div class="col-xs-4">
                        <h4 class="text-weight-bold <?php echo $variance >= 0 ? 'text-success' : 'text-danger'; ?>">
                            E <?php echo number_format($variance, 2); ?>
                        </h4>
                        <p class="text-xs text-muted">Variance</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-5">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Recent Transactions – <?php echo htmlspecialchars($payment_type); ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-transactions">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Description</th>
                                <th class="text-right">Amount (E)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $this->db->select('s.*, m.name AS member_name, m.idnumber');
                            $this->db->from('subscriptions s');
                            $this->db->join('members m', 'm.id = s.memberid', 'left');
                            $this->db->where('s.source', $payment_type);
                            $this->db->where('s.date >=', $startdate);
                            $this->db->where('s.date <=', $enddate);
                            $this->db->order_by('s.date', 'DESC');
                            $this->db->limit(50); // ← prevent huge tables
                            $transactions = $this->db->get()->result_array();

                            $i = 0;
                            foreach ($transactions as $row):
                                $i++;
                                $amount_class = $row['amount'] > 0 ? 'text-success' : 'text-danger';
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['member_name'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['description'] ?? '—'); ?></td>
                                <td class="text-right <?php echo $amount_class; ?>">
                                    <?php echo number_format(abs($row['amount']), 2, '.', ' '); ?>
                                    <?php echo $row['amount'] > 0 ? 'Cr' : 'Dr'; ?>
                                </td>
                                <td><?php echo ucfirst(htmlspecialchars($row['status'] ?? '—')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($i === 0): ?>
                            <tr><td colspan="6" class="text-center text-muted">No transactions found</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
(function() {
    var donutData = [
        { label: "Received", value: <?php echo (float)$total_received; ?> },
        { label: "Paid Out", value: <?php echo (float)$total_paid; ?> }
    ];

    if (typeof Morris !== 'undefined' && document.getElementById('paymentDonut')) {
        Morris.Donut({
            element: 'paymentDonut',
            data: donutData,
            resize: true,
            colors: ['#47a447', '#d9534f'],
            formatter: function(y) { return 'E ' + y.toFixed(2); }
        });
    }

    if (jQuery.fn.DataTable && jQuery('#datatable-transactions').length) {
        jQuery('#datatable-transactions').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print'],
            pageLength: 10,
            order: [[1, 'desc']] // sort by date descending
        });
    }
})();
</script>