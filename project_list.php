<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
            <?php if($_SESSION['login_type'] == "Admin"): ?>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project"><i class="fa fa-plus"></i> Add New project</a>
			</div>
            <?php endif; ?>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-condensed" id="list">
				<colgroup>
					<col width="5%">
					<col width="35%">
					<col width="15%">
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Project</th>
						<th>Project Code</th>
						<th>Category</th>
						<th>Date Started</th>
						<th>Due Date</th>
						<th>Days Left</th>
						<th>Amount Budgetted</th>
						<th>Amount Spent</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
					$qry = $conn->query("SELECT * FROM project_list order by name asc");
					while($row= $qry->fetch_assoc()):
						$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
						unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
						$desc = strtr(html_entity_decode($row['description']),$trans);
						$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);

					 	$tprog = $conn->query("SELECT * FROM task_list where id = {$row['id']}")->num_rows;
		                $cprog = $conn->query("SELECT * FROM task_list where id = {$row['id']} and status = 3")->num_rows;
						$prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
		                $prog = $prog > 0 ?  number_format($prog,2) : $prog;
		                $prod = $conn->query("SELECT * FROM user_productivity where id = {$row['id']}")->num_rows;
						if($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
						if($prod  > 0  || $cprog > 0)
		                  $row['status'] = 2;
		                else
		                  $row['status'] = 1;
						elseif($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
						$row['status'] = 4;
						endif;
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td>
							<p><b><?php echo ucwords($row['name']) ?></b></p>
							<p class="truncate"><?php echo strip_tags($desc) ?></p>
						</td>
						<td><b><?php echo $row['contract_code'] ?></b></td>
						<td><?php echo ucwords($row['type']) ?></td>
						<td><b><?php echo date("M d, Y",strtotime($row['start_date'])) ?></b></td>
						<td><b><?php echo date("M d, Y",strtotime($row['end_date'])) ?></b></td>
						<td>
						<style type="text/css">
							#progressbar {
							 background-color: #99cc66;
							  width: 50%; 
							  height: 20px;
							  border-radius: 10px;
							}
							#completed {
								position: relative;
								height: 100%;
								background-color: #99cc66;
							}
							</style>
							<?php
							$date1 = strtotime($row['start_date']);
							$date2 = strtotime($row['end_date']);
							$today = time();

							if ($today < $date1) {
								$percentage = "error";
							} elseif ($today > $date1) {
								$daysleft = $date2-$date1;
								$percentage = round((($daysleft/24)/60)/60);
							}
							?>
							<div id="">
							<div><span><?php echo $percentage; ?>days left</span></div>
							</div>
							</td>
							<td><p><b><?php echo "₦" . number_format($row['estimates']) ?></b></p></td>
						<td><?php echo "₦" . number_format($row['amt_used']) ?></td>
						
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu" style="">
		                      <a class="dropdown-item view_project" href="./index.php?page=view_project&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
							  <?php if($_SESSION['login_type'] == "Admin"): ?>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_project&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item delete_project" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
		                  <?php endif; ?>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style>
	table p{
		margin: unset !important;
	}
	table td{
		vertical-align: middle !important
	}
</style>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	
	$('.delete_project').click(function(){
	_conf("Are you sure to delete this project?","delete_project",[$(this).attr('data-id')])
	})
	})
	function delete_project($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_project',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>