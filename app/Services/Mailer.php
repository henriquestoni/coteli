<?php

namespace App\Services;

class Mailer
{
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private bool $useSsl;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $pass,
        bool $useSsl = true
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->useSsl = $useSsl;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $transport = $this->useSsl ? "ssl://{$this->host}:{$this->port}" : "{$this->host}:{$this->port}";
        $fp = @stream_socket_client($transport, $errno, $errstr, 15);
        if (!$fp) {
            $this->log("Falha ao conectar: {$errstr} ({$errno})");
            return false;
        }
        stream_set_timeout($fp, 10);

        $read = function () use ($fp) {
            return fgets($fp, 512);
        };
        $send = function (string $cmd) use ($fp, $read) {
            fwrite($fp, $cmd . "\r\n");
            return $read();
        };

        $this->log(trim($read()));
        $send("EHLO coteli");
        $send("AUTH LOGIN");
        $send(base64_encode($this->user));
        $send(base64_encode($this->pass));
        $send("MAIL FROM:<{$this->user}>");
        $send("RCPT TO:<{$to}>");
        $send("DATA");

        $headers = [];
        $headers[] = "From: {$this->user}";
        $headers[] = "To: {$to}";
        $headers[] = "Subject: {$subject}";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        $send($data);
        $send("QUIT");
        fclose($fp);
        return true;
    }

    private function log(string $msg): void
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        @file_put_contents($dir . '/mail_debug.log', '[' . date('Y-m-d H:i:s') . "] {$msg}\n", FILE_APPEND);
    }
}
