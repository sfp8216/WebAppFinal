<?php
	//all XHR calls for chat or game go through here
	//things sent in: 
	//	a - area (within the service layer - chat, game, etc)
	//	method - function to call (in the service layer folder)
	//	data - any subsequent data that is needed by the method I'm calling
    session_start();
	
	if(isset($_GET['method']) || isset($_POST['method'])){
		//include all the files that I need for area (a)
		foreach(glob("./svcLayer/".$_REQUEST['a']."/*.php") as $fileName){
			require $fileName;
		}

		$data=$_REQUEST['data'];
		$serviceMethod=$_REQUEST['method'];
		$result=@call_user_func($serviceMethod,$data,$_SERVER['REMOTE_ADDRESS'],$_COOKIE['token']);
		if($result){
			//before I send back the results, might need to clear cache
			header("Content-type:text/plain");
			echo $result;
		}
		
	}
?>