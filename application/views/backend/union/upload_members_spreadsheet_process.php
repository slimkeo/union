<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i> Processing Members CSV Upload
                </h3>
            </div>
            <div class="box-body">
                <p>Your members CSV file is being processed in batches of 500 rows. Please wait...</p>
                
                <!-- Progress Bar -->
                <div class="progress" style="height: 25px; margin-bottom: 20px;">
                    <div class="progress-bar progress-bar-striped active" id="progressBar" role="progressbar" style="width: 0%">
                        <span id="progressText">0%</span>
                    </div>
                </div>

                <!-- Status Messages -->
                <div id="statusMessages" style="max-height: 220px; overflow-y: auto; margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px;">
                    <p><i class="fa fa-spinner fa-spin"></i> Starting member import process...</p>
                </div>

                <!-- Results -->
                <div id="resultsContainer" style="display: none; margin-top: 30px;">
                    <div class="alert alert-success" id="successAlert" style="display: none;"></div>
                    <div class="alert alert-danger" id="errorAlert" style="display: none;"></div>
                    
                    <table class="table table-bordered table-condensed" id="resultsTable">
                        <tr>
                            <td><strong>New Members Added:</strong></td>
                            <td id="totalInserted">0</td>
                        </tr>
                        <tr>
                            <td><strong>Existing Members Updated:</strong></td>
                            <td id="totalUpdated">0</td>
                        </tr>
                        <tr>
                            <td><strong>Rows Skipped:</strong></td>
                            <td id="totalSkipped">0</td>
                        </tr>
                        <tr>
                            <td><strong>Rows with Errors:</strong></td>
                            <td id="totalErrors">0</td>
                        </tr>
                    </table>

                    <!-- Error / Problematic Records List -->
                    <div id="errorsContainer" style="display: none; margin-top: 25px;">
                        <h5><i class="fa fa-exclamation-triangle text-danger"></i> Sample of Problematic Records (First 10 shown)</h5>
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Employee No</th>
                                    <th>ID Number</th>
                                    <th>Error Description</th>
                                </tr>
                            </thead>
                            <tbody id="errorsTableBody"></tbody>
                        </table>
                    </div>

                    <div style="margin-top: 30px; text-align: center;">
                        <a href="<?php echo base_url() ?>index.php?union/upload_members_spreadsheet" class="btn btn-primary btn-lg">
                            <i class="fa fa-arrow-left"></i> Back to Upload Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var sessionId     = '<?php echo $session_id; ?>';
    var offset        = 0;
    var totalInserted = 0;
    var totalUpdated  = 0;
    var totalSkipped  = 0;
    var totalErrors   = 0;
    var allErrors     = [];
    var isProcessing  = true;

    // Start processing immediately
    processChunk();

    function processChunk() {
        if (!isProcessing) return;

        $.ajax({
            url: '<?php echo base_url() ?>index.php?union/upload_members_spreadsheet_chunk',
            type: 'POST',
            dataType: 'json',
            data: {
                session_id: sessionId,
                offset: offset
            },
            success: function(response) {
                if (response.success) {
                    // Accumulate totals
                    totalInserted += parseInt(response.inserted || 0);
                    totalUpdated  += parseInt(response.updated  || 0);
                    totalSkipped  += parseInt(response.skipped  || 0);
                    totalErrors   += parseInt(response.error_count || 0);

                    // Collect errors
                    if (response.errors && response.errors.length > 0) {
                        allErrors = allErrors.concat(response.errors);
                    }

                    // Progress calculation (cap at 95% until finished)
                    var processedSoFar = offset + response.rows_processed;
                    var percent = Math.min(Math.round(processedSoFar / 15000 * 100), 95);
                    updateProgress(percent);

                    // Status line
                    addStatusMessage(
                        '✓ Batch processed: ' + response.rows_processed + ' rows | ' +
                        'New: ' + response.inserted + ' | ' +
                        'Updated: ' + response.updated + ' | ' +
                        'Skipped/Err: ' + (response.skipped + response.error_count)
                    );

                    // Next batch or finish
                    if (response.has_more) {
                        offset = response.next_offset;
                        setTimeout(processChunk, 800); // 800ms delay to avoid overloading server
                    } else {
                        completeProcessing();
                    }
                } else {
                    addStatusMessage('❌ ' + (response.error || 'Unknown server error'), 'error');
                    isProcessing = false;
                }
            },
            error: function(xhr, status, error) {
                addStatusMessage('❌ AJAX request failed: ' + error, 'error');
                isProcessing = false;
            }
        });
    }

    function updateProgress(percent) {
        $('#progressBar').css('width', percent + '%');
        $('#progressText').text(percent + '%');
    }

    function addStatusMessage(msg, type = 'success') {
        var className = (type === 'error') ? 'text-danger' : 'text-success';
        $('#statusMessages').append('<p class="' + className + '"><small>' + msg + '</small></p>');
        $('#statusMessages').scrollTop($('#statusMessages')[0].scrollHeight);
    }

    function completeProcessing() {
        isProcessing = false;
        
        // Finish progress bar
        updateProgress(100);
        $('#progressBar').removeClass('active');

        // Update summary
        $('#totalInserted').text(totalInserted);
        $('#totalUpdated').text(totalUpdated);
        $('#totalSkipped').text(totalSkipped);
        $('#totalErrors').text(totalErrors);

        // Final message
        var msg = '<strong>✓ Import completed successfully!</strong><br>' +
                  '<strong>' + totalInserted + '</strong> new members added<br>' +
                  '<strong>' + totalUpdated + '</strong> existing members updated';

        if (totalSkipped > 0 || totalErrors > 0) {
            msg += '<br><br><strong>Some issues occurred:</strong><br>' +
                   totalSkipped + ' rows skipped (empty or invalid)<br>' +
                   totalErrors + ' rows had errors (invalid phone, branch not found, etc.)';
        } else {
            msg += '<br><br><strong>No errors or skipped rows — perfect upload!</strong>';
        }

        $('#successAlert').html(msg).show();

        // Show errors if any
        if (allErrors.length > 0) {
            $('#errorsContainer').show();
            allErrors.slice(0, 10).forEach(function(item) {
                var row = '<tr>' +
                    '<td>' + (item.employeeno  || '-') + '</td>' +
                    '<td>' + (item.idnumber    || '-') + '</td>' +
                    '<td class="text-danger">' + (item.error || 'Unknown error') + '</td>' +
                    '</tr>';
                $('#errorsTableBody').append(row);
            });

            if (allErrors.length > 10) {
                $('#errorsTableBody').append(
                    '<tr><td colspan="3" class="text-muted"><em>(and ' + (allErrors.length - 10) + ' more errors not shown)</em></td></tr>'
                );
            }
        }

        $('#resultsContainer').show();

        // Cleanup temp file on server
        $.ajax({
            url: '<?php echo base_url() ?>index.php?union/upload_members_spreadsheet_finish',
            type: 'POST',
            dataType: 'json',
            data: { session_id: sessionId }
        });

        addStatusMessage('✓ All processing finished.');
    }
});
</script>