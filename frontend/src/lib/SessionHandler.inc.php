<?php
/**
* Manages users' sessions.
*/
namespace NBA\Frontend\Lib;

use rabbitMQClient;

abstract class SessionHandler {

    /**
     * The session object that has been or will be fetched
     * @var $session User session object.
     */
    private static ?\NBA\Shared\Session $session;

    /**
     * Function to get the session object.
     * Checks the cookie for session data.  
     * If missing, goes to fetch it.
     * 
     * @return session object or false
     */
    public static function getSession() {
        if(!isset(static::$session)) {
            $cookieName = 'SESSION';
            $cookieValue = ($_COOKIE[$cookieName] ?? null);

            if ($cookieValue === null) {
                return false;
            }

            $request  = new \NBA\Shared\Messaging\Frontend\SessionValidateRequest($cookieValue);
            $client     = new rabbitMQClient("host.ini", "testServer");
            $response = $request->sendRequest($client);
            if ($response instanceof \NBA\Shared\Messaging\Frontend\SessionValidateResponse) {
                if ($response->getResult()) {
                    $session = $response->getSession();
                    if ($session !== null) {
                        static::$session = $session;
                        return $session;
                    }
                }
            }

            return false;
        } else {
            return static::$session;
        }
    }

    /**
     * Logs in through rabbit messaging. Will get session
     * and set user session cookie.
     *
     * @param string $email        User's email.
     * @param string $passwordHash User's password hash.
     *
     * @return false|\NBA\Shared\Session The session object or false.
     */
    public static function login(string $email, string $passwordHash)
    {
        $cookieName = 'SESSION';
        $request    = new \NBA\Shared\Messaging\Frontend\LoginRequest($email, $passwordHash);
        $client     = new rabbitMQClient("host.ini", "testServer");
        $response   = $request->sendRequest($client);
        if ($response instanceof \NBA\Shared\Messaging\Frontend\LoginResponse) {
            if ($response->getResult()) {
                $session = $response->getSession();
                if ($session !== null) {
                    setcookie(
                        $cookieName,
                        $session->getSessionToken(),
                        $session->getSessionExpiration(),
                        '/',
                        $_SERVER['SERVER_NAME']
                    );
                    static::$session = $session;
                    return $session;
                }
            }
        }

        return false;

    }


    /**
     * Registers through rabbit messaging. Will get session
     * and set user session cookie.
     *
     * @param string $email        User's email.
     * @param string $passwordHash User's password hash.
     * @param string $firstName    User's first name.
     * @param string $lastName     User's last name.
     *
     * @return false|\JAND\Common\Session|
     * \JAND\Common\Messages\Shared\OperationError Session object, error, or false.
     */
    public static function register(string $email, string $passwordHash)
    {
        $cookieName = 'SESSION';

        $request  = new \NBA\Shared\Messaging\Frontend\RegisterRequest($email, $passwordHash);
        $client     = new rabbitMQClient("host.ini", "testServer");
        $response = $request->sendRequest($client());
        if ($response instanceof \NBA\Shared\Messaging\Frontend\RegisterResponse) {
            if ($response->getResult()) {
                $session = $response->getSession();
                if ($session !== null) {
                    setcookie(
                        $cookieName,
                        $session->getSessionToken(),
                        $session->getSessionExpiration(),
                        '/',
                        $_SERVER['SERVER_NAME']
                    );
                    static::$session = $session;
                    return $session;
                }
            } else {
                $error = $response->getMessageError();
                if ($error instanceof \NBA\Shared\Messaging\MessageError) {
                    return $error;
                }
            }
        }

        return false;

    }


    /**
     * Logs out user via session.
     *
     * @return boolean True on success, false on error.
     */
    public static function logout()
    {
        $session = static::getSession();

        if (!$session) {
            return true;
        }

        $cookieName = 'SESSION';

        $request  = new \NBA\Shared\Messaging\Frontend\SessionTerminateRequest($session->getSessionToken());
        $client     = new rabbitMQClient("host.ini", "testServer");
        $response = $request->sendRequest($client);
        if ($response instanceof \NBA\Shared\Messaging\Frontend\SessionTerminateResponse) {
            if ($response->getResult()) {
                $result = $response->getResult();
                if ($result) {
                    setcookie(
                        $cookieName,
                        '',
                        -1,
                        '/',
                        $_SERVER['SERVER_NAME']
                    );
                      static::$session = null;
                      return $result;
                }
            }
        }

        //false means a logout error.
        return false;

    }
}
?>