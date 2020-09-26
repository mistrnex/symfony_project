<?php


namespace App\Service;


use Psr\Log\LoggerInterface;

class Greeting
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $message;

    public function __construct(LoggerInterface $logger, string $message)
    {
        $this->logger = $logger;
        $this->message = $message;
    }

    /**
     * Just first testing method
     *
     * @param string $name
     * @return string
     */
    public function greet(string $name): string
    {
        $this->logger->info("Greeted $name");
        return "{$this->message} $name";
//        return "Hello lovely $name";

    }
}