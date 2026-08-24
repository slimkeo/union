<?php
$pending = $pending ?? [];
$files   = $files ?? [];

if (empty($pending)) {
    echo '<div class="alert alert-danger">Pending member not found.</div>';
    return;
}

$branch_name = '';
if (!empty($pending['branch'])) {
    $branch_row = $this->db->select('name')->get_where('branches', ['id' => $pending['branch']])->row();
    $branch_name = $branch_row->name ?? '';
}

$grade_name = '';
if (!empty($pending['grade']) && $this->db->table_exists('grade')) {
    $grade_row = $this->db->select('description')->get_where('grade', ['id' => $pending['grade']])->row();
    $grade_name = $grade_row->description ?? '';
}

$post_name = '';
if (!empty($pending['post']) && $this->db->table_exists('post')) {
    $post_row = $this->db->select('description')->get_where('post', ['id' => $pending['post']])->row();
    $post_name = $post_row->description ?? '';
}

$status_name = '';
if (!empty($pending['employment_status'])) {
    $status_row = $this->db->select('description')->get_where('employment_status', ['id' => $pending['employment_status']])->row();
    $status_name = $status_row->description ?? '';
}

$file_type_labels = [
    'id'      => 'ID Document',
    'ID'      => 'ID Document',
    'payslip' => 'Payslip',
    'POP'     => 'Proof of Payment (POP)',
    'pop'     => 'Proof of Payment (POP)',
];
?>

<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="pull-right">
                    <a href="<?php echo base_url('index.php?union/pending_members'); ?>" class="btn btn-xs btn-default">
                        <i class="fa fa-arrow-left"></i> Back to Pending
                    </a>
                </div>
                <h2 class="panel-title">
                    <i class="fa fa-user"></i> Pending Member Application
                </h2>
            </header>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr><th style="width: 35%;">Full Name</th><td><?php echo htmlspecialchars(trim(($pending['surname'] ?? '') . ' ' . ($pending['name'] ?? ''))); ?></td></tr>
                                <tr><th>National ID</th><td><?php echo !empty($pending['idnumber']) ? htmlspecialchars($pending['idnumber']) : 'N/A'; ?></td></tr>
                                <tr><th>Employee No</th><td><?php echo !empty($pending['employeeno']) ? htmlspecialchars($pending['employeeno']) : 'N/A'; ?></td></tr>
                                <tr><th>TSC No</th><td><?php echo !empty($pending['tscno']) ? htmlspecialchars($pending['tscno']) : 'N/A'; ?></td></tr>
                                <tr><th>Date of Birth</th><td><?php echo !empty($pending['dob']) ? htmlspecialchars($pending['dob']) : 'N/A'; ?></td></tr>
                                <tr><th>Gender</th><td><?php echo !empty($pending['gender']) ? htmlspecialchars($pending['gender']) : 'N/A'; ?></td></tr>
                                <tr><th>Cell Number</th><td><?php echo !empty($pending['cellnumber']) ? htmlspecialchars($pending['cellnumber']) : 'N/A'; ?></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr><th style="width: 35%;">School Code</th><td><?php echo !empty($pending['schoolcode']) ? htmlspecialchars($pending['schoolcode']) : 'N/A'; ?></td></tr>
                                <tr><th>Institution</th><td><?php echo !empty($pending['institution']) ? htmlspecialchars($pending['institution']) : 'N/A'; ?></td></tr>
                                <tr><th>Branch</th><td><?php echo !empty($branch_name) ? htmlspecialchars($branch_name) : 'N/A'; ?></td></tr>
                                <tr><th>Employment Status</th><td><?php echo !empty($status_name) ? htmlspecialchars($status_name) : 'N/A'; ?></td></tr>
                                <tr><th>Grade</th><td><?php echo !empty($grade_name) ? htmlspecialchars($grade_name).'('.$pending['salary'].')' : 'N/A'; ?></td></tr>
                                <tr><th>Post</th><td><?php echo !empty($post_name) ? htmlspecialchars($post_name) : 'N/A'; ?></td></tr>
                                <tr><th>Qualification</th><td><?php echo !empty($pending['qualification']) ? htmlspecialchars($pending['qualification']) : 'N/A'; ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><i class="fa fa-paperclip"></i> Joining Files (ID, Payslip, POP)</h2>
            </header>
            <div class="panel-body">
                <?php if (!empty($files)): ?>
                    <ul class="list-group">
                        <?php foreach ($files as $file): ?>
                            <?php
                                $type_key = (string)($file['file_type'] ?? '');
                                $label = $file_type_labels[$type_key] ?? strtoupper($type_key);
                                $file_url = base_url('index.php?union/view_joining_file/' . ($file['id'] ?? ''));
                            ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($label); ?></strong>
                                <span class="text-muted">(#<?php echo (int)($file['id'] ?? 0); ?>)</span>
                                <div class="pull-right">
                                    <a href="<?php echo $file_url; ?>" class="btn btn-xs btn-info" target="_blank">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-warning" style="margin-bottom: 0;">
                        No joining files found for this applicant.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel">
            <div class="panel-body text-center">
                <a href="<?php echo base_url('index.php?union/pending_members/approve/' . (int)$pending['id']); ?>"
                   class="btn btn-success btn-lg"
                   onclick="return confirm('Approve this applicant and transfer them to Members?');">
                    <i class="fa fa-check"></i> Approve
                </a>
                <a href="<?php echo base_url('index.php?union/pending_members/reject/' . (int)$pending['id']); ?>"
                   class="btn btn-danger btn-lg"
                   style="margin-left: 10px;"
                   onclick="return confirm('Reject and remove this pending application?');">
                    <i class="fa fa-times"></i> Reject
                </a>
            </div>
        </section>
    </div>
</div>
