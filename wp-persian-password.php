

<?php

/**
 * @package   Persian Password
 * @author    Mohammad Zarei <mohammad.zarei1380@gmail.com>
 * @license   GPL-3.0+
 * @link      https://zarei.dev
 *
 * Plugin Name:     Persian Password
 * Description:     Accept Password with persian numbers in your WordPress Login form.
 * Version:         1.0.4
 * Author:          Mohammad Zarei
 * Author URI:      https://zarei.dev
 * License:         GPL-3.0+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Tested up to:    6.0
 * Requires PHP:    5.6
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

add_filter( 'authenticate', 'wp_authenticate_persian_password', 30, 3 );

function wp_authenticate_persian_password( $user, $email_or_username, $password ) {
    if ( $user instanceof WP_User ) {
        return $user;
    }

    if ( $user instanceof WP_Error && $user->get_error_code() === 'incorrect_password' ) {
        // If the error code is incorrect_password, we know the password is wrong.

        if ( is_email( $email_or_username ) ) {
            $user = get_user_by( 'email', $email_or_username );
            if ( ! $user ) {
                return new WP_Error(
                    'invalid_email',
                    __( 'Unknown email address. Check again or try your username.' )
                );
            }
            $is_email = true;
        } else {
            $user = get_user_by( 'login', $email_or_username );
            if ( ! $user ) {
                return new WP_Error(
                    'invalid_username',
                    sprintf(
                        /* translators: %s: User name. */
                        __( '<strong>Error</strong>: The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.' ),
                        $email_or_username
                    )
                );
            }
            $is_email = false;
        }

        $persian = ['۰', '۱', '۲', '۳', '۴', '٤', '۵', '٥', '٦', '۶', '۷', '۸', '۹'];
        $english = [0, 1, 2, 3, 4, 4, 5, 5, 6, 6, 7, 8, 9];

        $english_password = str_replace($persian, $english, $password);
        $persian_password = str_replace($english, $persian, $password);

        // We check if the password is correct with persian numbers or not.
        if ( wp_check_password( $english_password, $user->user_pass, $user->ID ) || wp_check_password( $persian_password, $user->user_pass, $user->ID ) ) {
            return $user;
        }


        if ( $is_email ) {
            return new WP_Error(
                'incorrect_password',
                sprintf(
                    /* translators: %s: Email address. */
                    __( '<strong>Error</strong>: The password you entered for the email address %s is incorrect.' ),
                    '<strong>' . $email_or_username . '</strong>'
                ) .
                ' <a href="' . wp_lostpassword_url() . '">' .
                __( 'Lost your password?' ) .
                '</a>'
            );
        } else {
            return new WP_Error(
                'incorrect_password',
                sprintf(
                    /* translators: %s: User name. */
                    __( '<strong>Error</strong>: The password you entered for the username %s is incorrect.' ),
                    '<strong>' . $email_or_username . '</strong>'
                ) .
                ' <a href="' . wp_lostpassword_url() . '">' .
                __( 'Lost your password?' ) .
                '</a>'
            );
        }

    }
        
}
