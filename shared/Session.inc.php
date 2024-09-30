<?php
/**
* User session object.
*/

namespace NBA\Shared;

/**
* A user's session.
*/
class Session {

    /**
     * User's username.
     *
     * @var string $username is User's username.
     */
    private string $username;

    /**
     * User's session token.
     *
     * @var string $sessionToken is the user's session token.
     */
    private string $sessionToken;

    /**
     * Session expiration time using Unix epoch timestamp.
     *
     * @var string $$sessionExpiration is the timestamp of session expiration.
     */
    private string $sessionExpiration;

    /**
     * User's ID.
     *
     * @var string $UID is the ID of user.
     */
    private string $UID;

    /**
    * Constructor to create a new session.
    *
    * @param string $sessionToken The session token.
    * @param integer $sessionExpiration Timestamp of expiration.
    * @param string $UID The user's ID.
    * @param string $username The user's username.
    */
    public function __construct(
        string $sessionToken,
        int $sessionExpiration,
        string $UID,
        string $username
    ) {
        $this->username          = $username;
        $this->UID               = $UID;
        $this->sessionToken      = $sessionToken;
        $this->sessionExpiration = $sessionExpiration;
    } //end of contstructor

    //start of getters
    /**
     * Gets user's username.
     *
     * @return string Username.
     */
    public function getUsername()
    {
        return $this->username;

    }

    /**
     * Gets session token.
     *
     * @return string Session token.
     */
    public function getSessionToken()
    {
        return $this->sessionToken;

    }

    /**
     * Gets session expiration timestamp.
     *
     * @return integer Session expiration timestamp.
     */
    public function getSessionExpiration()
    {
        return $this->sessionExpiration;

    }


    /**
     * Gets the user ID.
     *
     * @return string UID.
     */
    public function getUserId()
    {
        return $this->UID;

    }//end getUserId()
//end getters

}//end Session class

