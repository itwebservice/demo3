<?php include "../../model/model.php"; ?>
<?php
  $tourwise_id = $_GET['tourwise_id'];  
?>
<option value=""> Select Traveler Name </option>        
<?php
  $sq_tourwise = mysqli_fetch_assoc( mysqlQuery( "select traveler_group_id from tourwise_traveler_details where id='$tourwise_id'" ) );	
  $sq = mysqlQuery("select * from travelers_details where traveler_group_id= '$sq_tourwise[traveler_group_id]' and status='Active' ");
  while($row = mysqli_fetch_assoc($sq))
  {
   ?>
    <option value="<?php echo $row['traveler_id'] ?>"><?php echo $row['first_name']." ".$row['last_name']; ?></option>
   <?php 
  }  

?>