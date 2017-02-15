<?php
namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\TextResponse;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\NoopFinalHandler;

require __DIR__ . '/vendor/autoload.php';

$app = new MiddlewarePipe();

$app->pipe(function (ServerRequestInterface $request, DelegateInterface $delegate) {
    // 直接出力されたらレスポンスをその内容に差し替える（デバッグ用）
    ob_start();
    $response = $delegate->process($request);
    $out = ob_get_clean();
    if (strlen($out)) {
        return new HtmlResponse($out);
    }
    return $response;
});

$app->pipe(function (ServerRequestInterface $request, DelegateInterface $delegate) {
    return new TextResponse("Hello World");
});

$server = Server::createServerFromRequest($app, ServerRequestFactory::fromGlobals());
$server->listen(new NoopFinalHandler());
