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

?>