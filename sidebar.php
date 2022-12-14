  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
   	<a href="./" class="brand-link">
        <?php if($_SESSION['login_type'] == "Admin"): ?>
        <h3 class="text-center p-0 m-0"><b>ADMIN</b></h3>
        <?php else: ?>
        <h3 class="text-center p-0 m-0"><b>USER</b></h3>
        <?php endif; ?>
    	</a>
    </div>
	  
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
		
          <li class="nav-item dropdown">
            <a href="./index1.php" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>  
		  
	<?php if($_SESSION['login_type'] == "Admin"): ?>
           <li class="nav-item">
                <a href="#" class="nav-link nav-estimates">
                   <i class="nav-icon fas fa-layer-group"></i>
			<p>Estimate Project<i class="right fas fa-angle-left"></i></p>
                 </a>
				 <ul class="nav nav-treeview">
					<li class="nav-item">
						<a href="./index1.php?page=estimates" class="nav-link nav-estimates tree-item">
						<i class="fas fa-angle-right nav-icon"></i>
						<p>Estimate</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="./index1.php?page=estimate_list" class="nav-link nav-estimate_list tree-item">
						<i class="fas fa-angle-right nav-icon"></i>
						<p>Estimate_list</p>
						</a>
					</li>
				</ul>	
          </li>
          <?php endif; ?> 
		  
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Projects
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
		  
            <ul class="nav nav-treeview">
            <?php if($_SESSION['login_type'] == "Admin"): ?>
              <li class="nav-item">
                <a href="./index1.php?page=new_project" class="nav-link nav-new_project tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
            <?php endif; ?>
		    
              <li class="nav-item">
                <a href="./index1.php?page=project_list" class="nav-link nav-project_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
		    
            </ul>
          </li> 
		
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_project nav-view_project">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Tasks
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
		  
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index1.php?page=task_list" class="nav-link nav-task_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Add/View Tasks</p>
                </a>
              </li>
		    
              <li class="nav-item">
                <a href="./index1.php?page=assign_tasks" class="nav-link nav-assign_tasks tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Assign tasks</p>
                </a>
              </li>
		    
              <li class="nav-item">
                <a href="./index1.php?page=assign_tasks" class="nav-link nav-assigned_tasks tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Assigned tasks</p>
                </a>
              </li>
		    
            </ul>
          </li>
		
          <?php if($_SESSION['login_type'] == "Admin"): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_user">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./index1.php?page=new_user" class="nav-link nav-new_user tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Add New</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./index1.php?page=user_list" class="nav-link nav-user_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
		
		   <?php if($_SESSION['login_type'] == "Admin"): ?>
           <li class="nav-item">
                <a href="./index1.php?page=log" class="nav-link nav-log">
                  <i class="fas fa-th-list nav-icon"></i>
                  <p>Log</p>
                </a>
          </li>
          <?php endif; ?>
		
           <li class="nav-item">
                <a href="./" class="nav-link nav-index">
                  <i class="fa fa-home nav-icon"></i>
                  <p>Home Page</p>
                </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})
  </script>
