<?php

class Login
{
  function __construct()
  {
    if($this->checkMethod() == 1){
      echo "Method ".$_SERVER['REQUEST_METHOD']." is not allowed. Only POST method";
      return;
    }
    $this->Output("Welcome to login page");
  }

  function Output($value){
    echo $value;
  }

  function checkMethod(){
    if($_SERVER['REQUEST_METHOD'] != "POST"){
      return 1;
    }
    return 0;
  }

}

 ?>
