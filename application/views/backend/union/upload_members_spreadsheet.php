<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i> Upload / Update Members from CSV
                </h3>
            </div>

            <div class="box-body">
                <div class="alert alert-info">
                    <strong>CSV File Requirements:</strong>
                    <ul style="margin: 10px 0 0 20px; padding-left: 0;">
                        <li>Must be a valid <strong>.csv</strong> file (preferably UTF-8 encoded).</li>
                        <li>Should have **11 columns** with headers in the first row.</li>
                        <li>Typical/expected columns<br>
                            <code>Timestamp, Last Name, First Names, Employment Number, School Code, School / Institution, ID Number, Cell Number(MOMO active Number), SNAT Union Branch, Employment Status, Confirmation & Consent</code>
                        </li>
                        <li><strong>MAKE SURE COLUMNS ARE IN THIS ORDER</strong></li>
                        <li>Cell numbers are normalized automatically:<br>
                            → 8 digits (e.g. <code>76404197</code>) becomes <code>26876404197</code><br>
                            → Already 11 digits starting with 268 are kept as-is
                        </li>
                        <li>Members are matched/updated by <strong>ID Number</strong> or <strong>Employment Number</strong>.</li>
                        <li><strong>SNAT Union Branch</strong> and <strong>Employment Status</strong> must match existing values exactly (case-sensitive).</li>
                        <li>Large files (10,000+ rows) are processed in batches of 500 rows automatically.</li>
                    </ul>
                </div>

                <?php echo form_open(
                    base_url() . 'index.php?union/upload_members_spreadsheet_do',
                    array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'uploadForm'
                    )
                ); ?>

                <div class="form-group">
                    <label class="col-sm-3 control-label required">Select CSV File</label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="file"
                                   name="csv_file"
                                   accept=".csv"
                                   class="form-control"
                                   required
                                   id="csvFileInput" />
                            <span class="input-group-btn">
                                <button type="submit"
                                        class="btn btn-success"
                                        id="submitBtn">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                            </span>
                        </div>
                        <p class="help-block" id="fileFeedback" style="margin-top: 8px;">
                            Only .csv files accepted
                        </p>
                    </div>
                </div>

                <!-- Upload progress indicator -->
                <div id="uploadingContainer" style="display:none; margin-top: 20px;">
                    <div class="col-sm-offset-3 col-sm-7">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%">
                                Uploading file... Please wait
                            </div>
                        </div>
                        <p class="text-primary text-center" style="margin-top: 10px;">
                            <i class="fa fa-spinner fa-spin"></i> Processing will start automatically after upload
                        </p>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>

            <div class="box-footer text-muted">
                <small>
                    Recommended: Export from Excel/Google Sheets as "CSV UTF-8" to avoid character encoding problems.
                </small>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Show selected file name + validate extension
    $('#csvFileInput').on('change', function() {
        var fileName = this.files.length > 0 ? this.files[0].name : '';
        var feedback = $('#fileFeedback');

        if (fileName) {
            if (fileName.toLowerCase().endsWith('.csv')) {
                feedback
                    .text('Selected: ' + fileName)
                    .removeClass('text-danger')
                    .addClass('text-success');
            } else {
                feedback
                    .text('Error: Please select a .csv file only')
                    .removeClass('text-success')
                    .addClass('text-danger');
                this.value = ''; // clear invalid file
            }
        } else {
            feedback
                .text('Only .csv files are accepted')
                .removeClass('text-success text-danger');
        }
    });

    // Handle form submit
    $('#uploadForm').on('submit', function(e) {
        var fileInput = $('#csvFileInput');

        if (!fileInput.val()) {
            alert('Please select a CSV file first.');
            e.preventDefault();
            return false;
        }

        if (!fileInput.val().toLowerCase().endsWith('.csv')) {
            alert('Only .csv files are allowed.');
            e.preventDefault();
            return false;
        }

        // Disable button & show progress
        $('#submitBtn').prop('disabled', true);
        $('#uploadingContainer').show();
        $('#fileFeedback').hide();
    });
});
</script>