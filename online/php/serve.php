<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

// Configuração do servidor WebSocket
$server = new \Socket\Raw\Socket(\Socket\Raw\Socket::AF_INET, \Socket\Raw\Socket::SOCK_STREAM, 0);
$server->bind('0.0.0.0', 8080);
$server->listen(10);

$clients = [];
$rooms = [];

while (true) {
    $read = array_merge([$server], $clients);
    $write = $except = null;
    
    if (socket_select($read, $write, $except, null) === false) {
        die('Erro no socket_select');
    }
    
    // Nova conexão
    if (in_array($server, $read)) {
        $client = $server->accept();
        $clients[] = $client;
        
        // Ler dados iniciais (username e room)
        $data = socket_read($client, 1024);
        parse_str($data, $params);
        
        $username = $params['username'] ?? 'Anônimo';
        $room = $params['room'] ?? 'default';
        
        // Registrar cliente
        socket_set_nonblock($client);
        socket_getpeername($client, $ip);
        
        $clientData = [
            'socket' => $client,
            'username' => $username,
            'room' => $room,
            'ip' => $ip,
            'score' => 0
        ];
        
        $rooms[$room][] = $clientData;
        
        // Notificar outros jogadores
        foreach ($rooms[$room] as $player) {
            if ($player['socket'] !== $client) {
                socket_write($player['socket'], json_encode([
                    'type' => 'player_joined',
                    'player' => $username
                ]));
            }
        }
        
        // Remover servidor da lista de leitura
        unset($read[array_search($server, $read)]);
    }
    
    // Processar mensagens dos clientes
    foreach ($read as $client) {
        $data = socket_read($client, 1024);
        
        if ($data === false || strlen($data) === 0) {
            // Cliente desconectado
            $key = array_search($client, $clients);
            if ($key !== false) {
                unset($clients[$key]);
            }
            
            // Remover de todas as salas
            foreach ($rooms as $roomName => $roomClients) {
                foreach ($roomClients as $i => $roomClient) {
                    if ($roomClient['socket'] === $client) {
                        $username = $roomClient['username'];
                        unset($rooms[$roomName][$i]);
                        
                        // Notificar outros jogadores
                        foreach ($rooms[$roomName] as $player) {
                            socket_write($player['socket'], json_encode([
                                'type' => 'player_left',
                                'player' => $username
                            ]));
                        }
                    }
                }
            }
            
            continue;
        }
        
        $message = json_decode($data, true);
        
        // Processar tipo de mensagem
        switch ($message['type']) {
            case 'flip_card':
                broadcastToRoom($client, $message['room'], [
                    'type' => 'card_flipped',
                    'cardIndex' => $message['cardIndex'],
                    'playerId' => $message['playerId']
                ]);
                break;
                
            case 'match_cards':
                broadcastToRoom($client, $message['room'], [
                    'type' => 'card_matched',
                    'cardIndices' => $message['cardIndices'],
                    'playerId' => $message['playerId']
                ]);
                break;
                
            case 'start_game':
                broadcastToRoom($client, $message['room'], [
                    'type' => 'game_started'
                ]);
                break;
                
            case 'game_over':
                broadcastToRoom($client, $message['room'], [
                    'type' => 'game_over',
                    'winner' => $message['winner']
                ]);
                break;
        }
    }
}

function broadcastToRoom($sender, $room, $message) {
    global $rooms;
    
    if (!isset($rooms[$room])) return;
    
    foreach ($rooms[$room] as $client) {
        if ($client['socket'] !== $sender) {
            socket_write($client['socket'], json_encode($message));
        }
    }
}