<?php
require_once('db.php');
require_once('../model/Task.php');
require_once('../model/Response.php');

try {
  $writeDB = DB::connectWriteDB();
  $readDB = DB::connectReadDB();
}
catch(PDOException $ex) {
  error_log("Connection error - ">$ex, 0);
  $response = new Response();
  $response->setHttpStatusCode(500);
  $response->setSuccess(false);
  $response->addMessage("Database Connection Error");
  $response->send();
  exit();
}

if(array_key_exists("taskid",$_GET)) {

  $taskid = $_GET['taskid'];

  if($taskid == '' || !is_numeric($taskid)){
    $response = new Response();
    $response->setHttpStatusCode(400);
    $response->setSuccess(false);
    $response->addMessage("Task ID cannot be blank or must be numberic");
    $response->send();
    exit;
  }

  if($_SERVER['REQUEST_METHOD'] === 'GET') {

    try{

      $query = $readDB->prepare('select id, title, description, DATE_FORMAT(deadline, "%d/%m/%Y %H:%i") as deadline, completed from tbltasks where id = :taskid');
      $query->bindParam(':taskid',$taskid, PDO::PARAM_INT);
      $query->execute();

      $rowCount = $query->rowCount();

      if($rowCount === 0){
        $response = new Response();
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage("Task ID cannot be blank or must be numberic");
        $response->send();
        exit;
      }

      while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $task = new Task($row['id'],$row['title'],$row['description'],$row['deadline'],$row['completed']);
        $taskArray[] = $task->returnTaskArray();
      }

      $returnData = array();
      $returnData['rows_returned'] = $rowCount;
      $returnData['tasks'] = $taskArray;

      $response = new Response();
      $response->setHttpStatusCode(200);
      $response->setSuccess(true);
      $response->toCache(true);
      $response->setData($returnData);
      $response->send();

    }
    catch(TASKException $ex){
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage($ex->getMessage());
      $response->send();
    }
    catch(PDOException $ex){
      error_log("Database query error - ">$ex, 0);
      $response = new Response();
      $response->setHttpStatusCode(500);
      $response->setSuccess(false);
      $response->addMessage("Failed to get task");
      $response->send();
      exit();
    }

  }
  elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){

  }
  elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){

  }
  else {
    $response = new Response();
    $response->setHttpStatusCode(405);
    $response->setSuccess(false);
    $response->addMessage("Request method not allowed");
    $response->send();
    exit;
  }

}
 ?>
