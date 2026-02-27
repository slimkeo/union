<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">
				
				    <div class="sidebar-header">
				        <div class="sidebar-title">
				            Navigation
				        </div>
				        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
				            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
				        </div>
				    </div>
	<div class="nano">
		<div class="nano-content">
			<nav id="menu" class="nav-main" role="navigation">
				<ul class="nav nav-main">

			<!-- DASHBOARD -->
			<li class="<?php if ($page_name == 'dashboard') echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/dashboard">
					<i class="fa fa-tachometer"></i>
					<span><?php echo get_phrase('dashboard'); ?></span>
				</a>
			</li> 
			<!-- Manage  Payments -->
			<li class="nav-parent <?php
				if ($page_name == 'payments' ||
						$page_name == 'delete_member' || $page_name == 'upload_spreadsheet' || $page_name == 'upload_spreadsheet_process' )
					echo 'nav-expanded nav-active';
				?> ">
					<a href="#">
						<i class="fa fa-money"></i>
						<span>Payments</span>
					</a>
				<ul class="nav nav-children">
					<li class="<?php if ($page_name == 'payments' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/payments">
							 <i class="fa fa-address-book"></i>
							<span>Pay Subscriptions</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'upload_spreadsheet' || $page_name == 'upload_spreadsheet_process') echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/upload_spreadsheet">
							 <i class="fa fa-address-book"></i>
							<span>Upload Spreadsheet</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'pay_merchandise' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/pay_merchandise">
							 <i class="fa fa-address-book-o"></i>
							<span>Merchandise Payments</span>
						</a>
					</li>								
				</ul>
			</li>	
			<!-- Manage  Members and Branches -->			
			<li class="nav-parent <?php
				if ($page_name == 'members' ||
						$page_name == 'detailed_meetings' || $page_name == 'detailed_meetings')
					echo 'nav-expanded nav-active';
				?> ">
					<a href="#">
						<i class="fa fa-users"></i>
						<span>Manage Members</span>
					</a>
				<ul class="nav nav-children">
					<li class="<?php if ($page_name == 'members' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/members">
							 <i class="fa fa-address-book"></i>
							<span>All Members</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'branches' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/branches">
							 <i class="fa fa-address-book-o"></i>
							<span>Branches</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'upload_members_spreadsheet' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/upload_members_spreadsheet">
							 <i class="fa fa-address-book-o"></i>
							<span>Upload Google Form CSV File</span>
						</a>
					</li>														
				</ul>
			</li>	
			<li class="<?php if ($page_name == 'categories'|| $page_name=='claim_details' ) echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/claims">
					 <i class="fa fa-slideshare"></i>
					<span><?php echo get_phrase('manage_claims'); ?></span>
				</a>
			</li>  			
			

			<!-- manage sms -->
			<?php if ($this->session->userdata('level')==1) { ?>
			<li class="nav-parent <?php
				if ($page_name == 'communication' ||
						$page_name == 'sms_batch_invite' || $page_name == 'sms_communique')
					echo 'nav-expanded nav-active';
				?> ">
					<a href="#">
						<i class="fa fa-bullhorn"></i>
						<span>SMS Communication</span>
					</a>
				<ul class="nav nav-children">
					<li class="<?php if ($page_name == 'sms_batch_invite' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/sms_batch_invite">
							 <i class="fa fa-address-book"></i>
							<span>Invite SMS</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'sms_communique' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/sms_communique">
							 <i class="fa fa-address-book-o"></i>
							<span>SMS Communiqua</span>
						</a>
					</li>									
				</ul>
			</li>
			<!-- Manage  Payments -->
			<li class="nav-parent <?php
				if ($page_name == 'reports' ||
						$page_name == 'date_range_reports' || $page_name == 'date_range' || $page_name == 'upload_spreadsheet_process' )
					echo 'nav-expanded nav-active';
				?> ">
					<a href="#">
						<i class="fa fa-money"></i>
						<span>Reports</span>
					</a>
				<ul class="nav nav-children">
					<li class="<?php if ($page_name == 'payments' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/date_range">
							 <i class="fa fa-calendar"></i>
							<span>Date Range Reports</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'per_branch' || $page_name == 'per_branch_report') echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/per_branch">
							 <i class="fa fa-users"></i>
							<span> Per Branch</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'per_status' ||  $page_name == 'per_status_report') echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/per_status">
							 <i class="fa fa-address-book-o"></i>
							<span>Per Employement Status</span>
						</a>
					</li>	
					<li class="<?php if ($page_name == 'subventions' ||  $page_name == 'subventions_report') echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/subventions">
							 <i class="fa fa-address-book-o"></i>
							<span>Subventions Reports</span>
						</a>
					</li>								
				</ul>
			</li>			
			<?php } ?>
			<!-- ADMIN MANAGEMENT PANEL -->
			<?php if ($this->session->userdata('level') == 1 || $this->session->userdata('level') == 3  ) { ?>		
			<li class="nav-parent <?php
				if ($page_name == 'momo_agm' ||
						$page_name == 'detailed_meetings' || $page_name == 'detailed_meetings')
					echo 'nav-expanded nav-active';
				?> ">
					<a href="#">
						<i class="fa fa-calendar-check-o"></i>
						<span>Manage Events</span>
					</a>
				<ul class="nav nav-children">
					<li class="<?php if ($page_name == 'detailed_meetings' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/manage_attendance">
							 <i class="fa fa-user"></i>
							<span>Manage Attendance</span>
						</a>
					</li>
					<li class="<?php if ($page_name == 'momo_agm' ) echo 'nav-active'; ?> ">
						<a href="<?php echo base_url(); ?>index.php?union/momo_pay">
							 <i class="fa fa-user"></i>
							<span><?php echo get_phrase('pay_with_momo'); ?></span>
						</a>
					</li>
				</ul>
			</li>
				<?php } ?>	
			<!-- ADMIN MANAGEMENT PANEL -->
			<?php if ($this->session->userdata('level') == 1) { ?>						
			<li class="nav-parent <?php
			if ($page_name == 'manage_users' ||
					$page_name == 'manage_events' || $page_name == 'security_settings' || $page_name == 'manage_system' )
				echo 'nav-expanded nav-active';
			?> ">
				<a href="#">
					<i class="fa fa-street-view"></i>
					<span><?php echo get_phrase('administrative'); ?></span>
				</a>
				<ul class="nav nav-children">
					<!-- \Manage USers -->
			<li class="<?php if ($page_name == 'manage_users') echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/manage_users">
					<i class="fa fa-slideshare"></i> 
					<span><?php echo get_phrase('manage_users'); ?></span>
				</a>
			</li>

					<!-- events LIST Addition -->
			<li class="<?php if ($page_name == 'manage_events') echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/manage_events">
					<i class="fa fa-bullhorn"></i> 
					<span><?php echo get_phrase('annual_general_events'); ?></span>
				</a>
			</li>						<!-- manage System -->
			<li class="<?php if ($page_name == 'manage_system' ) echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/manage_system">
					 <i class="fa fa-circle-o"></i>
					<span><?php echo get_phrase('manage_system'); ?></span>
				</a>
			</li>
					<!-- manage Security settings -->
			<li class="<?php if ($page_name == 'security_settings') echo 'nav-active'; ?> ">
				<a href="<?php echo base_url(); ?>index.php?union/security_settings">
					<i class="fa fa-unlock-alt"></i> 
					<span><?php echo get_phrase('security_settings'); ?></span>
				</a>
			</li>
			
			
			
				</ul>
			</li>
			
				<?php } ?>	
	

		</ul>
	 </nav>

	</div>

	      <script>
				            // Maintain Scroll Position
				            if (typeof localStorage !== 'undefined') {
				                if (localStorage.getItem('sidebar-left-position') !== null) {
				                    var initialPosition = localStorage.getItem('sidebar-left-position'),
				                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
				                    
				                    sidebarLeft.scrollTop = initialPosition;
				                }
				            }
				        </script>
	</div>		
</aside>
<!-- end: sidebar -->


