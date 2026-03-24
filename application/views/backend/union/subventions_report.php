<?php
    // Data passed from controller:
    // - $branch_stats: array of [branch_id, branch_name, subscription_count, total_amount]
    // - $branch_percentages: [branch_id => percentage_of_total_amount]
    // - $unaccounted: ['subscription_count' => int, 'total_amount' => float]
    // - $overall_total_amount, $overall_total_count

    $unaccounted_amount = isset($unaccounted['total_amount']) ? (float) $unaccounted['total_amount'] : 0;
    $unaccounted_count  = isset($unaccounted['subscription_count']) ? (int) $unaccounted['subscription_count'] : 0;
    $total_branches     = is_array($branch_stats) ? count($branch_stats) : 0;
?>

<div class="row">
    <div class="col-md-4" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-info">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-info">
                            <i class="fa fa-database"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Total Subscriptions Amount</h4>
                            <div class="info">
                                <span class="text-info text-uppercase">E</span>
                                <strong class="amount">
                                    <?php echo number_format($overall_total_amount, 2, '.', ' '); ?>
                                </strong>
                                <p class="text-xs text-primary mb-none">
                                    Period: <?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-4" data-appear-animation="bounceInUp">
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
                            <h4 class="title">Total Subscription Records</h4>
                            <div class="info">
                                <strong class="amount">
                                    <?php echo (int) $overall_total_count; ?>
                                </strong>
                                <p class="text-xs text-primary mb-none">
                                    Across <?php echo (int) $total_branches; ?> branches + Unaccounted
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-md-4" data-appear-animation="bounceInUp">
        <section class="panel panel-featured-left panel-featured-warning">
            <div class="panel-body">
                <div class="widget-summary widget-summary-sm">
                    <div class="widget-summary-col widget-summary-col-icon">
                        <div class="summary-icon bg-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="widget-summary-col">
                        <div class="summary">
                            <h4 class="title">Unaccounted for Branch</h4>
                            <div class="info">
                                <span class="text-warning text-uppercase">E</span>
                                <strong class="amount">
                                    <?php echo number_format($unaccounted_amount, 2, '.', ' '); ?>
                                </strong>
                                <p class="text-xs text-primary mb-none">
                                    Records: <?php echo (int) $unaccounted_count; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <!-- Branch summary table -->
    <div class="col-md-8" data-appear-animation="bounceIn">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Subscriptions by Branch (<?php echo htmlspecialchars($startdate); ?> to <?php echo htmlspecialchars($enddate); ?>)</h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-none" id="datatable-branches">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Branch</th>
                                <th class="text-right">Total Subscriptions</th>
                                <th class="text-right">Total Amount (E)</th>
                                <th class="text-right">30% of Total</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $index = 0;
                            $lastIndex = max($total_branches - 1, 0);
                            foreach ($branch_stats as $row):
                                $index++;
                                $branch_id   = $row['branch_id'];
                                $branch_name = $row['branch_name'];
                                $count       = (int) $row['subscription_count'];
                                $amount      = (float) $row['total_amount'];
                                $percentage  = isset($branch_percentages[$branch_id]) ? $branch_percentages[$branch_id] : 0;

                                $remark = '';
                                if ($index === 1 && $total_branches > 0) {
                                    $remark = 'Highest';
                                } elseif ($index === $total_branches && $total_branches > 1) {
                                    $remark = 'Lowest';
                                }
                        ?>
                            <tr>
                                <td><?php echo $index; ?></td>
                                <td><?php echo htmlspecialchars($branch_name); ?></td>
                                <td class="text-right"><?php echo number_format($count); ?></td>
                                <td class="text-right"><?php echo number_format($amount, 2, '.', ' '); ?></td>
                                <td class="text-right">
                                    <?php echo number_format($percentage, 2, '.', ' '); ?>%
                                </td>
                                <td><?php echo $remark; ?></td>
                            </tr>
                        <?php endforeach; ?>
                            <!-- Unaccounted row -->
                            <tr class="warning">
                                <td><?php echo $total_branches + 1; ?></td>
                                <td><strong>Unaccounted</strong></td>
                                <td class="text-right"><?php echo number_format($unaccounted_count); ?></td>
                                <td class="text-right"><?php echo number_format($unaccounted_amount, 2, '.', ' '); ?></td>
                                <td class="text-right">
                                    <?php
                                        $unaccounted_pct = 0;
                                        if ($overall_total_amount > 0) {
                                            $unaccounted_pct = round(($unaccounted_amount / $overall_total_amount) * 100, 2);
                                        }
                                        echo number_format($unaccounted_pct, 2, '.', ' ');
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

    <!-- Charts -->
    <div class="col-md-4" data-appear-animation="bounceInLeft">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Branch Shares (Amount)</h2>
            </header>
            <div class="panel-body">
                <div class="chart chart-md" id="branchDonut"></div>
                <hr class="solid short mt-lg">
                <div class="chart chart-sm" id="branchBar" style="height: 220px;"></div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    (function() {
        var branchDonutData = [
            <?php if (!empty($branch_stats)): ?>
                <?php foreach ($branch_stats as $row): ?>
                {
                    label: "<?php echo addslashes($row['branch_name']); ?>",
                    value: <?php echo (float) $row['total_amount']; ?>
                },
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($unaccounted_amount > 0): ?>
            {
                label: "Unaccounted",
                value: <?php echo $unaccounted_amount; ?>
            }
            <?php endif; ?>
        ];

        var branchBarData = [
            <?php if (!empty($branch_stats)): ?>
                <?php foreach ($branch_stats as $row): ?>
                {
                    branch: "<?php echo addslashes($row['branch_name']); ?>",
                    amount: <?php echo (float) $row['total_amount']; ?>
                },
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($unaccounted_amount > 0): ?>
            {
                branch: "Unaccounted",
                amount: <?php echo $unaccounted_amount; ?>
            }
            <?php endif; ?>
        ];

        jQuery(function() {
            if (typeof Morris !== 'undefined') {
                if (document.getElementById('branchDonut')) {
                    Morris.Donut({
                        element: 'branchDonut',
                        data: branchDonutData,
                        resize: true,
                        formatter: function (y) {
                            return 'E ' + y.toFixed(2);
                        }
                    });
                }

                if (document.getElementById('branchBar')) {
                    Morris.Bar({
                        element: 'branchBar',
                        data: branchBarData,
                        xkey: 'branch',
                        ykeys: ['amount'],
                        labels: ['Amount (E)'],
                        hideHover: 'auto',
                        resize: true,
                        barColors: ['#0088cc']
                    });
                }

                // DataTable with export / print buttons
                if (jQuery.fn.DataTable && jQuery('#datatable-branches').length) {
                    jQuery('#datatable-branches').DataTable({
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
