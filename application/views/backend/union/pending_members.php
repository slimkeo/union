<div class="row">
	<div class="col-md-12">

		<!---CONTROL TABS START-->
		<div class="tabs">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab"><i class="fa fa-list"></i>
					<?php echo get_phrase('pending_members');?>
				</a>
			</li>
		</ul>
		<!---CONTROL TABS END-->

		<div class="tab-content">
		<br>

			<!--TABLE LISTING STARTS-->
			<div class="tab-pane box active" id="list">

				<table class="table table-bordered table-striped mb-none" id="datatable-tabletools1">
			<thead>
				<tr>
                    <th>#</th>
                    <th>ID Number</th>
					<th>Emp NO:</th>
                    <th>Surname</th>
                    <th>Name</th>
                    <th>Cell Number</th>
                    <th>Gender</th>
                    <th>School Code</th>
					<th><?php echo get_phrase('options');?></th>
				</tr>
			</thead>

	    <tbody>
	        <!-- Leave empty. DataTables will fill this -->
	    </tbody>

		</table>
		</div>
		<!--TABLE LISTING ENDS-->

		</div>
	</div>
   </div>
</div>
<script>
$(document).ready(function() {

    $('#datatable-tabletools1').DataTable({

		processing: true,
		serverSide: true,

		ajax: {
			url: "<?php echo base_url('index.php?union/get_pending_members');?>",
			type: "POST"
		},

		dom: 'Bfrtip',

		buttons: [

			{
				extend: 'copy',
				text: 'Copy'
			},

			{
				extend: 'excelHtml5',
				text: 'Excel',

				action: function (e, dt, button, config) {

					var self = this;

					dt.one('preXhr', function (e, s, data) {
						data.start = 0;
						data.length = -1;
					});

					dt.one('draw', function (e, settings) {

						$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);

						dt.one('preXhr', function (e, s, data) {
							data.start = settings._iDisplayStart;
							data.length = settings._iDisplayLength;
						});

						setTimeout(function () {
							dt.ajax.reload(null, false);
						}, 100);
					});

					dt.ajax.reload();
				}
			},

			{
				extend: 'pdfHtml5',
				text: 'PDF',

				action: function (e, dt, button, config) {

					var self = this;

					dt.one('preXhr', function (e, s, data) {
						data.start = 0;
						data.length = -1;
					});

					dt.one('draw', function (e, settings) {

						$.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);

						dt.one('preXhr', function (e, s, data) {
							data.start = settings._iDisplayStart;
							data.length = settings._iDisplayLength;
						});

						setTimeout(function () {
							dt.ajax.reload(null, false);
						}, 100);
					});

					dt.ajax.reload();
				}
			},

			{
				extend: 'print',
				text: 'Print',

				action: function (e, dt, button, config) {

					var self = this;

					dt.one('preXhr', function (e, s, data) {
						data.start = 0;
						data.length = -1;
					});

					dt.one('draw', function (e, settings) {

						$.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);

						dt.one('preXhr', function (e, s, data) {
							data.start = settings._iDisplayStart;
							data.length = settings._iDisplayLength;
						});

						setTimeout(function () {
							dt.ajax.reload(null, false);
						}, 100);
					});

					dt.ajax.reload();
				}
			}

		]

    });

});
</script>
