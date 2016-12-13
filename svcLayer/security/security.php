<?php

function cleanInputFields($inputFields){
    $cleanInput = strip_tags($inputFields);
    $cleanInput = htmlentities($cleanInput);
    return $cleanInput;
}

function filterBy($input,$type,$length){          
    switch ($type){
        case "Text":
            if($length){
                if(strlen($input) > $length){
                    return "error";
                }else{
                    //Strip tags
                    $cleanInput = cleanInputFields($input);
                    if(strlen($input)>$length){
                        return "error";
                    }else{
                        return $cleanInput;
                    }
                }
            }else{
              //Strip tags
                $cleanInput = cleanInputFields($input);
                return $cleanInput;
            }

        case "Email":
            if(!filter_var($input,FILTER_VALIDATE_EMAIL)){
                echo "bad email :(";
            }else{
                echo "Good email :D";
            }
        default:
            break;

    }

}
?>