<?php
namespace MyOne4All\Tests;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Process\Process;

/**
 * The Server class is used to control a scripted webserver using node.js that
 * will respond to HTTP requests with queued responses.
 *
 * Queued responses will be served to requests using a FIFO order.  All requests
 * received by the server are stored on the node.js server and can be retrieved
 * by calling {@see Server::received()}.
 *
 * Mock responses that don't require data to be transmitted over HTTP a great
 * for testing.  Mock response, however, cannot test the actual sending of an
 * HTTP request using cURL.  This test server allows the simulation of any
 * number of HTTP request response transactions to test the actual sending of
 * requests over the wire without having to leave an internal network.
 */
class Server
{
    /** @var Client */
    private static $client;
    private static $started = false;
    public static $url = 'http://127.0.0.1:9005/'; //mockserver -p 9005 -m mocks
    public static $port = 9005;

    public static function wait($maxTries = 5)
    {
        $tries = 0;
        while (!self::isListening() && ++$tries < $maxTries) {
            usleep(100000);
        }
        if (!self::isListening()) {
            throw new \RuntimeException('Unable to contact node.js mockserver server on port '.self::$port);
        }
    }
    public static function start()
    {
        if (self::$started) {
            return;
        }
        if (!self::isListening()) {

            $command = 'mockserver -p 9005 -m ' . __DIR__ . '/mocks';
            $process = new Process($command);
            $process->start();

            self::wait();
        }
        self::$started = true;
    }
    private static function isListening()
    {
        try {
            self::getClient()->request('GET', 'server', [
                'connect_timeout' => 5,
                'timeout'         => 5
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    private static function getClient()
    {
        if (!self::$client) {
            self::$client = new Client([
                'base_uri' => self::$url,
                'sync'     => true,
            ]);
        }
        return self::$client;
    }
}