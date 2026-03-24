<?php
$member = $this->db->get_where('members', array('id' => $memberid))->row_array();
if (!$member) {
    echo '<div class="alert alert-danger">Member not found.</div>';
    return;
}

$branch_name = '';
if (!empty($member['branch'])) {
    $branch_name = $this->db->select('name')->get_where('branches', array('id' => $member['branch']))->row('name');
}

$status_name = '';
if (!empty($member['employment_status'])) {
    $status_name = $this->db->select('description')->get_where('employment_status', array('id' => $member['employment_status']))->row('description');
}

$nominee = $this->db
    ->order_by('id', 'ASC')
    ->get_where('nominee', array('member_id' => $member['id']))
    ->row_array();

$subscriptions = $this->db
    ->where('memberid', $member['id'])
    ->order_by('date', 'DESC')
    ->limit(12)
    ->get('subscriptions')
    ->result_array();
?>

<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="pull-right">
                    <a href="#"
                       class="btn btn-xs btn-primary"
                       onclick="showAjaxModal('<?php echo base_url('index.php?modal/popup/modal_edit_member/' . $member['id']); ?>')">
                        <i class="fa fa-edit"></i> Edit Member
                    </a>
                    <a href="<?php echo base_url('index.php?union/member_profile_print/' . $member['id']); ?>"
                       target="_blank"
                       class="btn btn-xs btn-default">
                        <i class="fa fa-print"></i> Print Profile
                    </a>
                </div>
                <h2 class="panel-title">
                    <i class="fa fa-user"></i> Member Profile
                </h2>
            </header>

            <div class="panel-body">
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-2 col-sm-3 text-center">
                        <img src="<?php echo base_url('uploads/logo.png'); ?>" alt="SNAT Logo" class="img-responsive center-block" style="max-height: 70px;">
                    </div>
                    <div class="col-md-10 col-sm-9">
                        <h3 style="margin-top: 0; margin-bottom: 5px;">SNAT Union Member Profile</h3>
                        <p class="text-muted" style="margin-bottom: 0;">
                            Account: <strong><?php echo '058-' . $member['id']; ?></strong>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr><th style="width: 35%;">Full Name</th><td><?php echo htmlspecialchars(trim(($member['surname'] ?? '') . ' ' . ($member['name'] ?? ''))); ?></td></tr>
                                <tr><th>National ID</th><td><?php echo !empty($member['idnumber']) ? htmlspecialchars($member['idnumber']) : 'N/A'; ?></td></tr>
                                <tr><th>Employee No</th><td><?php echo !empty($member['employeeno']) ? htmlspecialchars($member['employeeno']) : 'N/A'; ?></td></tr>
                                <tr><th>TSC No</th><td><?php echo !empty($member['tscno']) ? htmlspecialchars($member['tscno']) : 'N/A'; ?></td></tr>
                                <tr><th>Date of Birth</th><td><?php echo !empty($member['dob']) ? htmlspecialchars($member['dob']) : 'N/A'; ?></td></tr>
                                <tr><th>Gender</th><td><?php echo !empty($member['gender']) ? htmlspecialchars($member['gender']) : 'N/A'; ?></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr><th style="width: 35%;">Cell Number</th><td><?php echo !empty($member['cellnumber']) ? htmlspecialchars($member['cellnumber']) : 'N/A'; ?></td></tr>
                                <tr><th>School Code</th><td><?php echo !empty($member['schoolcode']) ? htmlspecialchars($member['schoolcode']) : 'N/A'; ?></td></tr>
                                <tr><th>Institution</th><td><?php echo !empty($member['institution']) ? htmlspecialchars($member['institution']) : 'N/A'; ?></td></tr>
                                <tr><th>Branch</th><td><?php echo !empty($branch_name) ? htmlspecialchars($branch_name) : 'N/A'; ?></td></tr>
                                <tr><th>Employment Status</th><td><?php echo !empty($status_name) ? htmlspecialchars($status_name) : 'N/A'; ?></td></tr>
                                <tr><th>Nominee</th><td><?php echo !empty($nominee['fullname']) ? htmlspecialchars($nominee['fullname']) : 'N/A'; ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><i class="fa fa-list"></i> Last 12 Subscriptions</h2>
            </header>
            <div class="panel-body">
                <?php if (!empty($subscriptions)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th class="text-right">Amount (E)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($subscriptions as $sub): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo !empty($sub['date']) ? date('Y-m-d', strtotime($sub['date'])) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['description']) ? htmlspecialchars($sub['description']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['type']) ? htmlspecialchars($sub['type']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['status']) ? htmlspecialchars($sub['status']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['source']) ? htmlspecialchars($sub['source']) : 'N/A'; ?></td>
                                        <td class="text-right"><?php echo number_format((float)($sub['amount'] ?? 0), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" style="margin-bottom: 0;">
                        No subscriptions found for this member.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?php
$member_data = $this->db->get_where('members', array('id' => $memberid))->result_array();
foreach ($member_data as $row):

$branch_name = '';
if (!empty($row['branch'])) {
    $branch_name = $this->db->select('name')->get_where('branches', array('id' => $row['branch']))->row('name');
}

$status_name = '';
if (!empty($row['employment_status'])) {
    $status_name = $this->db->select('description')->get_where('employment_status', array('id' => $row['employment_status']))->row('description');
}

$nominee = $this->db
    ->order_by('id', 'ASC')
    ->get_where('nominee', array('member_id' => $row['id']))
    ->row_array();

$subscriptions = $this->db
    ->where('memberid', $row['id'])
    ->order_by('date', 'DESC')
    ->limit(12)
    ->get('subscriptions')
    ->result_array();
?>

<style>
@media print {
    body { margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .panel { border: 1px solid #ddd !important; box-shadow: none !important; }
    .panel-heading { background: #f5f5f5 !important; color: #333 !important; }
    .table th, .table td { border: 1px solid #ccc !important; }
}
.profile-wrapper { margin-top: 10px; }
.profile-logo { max-height: 56px; width: auto; }
.profile-title { margin: 10px 0 0; font-weight: 700; }
.table-profile td { vertical-align: middle !important; }
.table-profile .label-cell { width: 32%; font-weight: 600; color: #555; background: #fafafa; }
</style>

<div class="row profile-wrapper">
    <div class="col-md-12">
        <div class="no-print text-right" style="margin-bottom: 15px;">
            <a href="#" class="btn btn-warning"
               onclick="showAjaxModal('<?php echo base_url('index.php?modal/popup/modal_edit_member/' . $row['id']); ?>')">
                <i class="fa fa-edit"></i> Edit Member
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print
            </button>
        </div>

        <div class="panel panel-default">
            <div class="panel-body text-center">
                <img src="<?php echo base_url('uploads/logo.png'); ?>" alt="SNAT Logo" class="profile-logo">
                <h3 class="profile-title">SNAT Union Member Profile</h3>
                <p class="text-muted" style="margin-bottom: 0;">Generated on <?php echo date('d-m-Y H:i:s'); ?></p>
            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-user"></i> Member Details</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-profile">
                            <tr><td class="label-cell">SNAT Account</td><td><?php echo '058-' . $row['id']; ?></td></tr>
                            <tr><td class="label-cell">Full Name</td><td><?php echo htmlspecialchars(trim(($row['surname'] ?? '') . ' ' . ($row['name'] ?? ''))); ?></td></tr>
                            <tr><td class="label-cell">National ID</td><td><?php echo !empty($row['idnumber']) ? htmlspecialchars($row['idnumber']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Employee No</td><td><?php echo !empty($row['employeeno']) ? htmlspecialchars($row['employeeno']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">TSC No</td><td><?php echo !empty($row['tscno']) ? htmlspecialchars($row['tscno']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Date of Birth</td><td><?php echo !empty($row['dob']) ? htmlspecialchars($row['dob']) : 'N/A'; ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-profile">
                            <tr><td class="label-cell">Gender</td><td><?php echo !empty($row['gender']) ? htmlspecialchars($row['gender']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Cell Number</td><td><?php echo !empty($row['cellnumber']) ? htmlspecialchars($row['cellnumber']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">School Code</td><td><?php echo !empty($row['schoolcode']) ? htmlspecialchars($row['schoolcode']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Institution</td><td><?php echo !empty($row['institution']) ? htmlspecialchars($row['institution']) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Branch</td><td><?php echo !empty($branch_name) ? htmlspecialchars($branch_name) : 'N/A'; ?></td></tr>
                            <tr><td class="label-cell">Employment Status</td><td><?php echo !empty($status_name) ? htmlspecialchars($status_name) : 'N/A'; ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($nominee)): ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-user-plus"></i> Nominee</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-profile" style="margin-bottom: 0;">
                        <tr>
                            <td class="label-cell">Nominee Full Name</td>
                            <td><?php echo !empty($nominee['fullname']) ? htmlspecialchars($nominee['fullname']) : 'N/A'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-list"></i> Last 12 Subscriptions</h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($subscriptions)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th class="text-right">Amount (E)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($subscriptions as $sub): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo !empty($sub['date']) ? date('Y-m-d', strtotime($sub['date'])) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['description']) ? htmlspecialchars($sub['description']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['type']) ? htmlspecialchars($sub['type']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['status']) ? htmlspecialchars($sub['status']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($sub['source']) ? htmlspecialchars($sub['source']) : 'N/A'; ?></td>
                                        <td class="text-right"><?php echo number_format((float)($sub['amount'] ?? 0), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" style="margin-bottom: 0;">
                        No subscriptions found for this member.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
endforeach;
?>
<?php
$member_data = $this->db->get_where('members', array('id' => $memberid))->result_array();
foreach ($member_data as $row):
?>

<style>
@media print {
    body { margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .print-page { page-break-after: always; }
    .print-page:last-child { page-break-after: auto; }
    .member-profile { padding: 20px; }
    .profile-header { border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
    .profile-section { margin-bottom: 25px; }
    .info-row { margin-bottom: 10px; padding: 5px 0; border-bottom: 1px dotted #ccc; }
    .info-label { font-weight: bold; display: inline-block; width: 200px; }
    .info-value { display: inline-block; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table th, table td { border: 1px solid #000; padding: 8px; text-align: left; }
    table th { background-color: #f0f0f0; font-weight: bold; }
    .page-break { page-break-before: always; }
}
.member-profile { background: #fff; padding: 30px; }
.profile-header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
.profile-header h2 { margin: 0; font-size: 24px; }
.profile-header p { margin: 5px 0; color: #666; }
.profile-section { margin-bottom: 30px; }
.section-title { font-size: 18px; font-weight: bold; color: #333; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 15px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.info-row { padding: 8px 0; border-bottom: 1px dotted #ddd; }
.info-label { font-weight: bold; color: #555; }
.info-value { color: #333; }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="no-print" style="margin-bottom: 20px; text-align: right;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Profile
            </button>
            <a href="#"
               class="btn btn-warning"
               onclick="showAjaxModal('<?php echo base_url('index.php?modal/popup/modal_edit_member/' . $row['id']); ?>')">
                <i class="fa fa-edit"></i> Edit Member
            </a>
        </div>

        <!-- MEMBER INFORMATION -->
        <div class="member-profile print-page">
            <div class="profile-header">
                <h2>SNAT union</h2>
                <p>Member Profile</p>
                <p>Generated on: <?php echo date('d-m-Y H:i:s'); ?></p>
            </div>

            <div class="profile-section">
                <div class="section-title">Member Information</div>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">SNAT union Account:</span>
                        <span class="info-value"><?php echo '058-'.$row['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo $row['surname'].' '.$row['name']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">National ID:</span>
                        <span class="info-value"><?php echo $row['idnumber']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Employee No:</span>
                        <span class="info-value"><?php echo $row['employeeno'] ? $row['employeeno'] : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">TSC No:</span>
                        <span class="info-value"><?php echo $row['tscno'] ? $row['tscno'] : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value"><?php echo $row['dob']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender:</span>
                        <span class="info-value"><?php echo $row['gender']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cell Number:</span>
                        <span class="info-value"><?php echo $row['cellnumber']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">School Code:</span>
                        <span class="info-value"><?php echo $row['schoolcode'] ? $row['schoolcode'] : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Institution:</span>
                        <span class="info-value"><?php echo $row['institution'] ? $row['institution'] : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Branch:</span>
                        <span class="info-value">
                            <?php
                            if (!empty($row['branch'])) {
                                $branch_name = $this->db->select('name')->get_where('branches', array('id' => $row['branch']))->row('name');
                                echo $branch_name ? $branch_name : 'N/A';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Employment Status:</span>
                        <span class="info-value">
                            <?php
                            if (!empty($row['employment_status'])) {
                                $status_name = $this->db->select('description')->get_where('employment_status', array('id' => $row['employment_status']))->row('description');
                                echo $status_name ? $status_name : 'N/A';
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endforeach;
?>
 