<?php
/**
 * Class containing base request.
 */

 namespace NBA\Shared\Messaging;

 /**
  * class that is base for requests.
  */
abstract class Request {


    /**
     * Sends request.
     * @param $client RabbitMQ Client based on rabbitLib file
     * @return Response from broker.
     */
    public function sendRequest($client){
        return unserialize($client->sendRequest(serialize($this), 'application/php-serialized'));
    }
}