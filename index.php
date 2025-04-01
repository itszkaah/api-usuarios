<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Middleware para tratamento de erro
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode(["error" => "Recurso não foi encontrado"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
});

$usuarios = [];

// Rota GET para listar 5 usuários fictícios
$app->get('/usuarios', function (Request $request, Response $response, array $args) {
    $usuarios = [
        ["id" => 1, "nome" => "Carlos", "login" => "carlos123", "perfil" => "admin"],
        ["id" => 2, "nome" => "Ana", "login" => "ana456", "perfil" => "user"],
        ["id" => 3, "nome" => "Mariana", "login" => "mariana789", "perfil" => "user"],
        ["id" => 4, "nome" => "José", "login" => "jose000", "perfil" => "editor"],
        ["id" => 5, "nome" => "Fernanda", "login" => "fernanda999", "perfil" => "admin"]
    ];
    $response->getBody()->write(json_encode($usuarios));
    return $response->withHeader('Content-Type', 'application/json');
});

// Rota POST para criar usuário
$app->post('/usuarios', function (Request $request, Response $response, array $args) {
    $parametros = (array) $request->getParsedBody();
    if (!isset($parametros['login']) || empty($parametros['login']) ||
        !isset($parametros['senha']) || empty($parametros['senha'])) {
        $response->getBody()->write(json_encode(["mensagem" => "Login e senha são obrigatórios"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    $response->getBody()->write(json_encode(["mensagem" => "Usuário criado com sucesso"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

// Rota DELETE para excluir usuário
$app->delete('/usuarios/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $response->getBody()->write(json_encode(["mensagem" => "Usuário com ID $id removido"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

// Rota PUT para atualizar usuário
$app->put('/usuarios/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $dados_para_atualizar = (array) $request->getParsedBody();
    if (empty($dados_para_atualizar)) {
        $response->getBody()->write(json_encode(["mensagem" => "Nenhum dado para atualizar"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    $response->getBody()->write(json_encode(["mensagem" => "Usuário com ID $id atualizado"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->run();
