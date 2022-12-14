<?php
session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where email = '".$email."' and password = '".md5($password)."'  ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 2;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = '".$student_code."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);

				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");

		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
					$_SESSION['login_id'] = $id;
				if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(!empty($password))
			$data .= " ,password=md5('$password') ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	function save_project(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(isset($user_ids)){
			$data .= ", user_ids='".implode(',',$user_ids)."' ";
		}
		// echo $data;exit;
		if(empty($id)){
			$save = $this->db->query("INSERT INTO project_list set $data");
		}else{
			$save = $this->db->query("UPDATE project_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_project(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM project_list where id = $id");
		if($delete){
			return 1;
		}
	}
	
	function save_task(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
			$sql="INSERT INTO task_list set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_progress(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'comment')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$dur = abs(strtotime("2020-01-01 ".$end_time)) - abs(strtotime("2020-01-01 ".$start_time));
		$dur = $dur / (60 * 60);
		$data .= ", time_rendered='$dur' ";
		// echo "INSERT INTO user_productivity set $data"; exit;
		if(empty($id)){
			$data .= ", user_id={$_SESSION['login_id']} ";
			
			$save = $this->db->query("INSERT INTO user_productivity set $data");
		}else{
			$save = $this->db->query("UPDATE user_productivity set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM user_productivity where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_report(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while($row= $get->fetch_assoc()){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);

	}
	function save_estimate(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
			$sql="INSERT INTO estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function save_images(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'name')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		for($i=0;$i<count(array_filter($_FILES["images"]["name"]));$i++){
		if(empty($id)){
			$save = $this->db->query("INSERT INTO images set $data");
		}else{
			$save = $this->db->query("UPDATE images set $data where id = $id");
		}
		}
		if($save){
			return 1;
		}
	}	
	function save_substructure(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
			$save = $this->db->query("UPDATE substructure_report set $data where id = $id");
		}else{
			$sql="INSERT INTO substructure_report set $data";
			$save = $this->db->query("$sql");
		}
		if($save){
			return 1;
		}
	}
	function save_substructure2(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'report')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO substructure_excavation_of_column_bases set $data");
		}else{
			$save = $this->db->query("UPDATE substructure_excavation_of_column_bases set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}

	function save_firstestimate(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['substructureresult']=$_POST['substructureresult'];
			$sql="INSERT INTO substructure_estimates set $data";
			$sql="INSERT INTO substructure_report set $data";
			
			$save = $this->db->query("$sql");
			$save= $this->db->query("$sql2");
		}else{
			$save = $this->db->query("UPDATE substructure_report set $data where id = $id");
			
		}
		if($save){
			return 1;
		}
	}
	
	
	function save_secondestimate(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['concreteworkresult']=$_POST['concreteworkresult'];
			$sql="INSERT INTO concretework_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE concretework_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
	function save_thirdestimate(){
		extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['blockworkresult']=$_POST['blockworkresult'];
			$sql="INSERT INTO blockwork_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE blockwork_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}

function save_forthestimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['roofingresult']=$_POST['roofingresult'];
			$sql="INSERT INTO roofing_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE roofing_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_windowsestimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['windowsresult']=$_POST['windowsresult'];
			$sql="INSERT INTO windows_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE windows_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_doorsestimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['doorsresult']=$_POST['doorsresult'];
			$sql="INSERT INTO doors_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE doors_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}

function save_fittings_fixtures_estimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['fittings_fixtures_result']=$_POST['fittings_fixtures_result'];
			$sql="INSERT INTO fittings_fixtures_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE fittings_fixtures_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_metal_works_estimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['metal_works_result']=$_POST['metal_works_result'];
			$sql="INSERT INTO metal_works_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE metal_works_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}	
	
function save_finishingsestimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['finishingsresult']=$_POST['finishingsresult'];
			$sql="INSERT INTO finishingsestimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE finishingsestimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}	
	
function save_paintings_decorations_estimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['paintings_decorations_result']=$_POST['paintings_decorations_result'];
			$sql="INSERT INTO paintings_decorations_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE paintings_decorations_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_mechanical_services_estimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION['mechanical_services_result']= $_POST['mechanical_services_result'];
			$sql="INSERT INTO mechanical_services_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE mechanical_services_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
function save_electrical_services_estimate(){
	extract($_POST);
    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
            if($k == 'name')
                $v = htmlentities(str_replace("'","&#x2019;",$v));
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }
    if(isset($user_ids)){
        $data .= ", user_ids='".implode(',',$user_ids)."' ";
    }
		if(empty($id)){
				$_SESSION["electrical_services_result"]= $_POST["electrical_services_result"];
			$sql="INSERT INTO electrical_services_estimates set $data";
			$save = $this->db->query("$sql");
		}else{
			$save = $this->db->query("UPDATE electrical_services_estimates set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	
}
