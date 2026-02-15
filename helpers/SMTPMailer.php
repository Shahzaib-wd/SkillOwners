<?php
/**
 * SMTP Mailer Class
 * A lightweight SMTP client for sending emails without external dependencies.
 */
class SMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $timeout = 30;
    private $socket;
    private $debug = false;
    private $logs = [];

    public function __construct($host, $port, $username, $password) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function setDebug($debug) {
        $this->debug = $debug;
    }

    public function getLogs() {
        return $this->logs;
    }

    private function log($message) {
        $this->logs[] = $message;
        if ($this->debug) {
            error_log("SMTP: " . trim($message));
        }
    }

    private function sendCommand($command, $expectedCode) {
        $this->log("CLIENT: " . $command);
        fputs($this->socket, $command . "\r\n");
        
        $response = "";
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        
        $this->log("SERVER: " . $response);
        $code = substr($response, 0, 3);
        
        if ($code != $expectedCode) {
            throw new Exception("SMTP Error: Expected $expectedCode, got $code. Response: $response");
        }
        
        return $response;
    }

    public function send($to, $subject, $body, $from, $fromName, $isHtml = true) {
        try {
            $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            
            if (!$this->socket) {
                throw new Exception("Could not connect to SMTP host: $errstr ($errno)");
            }
            
            // Read initial greeting
            $response = "";
            while ($str = fgets($this->socket, 515)) {
                $response .= $str;
                if (substr($str, 3, 1) == " ") {
                    break;
                }
            }
            $this->log("SERVER: " . $response);

            // HELO/EHLO
            $this->sendCommand("EHLO " . gethostname(), 250);

            // STARTTLS
            // If port is 587, we usually need STARTTLS using stream_socket_enable_crypto
            if ($this->port == 587) {
                $this->sendCommand("STARTTLS", 220);
                if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                     throw new Exception("TLS negotiation failed");
                }
                // Resend EHLO after TLS
                $this->sendCommand("EHLO " . gethostname(), 250);
            }

            // AUTH LOGIN
            $this->sendCommand("AUTH LOGIN", 334);
            $this->sendCommand(base64_encode($this->username), 334);
            $this->sendCommand(base64_encode($this->password), 235);

            // MAIL FROM
            $this->sendCommand("MAIL FROM: <$from>", 250);

            // RCPT TO
            $this->sendCommand("RCPT TO: <$to>", 250);

            // DATA
            $this->sendCommand("DATA", 354);

            // Headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: " . ($isHtml ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
            $headers .= "From: $fromName <$from>\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "Date: " . date("r") . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

            // Body
            $content = $headers . "\r\n" . $body . "\r\n.";
            
            $this->sendCommand($content, 250);

            // QUIT
            $this->sendCommand("QUIT", 221);

            fclose($this->socket);
            return true;

        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            if (is_resource($this->socket)) {
                fclose($this->socket);
            }
            // Re-throw so caller knows it failed
            throw $e;
        }
    }
}
