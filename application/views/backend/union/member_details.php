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
                        <span class="info-value"><?php echo '058'.$row['id']; ?></span>
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
