<?php

function accessbank_registratio_10()
{
    if(is_user_logged_in()) {
        wp_redirect(site_url('profile'));
    }
    include('templates/registrationForm1.php');
}
add_shortcode('access_registration_10', 'accessbank_registratio_10');



add_action('init', 'acmt_submit_form1');

function acmt_submit_form1()
{
    if (isset($_POST['acmt_register'])) {
        global $register_user;
        $password = $_POST['password'];
        $email = $_POST['email'];
        if(email_exists($email)) {
            $email_error = "Email already exist, login if it is your email";
            wp_redirect(site_url('choose-your-registration-type/?email_error='.$email_error)); // "?s=".$p->post_title
            exit;
        }
        // if($password < 5) {
        //     $pass_error = "Empty or password too short. Password should not be less than 5";
        //     wp_redirect(site_url('race/?pass_error='.$pass_error)); // "?s=".$p->post_title
        //     exit;
        // }
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        $userdata = array(
            'user_login' => esc_attr($email),
            'user_email' => esc_attr($email),
            'user_pass' => esc_attr($password),
            'first_name' => esc_attr($first_name),
            'nickname' => esc_attr($nickname),
            'last_name' => esc_attr($last_name),
            'display_name' => esc_attr($first_name . ' ' . $last_name),
            // 'bio' => $bio,
        );
        
        $register_user = wp_insert_user($userdata);

        // print_r($register_user);

        $user = get_user_by('id', $register_user);

        // print_r($user);

        // add_user_meta($register_user, "acmt_bio", serializeMetaArray($bio));

        // Insert user meta data

        $userinfodata = array(
            'height' => esc_attr($_POST['height']),
            'weight' => esc_attr($_POST['weight']),
            'phone' => esc_attr($_POST['phone']),
            'gender' => esc_attr($_POST['gender']),
            'country' => esc_attr($_POST['country']),
            'nationality' => esc_attr($_POST['nationality']),
            'contact' => esc_attr($_POST['contact']),
            'dob' => esc_attr($_POST['dob']),
            'address' => esc_attr($_POST['address']),
            'disability' => esc_attr($_POST['disability']),
            'race' => esc_attr($_POST['race'])
        );

        $user_info = add_user_meta($user->ID, "acmt_bio", $userinfodata);


        

        if ($user_info) {
            // print_r($user->user_login);
            // die();
            wp_set_current_user($register_user, $user->user_login);
            wp_set_auth_cookie($register_user);

            do_action('wp_login', $user->user_login, $user);
            wp_redirect(site_url('payment'));
            exit;
        } else {
            echo "Error on submission";
        }

        // if ($register_user) {
        //     wp_set_current_user($register_user, $user->user_login);
        //     wp_set_auth_cookie($register_user);
        //     do_action('wp_login', $user->user_login, $user);

        //     wp_redirect(site_url('race_2'));
            
        //     exit;
        // } 
    }
}
?>