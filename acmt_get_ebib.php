<?php
function acmt_get_ebib()
{
    if(!is_user_logged_in()) {
        wp_redirect(site_url('choose-your-registration-type'));
    }

    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $user_id = $current_user->ID;
    $race = $_GET['race'];

    $isRequestEmail = isset($_GET['se']) ? 1 : 0;
    global $wpdb;

    $query = "SELECT * FROM `wp_accessbank_test` WHERE user_id = $user_id";
    $results = $wpdb->get_results($query);


    $results_array = (array) $results[0];


    $mysqli = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}";
    }

    $sql = "SELECT * FROM `wp_accessbank_test` WHERE user_id = $user_id";

    $result = $mysqli->query($sql);

    $users = array();
    while($user = $result->fetch_row()) {
        $users[] = $user[2];
    }
    // print_r($result);
    // $users[] = $results_array['Family_name'];
    // foreach($results_array as $user){
    //     $users = $user;
    // }

    // if(isset($results_array['group_family'])) {
    //     if($results_array['group_family'] == 'group'){
    //             $group = 'group';
    //         } else {
    //             $group = 'family';
    //         }
    // } else {
    //     $group = 'single';
    // }

// echo '<pre>';
// // print_r($group);
// echo "User:".$user_id."<br>";
// // echo $results_array['Family_name']."<br/>";
// print_r($users);

// echo "G:".$group;

// echo '</pre>';
// exit;
    // $query = "SELECT * FROM `wp_accessbank_test` WHERE user_id = $user_id";
    // $results = $wpdb->get_results($query);
    // $results_array = (array) $results[0];

    // if(empty($results_array['group_family'])) {
    //         $group = 1;
    //     } else {
    //         
    //     }



        $data = array(
            'email' => $email,
            'name' => $current_user->first_name." ".$current_user->last_name,
            // 'type' => $group,
            // 'users' => $users,
            'race' => $race,
            'isRequestEmail' => $isRequestEmail
        );

// print_r($data);
// exit;
        $curl = curl_init();
        $host =  $_SERVER['HTTP_HOST'];
        if ($host == "localhost") {
        // Local machine url
            $url = 'https://94.229.74.69/api/barcode/single';
        } else {
        // Live Server url
            $url = 'http://127.0.0.1/api/barcode/single';        
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ]
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            ob_clean();
            header('Content-type: application/json');
            $resp = json_decode($response);

            // print_r($resp);

            // echo site_url('thank-you/?status='.$resp->status);

            // wp_redirect(site_url('/thank-you'));
            wp_redirect(site_url('thank-you/?status='.$resp->status));
            exit;
        }
}
add_shortcode('acmt_ebib', 'acmt_get_ebib');