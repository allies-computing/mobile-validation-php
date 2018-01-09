<?php

    /*

    Mobile number validation with PHP
    Simple demo which passes mobile phone number to the API and shows a message based on response.

    For non UK numbers you will need to include the country code at the start, in either +44 or 0044 format

    Full mobile validation API documentation:-
    https://developers.alliescomputing.com/postcoder-web-api/mobile-validation
    
    */

    if (array_key_exists("mobile", $_GET)) {

        var_dump(validate_mobile_number($_GET['mobile']));
        
    } else {
        
        echo "<p>Pass a mobile number using <code>?mobile=+441234567789</code></p>";
        
    }

    function validate_mobile_number($mobile = "") {
        
        // Replace with your API key, test key will always return true regardless of mobile number
        $api_key = "PCW45-12345-12345-1234X";
        
        // Grab the input text and trim any whitespace
        $mobile = trim($mobile);
        
        // Create an empty output object
        $output = new StdClass();
        
        if ($mobile == "") {
            
            // Respond without calling API if no mobile number supplied
            $output->valid = false;
            $output->message = "No mobile number supplied";
            
        } else {
            
            // Create the URL including API key and encoded mobile number
            $mobile_url = "https://ws.postcoder.com/pcw/" . $api_key . "/mobile/" . urlencode($mobile); 
            
            // use cURL to send the request and get the output
            $session = curl_init($mobile_url); 
            // Tell cURL to return the request data
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
            // use application/json to specify json return values, the default is XML.
            $headers = array('Content-Type: application/json');
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL on the session handle
            $response = curl_exec($session);
            
            $http_status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);

            // Close the cURL session
            curl_close($session);
            
            if ($http_status_code != 200) {
                
                // Triggered if API does not return 200 HTTP code
                // More info - https://developers.alliescomputing.com/postcoder-web-api/error-handling
                
                // Here we will output a basic message with HTTP code
                $output->message = "An error occurred - " . $http_status_code;
                
            } else {
                
                // Convert JSON into an object
                $result = json_decode($response);

                // Basic is valid check
                if($result->valid === true) {

                    // Do something if valid, here we will output the response

                    $output->valid = $result->valid;
                    $output->message = $result->state;
                    
                    // Additional info such as Network is also returned, full details - https://developers.alliescomputing.com/postcoder-web-api/mobile-validation

                } else {

                    // Do something if invalid, here we will output the response

                    $output->valid = $result->valid;
                    $output->message = $result->state;

                }

                // Full list of "state" responses - https://developers.alliescomputing.com/postcoder-web-api/mobile-validation
                
            }
            
        }
            
        return $output;
        
    }

?>
