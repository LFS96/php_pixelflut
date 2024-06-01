<?php

class TCPClass
{
    private Socket $socket;
    public function createSocket($host, $port):bool
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return false;
        }

        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            return false;
        }

        $this->socket = $socket;
        return true;
    }

    public function sendCommand( $command, $size):bool
    {
        $result = socket_write($this->socket, $command, $size);

        return true;
    }

}