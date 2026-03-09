<?php
$this->db->select("*");
$this->db->from("branches");
$this->db->where("status",1);
$branches = $this->db->get()->result_array();
?>
<div class="row">
	<div class="col-md-12">
	<div class="col-lg-12">
								<section class="panel">
									<header class="panel-heading">
										<div class="panel-actions">
											<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
											<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
										</div>
						
										<h2 class="panel-title">Report Date Picker</h2>
									</header>
									<div class="panel-body">
									<?php echo form_open(base_url() . 'index.php?union/branchreport/' , array('class' => 'form-horizontal form-bordered validate','target'=>'_top'));?>					
                                    <div class="form-group">
												<label class="col-md-3 control-label">Date Range</label>
												<div class="col-md-6">
													<div class="input-daterange input-group" data-plugin-datepicker>
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" class="form-control" name="startdate">
														<span class="input-group-addon">To</span>
														<input type="text" class="form-control" name="enddate">
													</div>
												</div>
											</div>
                                            <div class="form-group">
												<label class="col-md-3 control-label">Payment Type</label>
												<div class="col-md-6">
													<select data-plugin-selectTwo class="form-control populate" id="branch" name="branch" style="width: 100%">
														<option value=""><?php echo get_phrase('choose_branch'); ?></option>
														<?php foreach ($payment_types as $row): ?>
														<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
											
						
											<div class="form-group">
							  <div class="col-sm-offset-7 col-sm-5">
								  <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i> View Subventions Report</button>
							  </div>
							</div>
						
											
						
										</form>
									</div>
								</section>
							</div>
						</div>



		
	
			


   </div>
</div>
