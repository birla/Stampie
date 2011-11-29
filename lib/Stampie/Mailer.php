<?php

namespace Stampie;

use Stampie\Adapter\AdapterInterface;
use Stampie\Adapter\ResponseInterface;

/**
 * Minimal implementation of a MailerInterface
 *
 * @author Henrik Bjornskov <henrik@bjrnskov.dk>
 */
abstract class Mailer implements MailerInterface
{
    /**
     * @var AdapterInterface $adapter
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $serverToken;

    /**
     * @param AdapterInterface $adapter
     * @param string $serverToken
     */
    public function __construct(AdapterInterface $adapter, $serverToken)
    {
        $this->setAdapter($adapter);
        $this->setServerToken($serverToken);
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $serverToken
     * @throws \InvalidArgumentException
     */
    public function setServerToken($serverToken)
    {
        if (empty($serverToken)) {
            throw new \InvalidArgumentException('ServerToken cannot be empty');
        }

        $this->serverToken = $serverToken;
    }

    /**
     * @return string
     */
    public function getServerToken()
    {
        return $this->serverToken;
    }

    /**
     * @param MessageInterface $message
     * @throws \LogicException
     * @return Boolean
     */
    public function send(MessageInterface $message)
    {
        $response = $this->getAdapter()->send(
            $this->getEndpoint(),
            $this->format($message),
            $this->getHeaders()
        );

        // We are all clear if status is HTTP 200 OK
        if ($response->getStatusCode() === 200) {
            return true;
        }

        return $this->handle($response);
    }
    

    /**
     * Format the given message into a body that can be used for sending 
     * to the api
     *
     * @param MessageInterface $message
     * @return string
     */
    abstract protected function format(MessageInterface $message);

    /**
     * Handle an error response where ResponseInterface::getStatusCode() is not 200
     *
     * @param ResponseInterface $response
     * @return boolean
     */
    abstract protected function handle(ResponseInterface $response);

    /**
     * Return an array of headers needed for this mailer. 
     *
     * example:
     *     array("HeaderName" => "HeaderValue");
     *
     * @return array
     */
    abstract protected function getHeaders();
    
}
