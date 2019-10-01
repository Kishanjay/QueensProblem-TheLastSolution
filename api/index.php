<?php

require '../vendor/autoload.php';

$app = new Silex\Application();

require '_queensproblemsolver.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['debug'] = true;

/* CORS fix */
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers',
        'Origin, X-Requested-With, Content-Type, Accept, Authorization');
});
$app->options("{path}", function () {
    return new \Symfony\Component\HttpFoundation\JsonResponse(null, 204);
})->assert("path", ".*");

/* Parse JSON body */
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->post('/queensproblemsolver', function (Request $request, Silex\Application $app) {
    $startTime = microtime(true);

    $board = $request->request->get('board');
    $solved = QueensProblem::solve($board);

    $endTime = microtime(true);

    $results = [
        'isValidBoard' => $solved['isValidBoard'],
        'boardSize' => $solved['boardSize'],
        'numberOfSolutions' => sizeof($solved['solutions']),
        'computationTime' => round(($endTime - $startTime)*1000, 2),
        'solutions' => $solved['solutions'],
    ];

    return $app->json($results, 200);
});

$app->run();
