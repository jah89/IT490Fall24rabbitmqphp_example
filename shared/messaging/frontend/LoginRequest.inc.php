<?php
/**
 * Contains class for frontend to request login.
 */

 namespace NBA\Shared\Messaging\Frontend;

 /**
  * Request from frontend to login.
  */
 abstract class LoginRequest extends \NBA\Shared\Messaging\Request
 {
    /**
     * Users email.
     * @var string $email The user's email addr.
     */
    private string $email;

    /**
     * Users' password.
     * 
     * @var string $password User's password.
     */
    private string $password;

    /**
     * Creates new login request.
     * 
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password){
        $this->email = $email;
        $this->password = $password;
    
    }

    /**
     * Function to get user's email.
     *
     * @return string Uer's email.
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * Function to get user's password.
     *
     * @return string Uer's password.
     */
    public function getPassword(){
        return $this->password;
    }
 }