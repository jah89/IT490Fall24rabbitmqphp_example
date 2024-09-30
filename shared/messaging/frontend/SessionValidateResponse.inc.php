<?php
/**
 * Contains response from DB to frontend to 
 * validate a session.
 */

namespace NBA\Shared\Messaging\Frontend;

/**
 * A request from the frontend to validate
 * a user's session via token.
 */
class SessionValidateResponse extends SessionResponse
{
    /**
     * The session object.
     * 
     * @var ?\NBA\Shared\Session $session The session object.
     */
    private ?\NBA\Shared\Session $session;


    
    /**
     * Create response to validate session.
     *
     * @param boolean $result  True for successful validation or invalidation,
     *  false if not valid or error.
     * @param \NBA\Shared\Session $session session object.
     */
    public function __construct(bool $result, ?\NBA\Shared\Session $session=null)
    {
        //Parent constructor from SessionResponse
        parent::__construct($result);
        $this->session = $session;

    }


    /**
     * FUnction to get session object.
     *
     * @return ?\JAND\Common\Session Session object.
     */
    public function getSession()
    {
        return $this->session;

    }
}