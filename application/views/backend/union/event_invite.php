<?php
$event = $this->db->get_where('events', array('id' => $event_id))->row();
$event_name = $event->description;
$time = $event->time;
$location = $event->location;
$date = $event->date;

// default message
$message =$event_name . " : " . $date . " " . $time . ", " . $location . ". SNAT UNION Members: Payslip/receipt and ID";
?>

<div class="row">
    <div class="col-md-12">

        <!-- PAGE TITLE -->
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-users"></i> <?php echo $event_name; ?> SMS Invitations
                </h3>
            </div>
        </div>

        <!-- FORM SECTION -->
        <div id="inviteForm" class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label>SMS Message</label>
                    <textarea id="smsMessage" class="form-control" rows="4"><?php echo $message. " OTP: 36912(example)"; ?></textarea>
                </div>

                <button class="btn btn-primary btn-lg" id="btnInviteAll">
                    <i class="fa fa-paper-plane"></i> Send Invitations
                </button>

            </div>
        </div>

        <!-- PROGRESS SECTION (HIDDEN INITIALLY) -->
        <div id="inviteProgressSection" class="panel panel-default" style="display:none;">
            <div class="panel-heading">
                <h4><i class="fa fa-spinner fa-spin"></i> Sending Invitations...</h4>
            </div>

            <div class="panel-body">

                <div class="progress">
                    <div id="inviteProgress" class="progress-bar progress-bar-success"
                         role="progressbar" style="width:0%">
                        0%
                    </div>
                </div>

                <div id="inviteLog"
                     style="background:#f9f9f9; border:1px solid #ddd; padding:10px;
                            height:250px; overflow-y:auto; margin-top:15px; font-size:13px;">
                </div>

                <button type="button" id="doneBtn" class="btn btn-success" style="display:none;">
                    Done
                </button>

            </div>
        </div>

    </div>
</div>
<script>
$(document).ready(function() {

    $("#btnInviteAll").click(function() {

        // get message
        const message = "<?php echo $message ?>";
        const event_id = "<?php echo $event_id ?>";

        if (!message.trim()) {
            alert("Please enter a message.");
            return;
        }

        // hide form, show progress
        $("#inviteForm").hide();
        $("#inviteProgressSection").show();

        startRealSending(message);
    });

    function startRealSending(message) {

        $("#inviteLog").html("");
        $("#inviteProgress").css("width", "0%").text("0%");
        $("#doneBtn").hide();

        const batchSize = 200;
        let offset = 0;

        $.ajax({
            url: "<?php echo base_url('index.php?union/invite_batch_init'); ?>",
            method: "POST",
            dataType: "json",
            success: function(initRes) {

                if (!initRes || !initRes.total) {
                    $("#inviteLog").append("<div class='text-danger'>Failed to get total members.</div>");
                    return;
                }

                const total = parseInt(initRes.total, 10);
                $("#inviteLog").append("Total members: " + total + "<br>");

                function processNextBatch() {

                    $.ajax({
                        url: "<?php echo base_url('index.php?union/invite_batch'); ?>",
                        method: "POST",
                        dataType: "json",
                        data: {
                            offset: offset,
                            limit: batchSize,
                            event_id: event_id,
                            message: message // send message to backend
                        },
                        success: function(res) {

                            const processed = res.processed || 0;
                            offset += processed;

                            if (res.logs) {
                                res.logs.forEach(function(line) {
                                    $("#inviteLog").append(line + "<br>");
                                });
                            }

                            const percent = Math.round((offset / total) * 100);
                            $("#inviteProgress").css("width", percent + "%").text(percent + "%");

                            $("#inviteLog").scrollTop($("#inviteLog")[0].scrollHeight);

                            if (offset < total) {
                                setTimeout(processNextBatch, 200);
                            } else {
                                $("#inviteLog").append("<b>All invitations sent. Total: " + res.total_success + "</b><br>");
                                $("#doneBtn").show();
                            }
                        },
                        error: function() {
                            $("#inviteLog").append("<div class='text-danger'>Batch failed.</div>");
                            $("#doneBtn").show();
                        }
                    });
                }

                processNextBatch();
            }
        });
    }

    // optional: reset UI
    $("#doneBtn").click(function() {
        $("#inviteProgressSection").hide();
        $("#inviteForm").show();
    });

});
</script>
