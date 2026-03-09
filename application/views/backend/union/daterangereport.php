<?php 
    // Date range report using core tables: members, claims, subscriptions, branches.
    // $startdate and $enddate are passed in as normalised YYYY-MM-DD.

    // Members created in range
    $this->db->where('createdate >=', $startdate);
    $this->db->where('createdate <=', $enddate);
    $this->db->from('members');
    $members_in_range = $this->db->count_all_results();

    // Claims in range (by claim_date)
    $this->db->where('claim_date >=', $startdate);
    $this->db->where('claim_date <=', $enddate);
    $this->db->from('claims');
    $claims_in_range = $this->db->count_all_results();

    $this->db->where('claim_date >=', $startdate);
    $this->db->where('claim_date <=', $enddate);
    $this->db->select_sum('amount');
    $query         = $this->db->get('claims');
    $claims_amount = $query->row()->amount;
    if ($claims_amount == NULL) { $claims_amount = 0; }

    // Subscriptions in range (by subscriptions.date, type = 'Subscription')
    $this->db->where('date >=', $startdate);
    $this->db->where('date <=', $enddate);
    $this->db->where('type', 'Subscription');
    $this->db->from('subscriptions');
    $subscriptions_in_range = $this->db->count_all_results();

    $this->db->where('date >=', $startdate);
    $this->db->where('date <=', $enddate);
    $this->db->where('type', 'Subscription');
    $this->db->select_sum('amount');
    $query                 = $this->db->get('subscriptions');
    $subscriptions_amount  = $query->row()->amount;
    if ($subscriptions_amount == NULL) { $subscriptions_amount = 0; }

    // Branches (overall, not date-limited)
    $this->db->from('branches');
    $branches_total = $this->db->count_all_results();

    // Variance for this date range
    $variance = $subscriptions_amount - $claims_amount;

    // Detailed branch-level subscriptions (subventions-style) for this date range
    $branch_query = $this->db->query("
        SELECT 
            b.id   AS branch_id,
            b.name AS branch_name,
            COUNT(s.id) AS subscription_count,
            COALESCE(SUM(s.amount), 0) AS total_amount
        FROM branches b
        LEFT JOIN members m 
            ON m.branch = b.id
        LEFT JOIN subscriptions s 
            ON s.memberid = m.id
            AND s.type = 'Subscription'
            AND s.date >= ?
            AND s.date <= ?
        GROUP BY b.id, b.name
        ORDER BY total_amount DESC
    ", [$startdate, $enddate]);

    $range_branch_stats = $branch_query->result_array();

    // Unaccounted subscriptions in range (no member or no branch)
    $unaccounted_query = $this->db->query("
        SELECT 
            COUNT(s.id) AS subscription_count,
            COALESCE(SUM(s.amount), 0) AS total_amount
        FROM subscriptions s
        LEFT JOIN members m ON m.id = s.memberid
        WHERE s.type = 'Subscription'
          AND s.date >= ?
          AND s.date <= ?
          AND (
                s.memberid IS NULL
                OR s.memberid = 0
                OR m.id IS NULL
                OR m.branch IS NULL
                OR m.branch = 0
              )
    ", [$startdate, $enddate]);

    $range_unaccounted = $unaccounted_query->row_array();
    if (!$range_unaccounted) {
        $range_unaccounted = ['subscription_count' => 0, 'total_amount' => 0];
    }

    // Compute overall branch total (may slightly differ from $subscriptions_amount if other types exist)
    $range_overall_branch_amount = 0;
    $range_overall_branch_count  = 0;
    foreach ($range_branch_stats as $row) {
        $range_overall_branch_amount += (float) $row['total_amount'];
        $range_overall_branch_count  += (int) $row['subscription_count'];
    }
    $range_overall_branch_amount += (float) $range_unaccounted['total_amount'];
    $range_overall_branch_count  += (int) $range_unaccounted['subscription_count'];

    // Percentage share per branch (using range_overall_branch_amount)
    $range_branch_percentages = [];
    if ($range_overall_branch_amount > 0) {
        foreach ($range_branch_stats as $row) {
            $range_branch_percentages[$row['branch_id']] = round(
                ((float) $row['total_amount'] / $range_overall_branch_amount) * 100,
                2
            );
        }
    }
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
                            <h4 class="title">Members (Created in Range)</h4>
                            <div class="info">
                                <strong class="amount"><?php echo $members_in_range; ?></strong>
                                <p class="text-xs text-primary mb-none">
                                    <?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>
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
                            <h4 class="title">Subscriptions Amount</h4>
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
                            <h4 class="title">Branches (Total)</h4>
                            <div class="info">
                                <strong class="amount"><?php echo $branches_total; ?></strong>
                                <p class="text-xs text-primary mb-none">All branches</p>
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
                <h2 class="panel-title">Subscriptions vs Claims (<?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>)</h2>
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
                <h2 class="panel-title">Claims in Range</h2>
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
                            $this->db->where('claim_date >=', $startdate);
                            $this->db->where('claim_date <=', $enddate);
                            $this->db->order_by('claim_date', 'DESC');
                            $claims_list = $this->db->get('claims')->result_array();
                            $i = 0;
                            foreach ($claims_list as $row):
                                $i++;
                                $member = null;
                                if (!empty($row['member_id'])) {
                                    $member = $this->db->get_where('members', ['id' => $row['member_id']])->row_array();
                                }
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $member ? htmlspecialchars($member['name']) : 'N/A'; ?></td>
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

<div class="row">
    <!-- Branch subscriptions (subventions-style) for this date range -->
    <div class="col-md-8" data-appear-animation="bounceIn">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    Subventions by Branch (<?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>)
                </h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-branches-range">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Branch</th>
                                <th class="text-right">Total Subscriptions</th>
                                <th class="text-right">Total Amount (E)</th>
                                <th class="text-right">% of Total</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $range_total_branches = is_array($range_branch_stats) ? count($range_branch_stats) : 0;
                            $idx = 0;
                            foreach ($range_branch_stats as $row):
                                $idx++;
                                $branch_id   = $row['branch_id'];
                                $branch_name = $row['branch_name'];
                                $count       = (int) $row['subscription_count'];
                                $amount      = (float) $row['total_amount'];
                                $percentage  = isset($range_branch_percentages[$branch_id]) ? $range_branch_percentages[$branch_id] : 0;

                                $remark = '';
                                if ($idx === 1 && $range_total_branches > 0) {
                                    $remark = 'Highest';
                                } elseif ($idx === $range_total_branches && $range_total_branches > 1) {
                                    $remark = 'Lowest';
                                }
                        ?>
                            <tr>
                                <td><?php echo $idx; ?></td>
                                <td><?php echo htmlspecialchars($branch_name); ?></td>
                                <td class="text-right"><?php echo number_format($count); ?></td>
                                <td class="text-right"><?php echo number_format($amount, 2, '.', ' '); ?></td>
                                <td class="text-right"><?php echo number_format($percentage, 2, '.', ' '); ?>%</td>
                                <td><?php echo $remark; ?></td>
                            </tr>
                        <?php endforeach; ?>
                            <!-- Unaccounted row -->
                            <tr class="warning">
                                <td><?php echo $range_total_branches + 1; ?></td>
                                <td><strong>Unaccounted</strong></td>
                                <td class="text-right"><?php echo number_format((int) $range_unaccounted['subscription_count']); ?></td>
                                <td class="text-right"><?php echo number_format((float) $range_unaccounted['total_amount'], 2, '.', ' '); ?></td>
                                <td class="text-right">
                                    <?php
                                        $unacc_pct = 0;
                                        if ($range_overall_branch_amount > 0) {
                                            $unacc_pct = round(((float) $range_unaccounted['total_amount'] / $range_overall_branch_amount) * 100, 2);
                                        }
                                        echo number_format($unacc_pct, 2, '.', ' ');
                                    ?>%
                                </td>
                                <td>Not linked to any branch</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-4" data-appear-animation="bounceInLeft">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Branch Shares (Subventions)</h2>
            </header>
            <div class="panel-body">
                <div class="chart chart-md" id="branchRangeDonut"></div>
                <hr class="solid short mt-lg">
                <div class="chart chart-sm" id="branchRangeBar" style="height: 220px;"></div>
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

        // Branch-level data for charts
        var branchRangeDonutData = [
            <?php if (!empty($range_branch_stats)): ?>
                <?php foreach ($range_branch_stats as $row): ?>
                {
                    label: "<?php echo addslashes($row['branch_name']); ?>",
                    value: <?php echo (float) $row['total_amount']; ?>
                },
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($range_unaccounted['total_amount']) && (float) $range_unaccounted['total_amount'] > 0): ?>
            {
                label: "Unaccounted",
                value: <?php echo (float) $range_unaccounted['total_amount']; ?>
            }
            <?php endif; ?>
        ];

        var branchRangeBarData = [
            <?php if (!empty($range_branch_stats)): ?>
                <?php foreach ($range_branch_stats as $row): ?>
                {
                    branch: "<?php echo addslashes($row['branch_name']); ?>",
                    amount: <?php echo (float) $row['total_amount']; ?>
                },
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($range_unaccounted['total_amount']) && (float) $range_unaccounted['total_amount'] > 0): ?>
            {
                branch: "Unaccounted",
                amount: <?php echo (float) $range_unaccounted['total_amount']; ?>
            }
            <?php endif; ?>
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

            if (typeof Morris !== 'undefined') {
                if (document.getElementById('branchRangeDonut')) {
                    Morris.Donut({
                        element: 'branchRangeDonut',
                        data: branchRangeDonutData,
                        resize: true,
                        formatter: function (y) {
                            return 'E ' + y.toFixed(2);
                        }
                    });
                }

                if (document.getElementById('branchRangeBar')) {
                    Morris.Bar({
                        element: 'branchRangeBar',
                        data: branchRangeBarData,
                        xkey: 'branch',
                        ykeys: ['amount'],
                        labels: ['Amount (E)'],
                        hideHover: 'auto',
                        resize: true,
                        barColors: ['#0088cc']
                    });
                }
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

                if (jQuery('#datatable-branches-range').length) {
                    jQuery('#datatable-branches-range').DataTable({
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
