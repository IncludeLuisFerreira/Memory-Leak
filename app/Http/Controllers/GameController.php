<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repositories\MysqlGameMatchRepository;

class GameController extends Controller
{
    private $matchRepository;

    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            $this->redirect('/login');
        }
        $db = Connection::getInstance();
        $this->matchRepository = new MysqlGameMatchRepository($db);
    }

    public function menu()
    {
        $this->render('menu', ['nome' => $_SESSION['nome_usuario']]);
    }

    public function solo()
    {
        $this->render('salaoJogo');
    }

    public function online()
    {
        $db = Connection::getInstance();
        $roomRepo = new \App\Infrastructure\Repositories\MysqlRoomRepository($db);
        $useCase = new \App\Application\UseCases\JoinRoom($roomRepo);

        $room = $useCase->execute($_SESSION['id_usuario']);

        $this->render('online', [
            'room' => $room,
            'userId' => $_SESSION['id_usuario']
        ]);
    }

    public function status()
    {
        $salaId = $_GET['sala_id'] ?? null;
        if (!$salaId) return $this->json(['status' => 'erro', 'mensagem' => 'ID missing'], 400);

        $db = Connection::getInstance();
        $roomRepo = new \App\Infrastructure\Repositories\MysqlRoomRepository($db);
        $room = $roomRepo->findById($salaId);

        if (!$room) return $this->json(['status' => 'erro', 'mensagem' => 'Room not found'], 404);

        return $this->json([
            'status' => 'ok',
            'tabuleiro' => $room->getBoardState(),
            'turno' => $room->getTurnUserId(),
            'status_partida' => $room->getStatus()
        ]);
    }

    public function play()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $salaId = $input['sala_id'] ?? null;
        $indices = $input['indices'] ?? null;
        $userId = $_SESSION['id_usuario'];

        if (!$salaId || !$indices) return $this->json(['status' => 'erro'], 400);

        $db = Connection::getInstance();
        $roomRepo = new \App\Infrastructure\Repositories\MysqlRoomRepository($db);
        $room = $roomRepo->findById($salaId);

        if (!$room || $room->getTurnUserId() !== $userId) {
            return $this->json(['status' => 'erro', 'mensagem' => 'Not your turn or room not found'], 403);
        }

        $state = $room->getBoardState();
        $cartas = &$state['cartas'];

        // Logic to check match
        $idx1 = $indices[0];
        $idx2 = $indices[1];

        if ($cartas[$idx1]['id'] === $cartas[$idx2]['id']) {
            $cartas[$idx1]['par'] = true;
            $cartas[$idx2]['par'] = true;
            $cartas[$idx1]['virada'] = true;
            $cartas[$idx2]['virada'] = true;

            if ($userId === $room->getPlayer1Id()) {
                $state['pares_jogador1']++;
            } else {
                $state['pares_jogador2']++;
            }
        } else {
            // Not a match, turn passes
            $nextTurn = ($userId === $room->getPlayer1Id()) ? $room->getPlayer2Id() : $room->getPlayer1Id();
            $room->setTurnUserId($nextTurn);

            // Mark as temporarily flipped for frontend
            $cartas[$idx1]['temporariamente_virada'] = true;
            $cartas[$idx2]['temporariamente_virada'] = true;
        }

        // Check if finished
        $finished = true;
        foreach ($cartas as $c) {
            if (!$c['par']) {
                $finished = false;
                break;
            }
        }

        if ($finished) {
            $room->setStatus(\App\Domain\Entities\Room::STATUS_FINISHED);
            // Save match for both? For now just mark room as finished
        }

        $room->setBoardState($state);
        $roomRepo->update($room);

        return $this->json([
            'status' => 'ok',
            'tabuleiro' => $state,
            'turno' => $room->getTurnUserId(),
            'status_partida' => $room->getStatus()
        ]);
    }

    public function compare()
    {
        $img1 = basename($_POST['img1'] ?? '');
        $img2 = basename($_POST['img2'] ?? '');
        return $this->json(['match' => ($img1 === $img2 && $img1 !== '')]);
    }

    public function saveMatch()
    {
        $userId = $_SESSION['id_usuario'];
        $match = new \App\Domain\Entities\GameMatch(
            null,
            $userId,
            (int)($_POST['tempo'] ?? 0),
            $_POST['modo'] ?? '1',
            (int)($_POST['vencedor'] ?? 1),
            (int)($_POST['pontos'] ?? 0)
        );
        $this->matchRepository->save($match);
        return $this->json(['success' => true]);
    }

    public function historico()
    {
        $this->render('historico');
    }

    public function historicoData()
    {
        $matches = $this->matchRepository->findByUserId($_SESSION['id_usuario']);
        $data = array_map(fn($m) => [
            'id' => $m->getId(),
            'tempo' => $m->getTime(),
            'modo' => $m->getMode(),
            'vencedor' => $m->getWinner(),
            'pontos' => $m->getPoints(),
            'data' => $m->getDate()->format('Y-m-d H:i:s')
        ], $matches);
        return $this->json($data);
    }

    public function ranking()
    {
        $criteria = $_GET['criterio'] ?? 'pontos';
        $ranking = $this->matchRepository->getRanking($criteria);
        return $this->json($ranking);
    }
}
