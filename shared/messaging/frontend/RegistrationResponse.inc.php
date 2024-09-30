<?php

/**
 * Contains message for DB response to registering a new user.
 */

namespace NBA\Shared\Messaging\Frontend;

/**
 * The DB response to a request from frontend to register new user.
 */
class RegisterResponse extends LoginResponse{
    
    private ?\NBA\Shared\Messaging\MessageError $error;

    public function __construct(bool $result,
    ?\NBA\Shared\Session $session,
    ?\NBA\Shared\Messaging\MessageError $error = null)
    {
        //Constructor of parent LoginResponse class
        parent::__construct($result, $session);
        $this->error = $error;
        
    }

    public function getMessageError(){
        return $this->error;
    }
}