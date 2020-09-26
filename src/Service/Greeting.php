<?php


namespace App\Service;


use Psr\Log\LoggerInterface;

class Greeting
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Just first testing method
     *
     * @param string $name
     * @return string
     */
    public function greet(string $name): string
    {
        $this->logger->info("Greated $name");
        return "Helloggg $name";
    }
}