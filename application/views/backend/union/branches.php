<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<div class="row">
	<div class="col-md-12">

		<!---CONTROL TABS START-->
		<div class="tabs">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab"><i class="fa fa-list"></i> 
					<?php echo get_phrase('all_branches');?>
						</a></li>
			<li>
				<a href="#add" data-toggle="tab"><i class="fa fa-plus-circle"></i>
					<?php echo get_phrase('new_branch'); ?>
						</a></li>
		</ul>
		<!---CONTROL TABS END-->

		<div class="tab-content">
		<br>
			<!--TABLE LISTING STARTS-->
			<div class="tab-pane box active" id="list">
				<table class="table table-bordered table-striped mb-none" id="datatable-tabletools" >
			<thead>
				<tr>
                                        <th>
						<div>
							<?php echo get_phrase('#');?>
						</div>
					</th>
					<th>
						<div>
							<?php echo get_phrase('name');?>
						</div>
					</th>
					<th>
						<div>
							<?php echo get_phrase('managed_by');?>
						</div>
					</th>
					<th>
						<div>
							<?php echo get_phrase('contacts');?>
						</div>
					</th>

					<th>
						<div>
							<?php echo get_phrase('options');?>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
			
				<?php
				
				$i=1;
				foreach ( $branches as $row ): ?>
				<tr>
                    <td>
						<?php echo $i++;?>
					</td>
					<td>
						<?php echo $row['name'];?>
					</td>
					<td><?php
					if (!empty($row['managed_by'])) {
						$member = $this->db
							->get_where('members', ['id' => $row['managed_by']])
							->row();

						echo $member->name ?? '';
					} else {
						echo '';
					}
					?>
					</td>
					<td><?php
					if (!empty($row['managed_by']) && isset($member)) {
						echo $member->cellnumber ?? '';
					} else {
						echo '';
					}
					?>
					</td>
					<td>

						<!-- VIEW CLIENT DETAILS LINK -->
						<a href="<?php echo base_url(); ?>index.php?union/report_per_branch/<?php echo $row['id'];?>" class="btn btn-xs btn-info" data-placement="top" data-toggle="tooltip" 
						data-original-title="View Branch Report">
                        <i class="fa fa-eye"></i>
                        </a>


						<!-- CLIENT EDITING LINK -->

						<a href="<?php echo base_url(); ?>index.php?union/edit_branch/<?php echo $row['id'];?>" class="btn btn-xs btn-success" data-placement="top" data-toggle="tooltip" 
						data-original-title="<?php echo get_phrase('edit');?>" >
                        <i class="fa fa-pencil"></i>
                        </a>
						

						<!-- CLIENT DELETION LINK -->
						<a href="#" class="btn btn-xs btn-danger" data-placement="top" data-toggle="tooltip"
						 data-original-title="<?php echo get_phrase('delete');?>" onClick="confirm_modal('<?php echo base_url();?>index.php?union/branches/delete/<?php echo $row['id'];?>');">
                        <i class="fa fa-trash"></i>
                        </a>			

					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
			</div>
			<!--CREATION FORM STARTS-->
			<div class="tab-pane box" id="add" style="padding: 5px">
				<div class="box-content">
					<?php echo form_open(base_url() . 'index.php?union/branches/create' , array('class' => 'form-horizontal form-bordered validate','enctype'=>'multipart/form-data'));?>
					<div class="form-group">
					<label class="col-md-3 control-label">
						<?php echo get_phrase('name');?>
					</label>

					<div class="col-md-7">
				<input type="text" class="form-control" name="name" required title="<?php echo get_phrase('value_required');?>" value="" autofocus>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">
						<?php echo get_phrase('managed_by'); ?> <span class="required">*</span>
					</label>

					<div class="col-md-7">
						<!-- Hidden field that will hold the selected member's ID -->
						<input type="hidden" name="managed_by" id="selected_member_id" value="" required>

						<!-- Search input -->
						<input type="text" id="managed_by_search" class="form-control" 
							placeholder="Search by surname, name, ID number, employee no..." 
							autocomplete="off" required>

						<!-- Results dropdown-like container -->
						<div id="managed_by_results" style="display:none; 
							position:absolute; z-index:1000; width:100%; max-height:300px; 
							overflow-y:auto; background:#fff; border:1px solid #ccc; 
							box-shadow:0 4px 8px rgba(0,0,0,0.1); margin-top:2px;">
						</div>

						<!-- Optional: Show selected member's info after pick -->
						<div id="selected_member_info" style="display:none; margin-top:8px; padding:8px; background:#f8f9fa; border:1px solid #ddd;">
							<strong>Selected:</strong> <span id="selected_name"></span><br>
							<small>ID: <span id="selected_idnumber"></span> • Cell: <span id="selected_cell"></span></small>
						</div>
					</div>
				</div>	
						<div class="form-group">
							  <div class="col-sm-offset-3 col-sm-5">
								  <button type="submit" class="btn btn-primary"><?php echo get_phrase('add_branch');?></button>
							  </div>
							</div>
					</form>                
				</div>                
			</div>
			<!--CREATION FORM ENDS-->

		</div>
	</div>
   </div>
</div>
<script>

$(document).ready(function() {

		// Hide results when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('#managed_by_search, #managed_by_results').length) {
				$('#managed_by_results').hide();
			}
		});

		// Live search on typing
		$('#managed_by_search').on('keyup', function() {
			var search = $(this).val().trim();

			if (search.length < 2) {
				$('#managed_by_results').html('').hide();
				return;
			}

			$.ajax({
				url: "<?= base_url('index.php?union/search_members') ?>",   // ← your endpoint
				method: 'POST',
				data: { search: search },
				dataType: 'json',
				success: function(res) {
					$('#managed_by_results').html(''); // clear previous

					if (res.success && res.members && res.members.length > 0) {
						let html = '';
						res.members.forEach(function(m) {
							html += `<div class="member-result" style="padding:10px; border-bottom:1px solid #eee; cursor:pointer;"
										data-id="${m.id}" 
										data-idnumber="${m.idnumber || ''}" 
										data-name="${m.surname} ${m.name}" 
										data-cell="${m.cellnumber || ''}">
										<strong>${m.surname} ${m.name}</strong><br>
										<small>ID: ${m.idnumber || 'N/A'} • Emp: ${m.employeeno || 'N/A'} • Cell: ${m.cellnumber || 'N/A'}</small>
									</div>`;
						});
						$('#managed_by_results').html(html).show();
					} else {
						$('#managed_by_results').html('<div style="padding:12px; text-align:center; color:#777;">No members found</div>').show();
					}
				},
				error: function() {
					$('#managed_by_results').html('<div style="padding:12px; text-align:center; color:#d00;">Error searching members</div>').show();
				}
			});
		});

		// Click on result → fill fields
		$('#managed_by_results').on('click', '.member-result', function() {
			var name     = $(this).data('name');
			var idnumber = $(this).data('idnumber');
			var cell     = $(this).data('cell');
			var id       = $(this).data('id');

			// Fill hidden input (this is what gets submitted)
			$('#selected_member_id').val(id);

			// Show selected info
			$('#selected_name').text(name);
			$('#selected_idnumber').text(idnumber);
			$('#selected_cell').text(cell);
			$('#selected_member_info').show();

			// Copy name to search input (looks like selected)
			$('#managed_by_search').val(name);

			// Hide results
			$('#managed_by_results').hide();

			// Optional: enable submit button or show other fields
			// $('#submit_btn').prop('disabled', false);
		});

});

</script>