<?php
    
    function validate_field($field){
        // Trim whitespace
        $trimmedField = trim($field);
        
        // Check if the field is empty after trimming
        if(strlen($trimmedField) < 1){
            echo "<script>alert('Field cannot be empty');</script>";
            return false;
        } else {
            // Check if the field contains any numerical characters
            if(preg_match('/[0-9]/', $trimmedField)){
                echo "<script>alert('Name must not contain numerical characters!');</script>";
                // If numerical characters are found, return false with a warning message
                return false;
            } else {
                // Otherwise, return true indicating successful validation
                return true;
            }
        }
    }
    
    // }
    
    // function validate_field($field){
    //     $field = htmlentities($field);
    //     if(strlen(trim($field))<1){
    //         return false;
    //     }else{
    //         return true;
    //     }
    // }
    
    function validate_field2($field){
        // Trim whitespace
        $trimmedField = trim($field);
        
        // Check if the field contains any numerical characters
        if(preg_match('/[0-9]/', $trimmedField)){
            echo "<script>alert('Name must not contain numerical characters!');</script>";
            // If numerical characters are found, return false with a warning message
            return false;
        } else {
            // Otherwise, return true indicating successful validation
            return true;
        }
    }
    
    function validate_field3($field){
        // Trim whitespace
        $trimmedField = trim($field);
        
        // Check if the field is empty after trimming
        if(strlen($trimmedField) < 1){
            echo "<script>alert('Field cannot be empty');</script>";
            return false;
        } else {
            // Otherwise, return true indicating successful validation
            return true;
        }
    }

    function validate_field5($field){
        // Trim whitespace
        $trimmedField = trim($field);
        
        // Check if the field is empty after trimming
        if(strlen($trimmedField) < 1){
            echo "<script>alert('Field cannot be empty');</script>";
            return false;
        } elseif (!preg_match('/^[0-9]+$/', $trimmedField)) {
            // Check if the field contains only numbers
            echo "<script>alert('Field must contain only numbers');</script>";
            return false;
        } else {
            // Otherwise, return true indicating successful validation
            return true;
        }
    }
    

    function validate_field4($field){
        // Trim whitespace
        $trimmedField = trim($field);
        
        // Check if the field is empty after trimming
        if(strlen($trimmedField) < 1){
            // Return false indicating validation failure
            return false;
        } else {
            // Otherwise, return true indicating successful validation
            return true;
        }
    }
    
    
    
    

    function validate_email($email){
        // Check if the 'email' key exists in the $_POST array
        if (isset($email)) {
            $email = trim($email); // Trim whitespace
            // Check if the email is not empty
            if (empty($email)) {
                return 'Email is required';
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Check if the email has a valid format
                return 'Email is invalid format';
            } else {
                return 'success';
            }
        } else {
            return 'Email is required'; // 'email' key doesn't exist in $_POST
        }
    }    

    function validate_username($userUserName){
        // Check if the 'username' key exists in the $_POST array
        if (isset($userUserName)) {
            $userUserName = trim($userUserName); // Trim whitespace
            // Check if the username is not empty
            if (empty($userUserName)) {
                return 'Username is required';
            } else {
                // Check if the username already exists in the database
                $user = new User();
                if ($user->isUsernameExist($userUserName)) {
                    return 'Username already exists';
                } else {
                    return 'success';
                }
            }
        } else {
            return 'Username is required'; // 'username' key doesn't exist in $_POST
        }
    }
    

    function validate_password($userPassword) {
        $userPassword = htmlentities($userPassword);
        
        if (strlen(trim($userPassword)) < 1) {
            return "Password cannot be empty";
        } elseif (strlen($userPassword) < 8) {
            return "Password must be at least 8 characters long";
        } else {
            return "success"; // Indicates successful validation
        }
    }
    
       



    function validate_cpw($password, $cpassword){
        $pw = htmlentities($password);
        $cpw = htmlentities($cpassword);
        if(strcmp($pw, $cpw) == 0){
            return true;
        }else{
            return false;
        }
    }

?>