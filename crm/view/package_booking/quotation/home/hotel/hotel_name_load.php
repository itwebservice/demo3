<?php 
include "../../../../../model/model.php"; 
$city_id = $_GET['city_id'];
?>
<option value="">Select Hotel</option>
<?php
$sq_hotel = mysqlQuery("select * from hotel_master where city_id='$city_id' and active_flag='Active'");
while($row_hotel = mysqli_fetch_assoc($sq_hotel))
{
?>
	<option value="<?php echo $row_hotel['hotel_id'] ?>"><?php echo $row_hotel['hotel_name'] ?></option>
<?php	
}

?>