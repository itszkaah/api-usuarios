<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
 
require __DIR__ . '/vendor/autoload.php';
 
$app = AppFactory::create();
 
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
 
$usuario = [
    ["id" =>1,"titulo" => "Lavar a louça", "concluido" => false],
    ["id" =>3,"titulo" => "Cozinhar", "concluido" => true],
    ["id" =>4,"titulo" => "Secar roupa", "concluido" => true],
    ["id" =>5,"titulo" => "Lavar roupa", "concluido" => false],
];
 
$app->get('/usuario', function (Request $request, Response $response, array $args) use ($usuario) {
    $response->getBody()->write(json_encode($usuario));
    return $response->withHeader('Content-Type', 'application/json');
});
 
$app->post('/usuario', function (Request $request, Response $response, array $args) {
    $data = (array) $request->getParsedBody();
    if (!isset($data['login']) || !isset($data['senha'])) {
        $response->getBody()->write(json_encode(["mensagem" => "Login e senha são obrigatórios"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    return $response->withStatus(201);
});
 
$app->delete('/usuario/{id}', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $response->getBody()->write(json_encode(["mensagem" => "Usuário com ID $id deletado"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});
 
$app->put('/usuario/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $data = (array) $request->getParsedBody();
    return $response->withStatus(200);
});
 
$app->run();
 