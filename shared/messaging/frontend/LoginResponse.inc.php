<?php
/**
 * Contains response from DB processor for a login request.
 *
 * @package JAND\Common\Messages\Frontend\AccountResponse
 */

namespace NBA\Shared\Messaging\Frontend;

/**
 * Response from DB processor to login request.
 */
abstract class LoginResponse
{

    /**
     * True for success, false for fail.
     *
     * @var boolean $result
     */
    private bool $result;

    /**
     * Session object.
     *
     * @var NBA\Shared\Session $session User session object.
     */
    private ?\NBA\Shared\Session $session;


    /**
     * Creates a new login response.
     *
     * @param boolean              $result  True if succesfully logged in, otherwise false.
     * @param \JAND\Common\Session $session The session object.
     */
    public function __construct(bool $result, ?\NBA\Shared\Session $session=null)
    {
        $this->result  = $result;
        $this->session = $session;

    }


    /**
     * Gets login result.
     *
     * @return boolean True if success.
     */
    public function getResult()
    {
        return $this->result;

    }


    /**
     * Gets session.
     *
     * @return ?\NBA\Shared\Session Session object.
     * 
     */
    public function getSession()
    {
        return $this->session;

    }


}//end class