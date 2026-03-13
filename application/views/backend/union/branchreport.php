<?php 
    // Single-branch date range report: members, subscriptions (subventions), and claims.
    // $startdate, $enddate (YYYY-MM-DD) and $branch_id are passed from controller.

    // Load branch details
    $branch      = $this->db->get_where('branches', ['id' => $branch_id])->row_array();
    $branch_name = $branch ? $branch['name'] : 'Unknown Branch';

    // Total members in this branch (all time)
    $this->db->where('branch', $branch_id);
    $this->db->from('members');
    $branch_members_total = $this->db->count_all_results();

    // Members created in this branch within the date range
    $this->db->where('branch', $branch_id);
    $this->db->where('createdate >=', $startdate);
    $this->db->where('createdate <=', $enddate);
    $this->db->from('members');
    $branch_members_in_range = $this->db->count_all_results();

    // Subscriptions (subventions) for this branch in the date range
    $subs_query = $this->db->query("
        SELECT 
            COUNT(s.id) AS subscriptions_in_range,
            COALESCE(SUM(s.amount), 0) AS subscriptions_amount
        FROM subscriptions s
        JOIN members m ON m.id = s.memberid
        WHERE m.branch = ?
          AND s.type = 'Subscription'
          AND s.date >= ?
          AND s.date <= ?
    ", [$branch_id, $startdate, $enddate]);

    $subs_row = $subs_query->row_array();
    $subscriptions_in_range = $subs_row ? (int) $subs_row['subscriptions_in_range'] : 0;
    $subscriptions_amount   = $subs_row ? (float) $subs_row['subscriptions_amount'] : 0;

    // Claims for this branch in the date range (via members.branch)
    $claims_query = $this->db->query("
        SELECT 
            COUNT(c.id) AS claims_in_range,
            COALESCE(SUM(c.amount), 0) AS claims_amount
        FROM claims c
        JOIN members m ON m.id = c.member_id
        WHERE m.branch = ?
          AND c.claim_date >= ?
          AND c.claim_date <= ?
    ", [$branch_id, $startdate, $enddate]);

    $claims_row = $claims_query->row_array();
    $claims_in_range = $claims_row ? (int) $claims_row['claims_in_range'] : 0;
    $claims_amount   = $claims_row ? (float) $claims_row['claims_amount'] : 0;

    // Variance for this branch in this date range
    $variance = $subscriptions_amount - $claims_amount;
?>

<div class="row">
    <div class="col-md-3" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-primary">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Members in <?php echo htmlspecialchars($branch_name); ?></h4>
                            <div class="info">
                                <strong class="amount"><?php echo $branch_members_total; ?></strong>
                                <p class="text-xs text-primary mb-none">
                                    In range: <?php echo (int) $branch_members_in_range; ?> 
                                    (<?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-3" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-success">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-success">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Subscriptions (Subventions)</h4>
                            <div class="info">
                                <span class="text-success text-uppercase">E</span>
                                <strong class="amount"><?php echo number_format($subscriptions_amount, 2, '.', ' '); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-3" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-info">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-info">
                            <i class="fa fa-file-text"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Claims Amount</h4>
                            <div class="info">
                                <span class="text-info text-uppercase">E</span>
                                <strong class="amount"><?php echo number_format($claims_amount, 2, '.', ' '); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-3" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-secondary">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-secondary">
                            <i class="fa fa-sitemap"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Branch</h4>
                            <div class="info">
                                <strong class="amount"><?php echo htmlspecialchars($branch_name); ?></strong>
                                <p class="text-xs text-primary mb-none">ID: <?php echo (int) $branch_id; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-md-6" data-appear-animation="bounceIn">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    Subscriptions vs Claims - <?php echo htmlspecialchars($branch_name); ?>
                    (<?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>)
                </h2>
            </header>
            <div class="panel-body">
                <div class="chart chart-md" id="rangeDonut"></div>
                <hr class="solid short mt-lg">
                <div class="row">
                    <div class="col-md-4">
                        <div class="h4 text-weight-bold mb-none mt-lg">E <?php echo number_format($subscriptions_amount, 2, '.', ' '); ?></div>
                        <p class="text-xs text-primary mb-none">Subscriptions Total</p>
                    </div>
                    <div class="col-md-4">
                        <div class="h4 text-weight-bold mb-none mt-lg">E <?php echo number_format($claims_amount, 2, '.', ' '); ?></div>
                        <p class="text-xs text-primary mb-none">Claims Total</p>
                    </div>
                    <div class="col-md-4">
                        <div class="h4 text-weight-bold mb-none mt-lg">E <?php echo number_format($variance, 2, '.', ' '); ?></div>
                        <p class="text-xs text-primary mb-none">Variance</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-6" data-appear-animation="bounceInLeft">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Claims in Range - <?php echo htmlspecialchars($branch_name); ?></h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-claims-range">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member</th>
                                <th>National ID</th>
                                <th class="text-right">Amount (E)</th>
                                <th>Claim Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $this->db->select('c.*, m.name AS member_name');
                            $this->db->from('claims c');
                            $this->db->join('members m', 'm.id = c.member_id', 'left');
                            $this->db->where('c.claim_date >=', $startdate);
                            $this->db->where('c.claim_date <=', $enddate);
                            $this->db->where('m.branch', $branch_id);
                            $this->db->order_by('c.claim_date', 'DESC');
                            $claims_list = $this->db->get()->result_array();
                            $i = 0;
                            foreach ($claims_list as $row):
                                $i++;
                                $member_name = !empty($row['member_name']) ? $row['member_name'] : 'N/A';
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo htmlspecialchars($member_name); ?></td>
                                <td><?php echo htmlspecialchars($row['national_id']); ?></td>
                                <td class="text-right"><?php echo number_format($row['amount'], 2, '.', ' '); ?></td>
                                <td><?php echo htmlspecialchars($row['claim_date']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<script type="text/javascript">
    (function() {
        var rangeDonutData = [
            {
                label: "Subscriptions",
                value: <?php echo (float) $subscriptions_amount; ?>
            },
            {
                label: "Claims",
                value: <?php echo (float) $claims_amount; ?>
            }
        ];

        jQuery(function() {
            if (typeof Morris !== 'undefined' && document.getElementById('rangeDonut')) {
                Morris.Donut({
                    element: 'rangeDonut',
                    data: rangeDonutData,
                    resize: true,
                    formatter: function (y) {
                        return 'E ' + y.toFixed(2);
                    }
                });
            }

            if (jQuery.fn.DataTable) {
                if (jQuery('#datatable-claims-range').length) {
                    jQuery('#datatable-claims-range').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'copy',  text: 'Copy' },
                            { extend: 'excel', text: 'Excel' },
                            { extend: 'pdf',   text: 'PDF' },
                            { extend: 'print', text: 'Print' }
                        ]
                    });
                }

                if (jQuery('#datatable-subscriptions-branch').length) {
                    jQuery('#datatable-subscriptions-branch').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'copy',  text: 'Copy' },
                            { extend: 'excel', text: 'Excel' },
                            { extend: 'pdf',   text: 'PDF' },
                            { extend: 'print', text: 'Print' }
                        ]
                    });
                }
            }
        });
    })();
</script>
