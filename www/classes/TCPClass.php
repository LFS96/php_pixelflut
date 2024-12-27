<?php

class TCPClass
{
    private Socket $socket;

    public function createSocket(string $host, int $port): bool
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->socket || !socket_connect($this->socket, $host, $port)) {
            return false;
        }
        return true;
    }

    public function sendCommand(string $command, int $size): bool
    {
        return socket_write($this->socket, $command, $size) !== false;
    }
}
