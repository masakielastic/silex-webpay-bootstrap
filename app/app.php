<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

if (!file_exists(__DIR__.'/config.php')) {
    echo 'config.php を用意してください。';
    exit;
}

require_once __DIR__.'/config.php';

class MyApplication extends Application
{
    use Silex\Application\TwigTrait;

    public function csrfToken($length = 48)
    {
        return $this->base64urlEncode(openssl_random_pseudo_bytes($length));
    }

    public function base64urlEncode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}

$app = new MyApplication();
// $app['debug'] = true;

$app->before(function() use(&$app, $config) {
    $app['config'] = [
        'public_key' => $config['public_key'],
        'private_key' => $config['private_key'],
        'payment_uri' => $config['base_uri'].'/payment'
    ];
});
$app->register(new SessionServiceProvider(), [
    'session.storage.options' => [
         'name' => $config['app_name'],
         'cookie_secure' => true,
         'cookie_httponly' => true
    ]
]);
$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views'
]);

$app['session']->start();

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    return $response;
});

$app->get('/', function () use ($app) {

    if (!$app['session']->has('csrf-token')) {
        $app['session']->set('csrf-token', $app->csrfToken());
    }

    if (!file_exists(__DIR__.'/views/index.twig')) {
        return new Response("index.twig を用意してください。\n", 404);
    }

    return $app->render('index.twig', [
        'csrf_token' => $app['session']->get('csrf-token'),
        'public_key'=> $app['config']['public_key'],
        'uri' => $app['config']['payment_uri']
    ]);
});

$app->post('/payment', function (Request $request) use ($app) {

    $msg = '';
    $valid = true;

    $valid = $valid && $request->headers->has('x_csrf_token');

    if (!$valid) {
        $msg .= 'CSRF 対策のヘッダートークンが送信されていません。';
    }

    $valid = $valid && $app['session']->has('csrf-token');

    if (!$valid) {
        $msg .= 'CSRF 対策のセッショントークンが送信されていません。';
    }
    
    $valid = $valid &&
    $request->headers->get('x_csrf_token') === $app['session']->get('csrf-token');

    if (!$valid) {
        $msg .= 'CSRF 対策のトークンが一致しません。';
    }

    $ret = $request->headers->get('x_csrf_token') === $app['session']->get('csrf-token') ?
        'true' : 'false';

    $app['session']->remove('csrf-token');

    if (!$request->request->has('webpay-token')) {
        $msg .= 'クレジットカードのトークンが送信されていません。';
        $valid = false;
    }

    if (!$request->request->has('amount')) {
        $msg .= '金額が送信されていません。';
        $valid = false;
    }

    if (!$valid) {
        return $app->json(['msg' => $msg], 400);
    }

    $data = [
        'amount' => (int) $request->request->get('amount'),
        'currency' => 'jpy',
        'card' => $request->request->get('webpay-token')
    ];

    try {
        $webpay = new WebPay\WebPay($app['config']['private_key']);
        $webpay->charge->create($data);
        $status = 200;
        $msg = ['msg' => 'ありがとうございます。'];
        $app['session']->remove('csrf-token');
    } catch (\Exception $e) {
        $status = $e->getStatus();
        $msg = ['msg' => $e->getMessage()];
    }

    return $app->json($msg, $status);
});

$app->match('/payment', function (Request $request) use($app) {
    return $app->json(['msg' => '許可されないメソッドです。'], 400);
});

$app->error(function (\Exception $e, $code) {
    return new Response('存在しないページです。');
});

$app->run();
