<?php
require_once('Task.php');

try {
  $task = new Task(1, "Title Here", "Description here", "01/01/2019 12:00", "Y");
  header('Content-type: application/json;charset=UTF-8');
  echo json_encode($task->returnTaskArray());
}
catch(TaskException $ex) {
  echo "Error: ".$ex->getMessage();
}

 ?>
