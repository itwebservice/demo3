<?php
include "../../model/model.php";

$task_id = $_POST['task_id'];

$sq_task = mysqli_fetch_assoc(mysqlQuery("select * from tasks_master where task_id='$task_id'"));
$extra_note = $sq_task['extra_note'];
?>
<div class="modal fade" id="task_extra_note_modal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Task Note</h4>
      </div>
      <div class="modal-body">
        
		<blockquote>
			<p><?= $extra_note ?></p>
		</blockquote>

      </div>     
    </div>
  </div>
</div>

<script>
$('#task_extra_note_modal').modal('show');
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>