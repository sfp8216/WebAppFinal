<?php
function cleanInputFields($inputFields) {
	$cleanInput = strip_tags($inputFields);
	$cleanInput = htmlentities($cleanInput);
	return $cleanInput;
}
function filterBy($input, $type, $length) {
	switch($type) {
		case "Text" :
			if($length) {
				if(strlen($input) > $length) {
					return "error";
				}
				else{
					//Strip tags
					$cleanInput = cleanInputFields($input);
					if(strlen($input) > $length) {
						return "error";
					}
					else{
						return $cleanInput;
					}
				}
			}
			else{
				//Strip tags
				$cleanInput = cleanInputFields($input);
				return $cleanInput;
			}
		case "Number" :
			if($length) {
				if(strlen($input) > $length) {
					return "error";
				}
				else{
					//Validate that it is int
					if(filter_var($input, FILTER_VALIDATE_INT)) {
						return $input;
					}
					else{
						return "error";
					}
				}
			}
			else{
				//Validate that it is int
				if(filter_var($input, FILTER_VALIDATE_INT)) {
					return $input;
				}
				else{
					return "error";
				}
			}
		case "Bool":
			if($input == 1 || $input == 0 || $input == true || $input == false
                || $input == "1" || $input == "0") {
				return $input."";
			}
			else{
				echo "error";
			}
		default :
			break;
	}
}
?>