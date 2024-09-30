<?php
/**
 * Contains response from DB processor to frontend about sessions.
 */

namespace NBA\Shared\Messaging\Frontend;

/**
 * Contains response from DB processor to frontend about sessions.
 */
abstract class SessionResponse
{

    /**
     * True for successful validation, false if not valid or there is an error.
     *
     * @var boolean $result
     */
    private bool $result;


    /**
     * Create new session response.
     *
     * @param boolean $result True for validation, false if not valid or there is an error.
     */
    public function __construct(bool $result)
    {
        $this->result = $result;

    }


    /**
     * Gets result of request.
     *
     * @return boolean True for successful validation or invalidation, false if not valid or there is an error.
     */
    public function getResult()
    {
        return $this->result;

    }


}