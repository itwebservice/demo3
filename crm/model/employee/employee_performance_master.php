<?php
$flag = true;
class employee_performance_master{

public function employee_performance_save()
{
	$emp_id = $_POST['emp_id'];
	$year = $_POST['year'];
	$month= $_POST['month'];
	$leadership = $_POST['leadership'];
	$communication = $_POST['communication'];
	$analytical_skills = $_POST['analytical_skills'];
	$ethics = $_POST['ethics'];
	$conceptual_thinking = $_POST['conceptual_thinking'];
	$teamwork = $_POST['teamwork'];
	$ave_ratings = $_POST['ave_ratings'];

	$created_at = date('Y-m-d');
	begin_t(); 
	$sq_count = mysqli_num_rows(mysqlQuery("select * from employee_performance_master where emp_id='$emp_id' and year='$year' and month='$month'"));

	if($sq_count>0){
		echo "error--Performance already added for this month details!!!";
	}
	else{
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from employee_performance_master"));
		$id = $sq_max['max'] + 1;
		$sq_sal = mysqlQuery("insert into employee_performance_master(id, emp_id, year,month, leadership, communication, analytical_skills, ethics, conceptual_thinking, teamwork, ave_ratings, created_at) values ( '$id', '$emp_id', '$year','$month', '$leadership', '$communication', '$analytical_skills', '$ethics', '$conceptual_thinking', '$teamwork', '$ave_ratings', '$created_at')");

		if($sq_sal){
			commit_t();
			echo "User Performance saved!";
			exit;
		}
		else{
			rollback_t();
			echo "error--Sorry, Performance not saved!";
			exit;
		}
	}
}
}
?>