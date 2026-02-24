<?php

namespace App\Http\Controllers;

use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repositories\MysqlUserRepository;
use App\Application\UseCases\LoginUser;
use App\Domain\Entities\User;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct()
    {
        $db = Connection::getInstance();
        $this->userRepository = new MysqlUserRepository($db);
    }

    public function showLogin()
    {
        $this->render('login');
    }

    public function login()
    {
        $email = $_POST['email_usuario'] ?? '';
        $password = $_POST['senha_usuario'] ?? '';

        $useCase = new LoginUser($this->userRepository);
        $user = $useCase->execute($email, $password);

        if ($user) {
            $_SESSION['id_usuario'] = $user->getId();
            $_SESSION['nome_usuario'] = $user->getName();
            $this->redirect('/menu');
        } else {
            $this->redirect('/login?erro=login');
        }
    }

    public function showRegister()
    {
        $this->render('cadastro');
    }

    public function register()
    {
        $nome = $_POST['nome_usuario'] ?? '';
        $email = $_POST['email_usuario'] ?? '';
        $senha = $_POST['senha_usuario'] ?? '';
        $confirmar = $_POST['senha_usuario_confirmar'] ?? '';

        if ($senha !== $confirmar) {
            $this->redirect("/cadastro?erro=senhas");
        }

        if ($this->userRepository->findByEmail($email)) {
            $this->redirect("/cadastro?erro=email_existe");
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $user = new User(null, $nome, $email, $hash);
        $this->userRepository->save($user);

        $_SESSION['id_usuario'] = $user->getId();
        $_SESSION['nome_usuario'] = $user->getName();
        $this->redirect('/menu');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}
