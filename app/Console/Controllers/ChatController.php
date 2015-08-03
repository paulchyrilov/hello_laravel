<?php
namespace App\Console\Controllers;

use App\Message;
use App\User;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ChatController implements MessageComponentInterface
{

    protected $clients;

    protected $userClients;

    protected $resourceUsers;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $socket will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $this->detachUserClient($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $this->detachUserClient($conn);

        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $client The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $client, $msg)
    {
        $msg = json_decode($msg, true);

        if(isset($msg['command'])) {
            switch ($msg['command']) {
                case 'setUser':
                    $this->setUserClient(isset($msg['token']) ? $msg['token'] : null, $client);
                default:
            }
        } else {
            if(isset($msg['from']) && isset($msg['to']) && isset($msg['text'])) {
                if(!isset($this->resourceUsers[$client->resourceId])) {
                    $client->close();
                    return;
                }
                $from = $this->resourceUsers[$client->resourceId]->id;
                $message = new Message();
                $message->setAttribute('sender_id', $from);
                $message->setAttribute('recipient_id', $msg['to']);
                $message->setAttribute('text', $msg['text']);
                $message->save();

                $recipient = isset($this->userClients[$msg['to']]) ? $this->userClients[$msg['to']] : null;
                if($recipient instanceof ConnectionInterface) {
                    $recipient->send($message->toJson());
                }

                $client->send($message->toJson());

                echo sprintf('Connection %d sending message "%s" to %d' . "\n"
                    , $from, $msg['text'], $msg['to']);
            }
        }

    }

    protected function setUserClient($token, ConnectionInterface $client)
    {
        $user = User::where('wstoken', $token)->first();
        if(!$user instanceof User) {
            $client->close();
            return;
        }
        echo 'Resource ' . $client->resourceId . ' associated to user ' . $user->id . "\n";
        $this->userClients[$user->id] = $client;
        $this->resourceUsers[$client->resourceId] = $user;
    }

    protected function detachUserClient($conn)
    {
        unset($this->resourceUsers[$conn->resourceId]);
        $userId = array_search($conn, $this->userClients);
        if($userId) {
            echo 'User ' . $userId . ' detached from resource ' . $conn->resourceId . "\n";
            unset($this->userClients[$userId]);
        }
    }




}