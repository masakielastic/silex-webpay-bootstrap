<?php
require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

if (5 >= PHP_MAJOR_VERSION && !function_exists('openssl_random_pseudo_bytes')) {
    exit('openssl_random_pseudo_bytes を利用できるようにしてください。');
}

class MyApplication extends Application
{
    use Silex\Application\TwigTrait;

    public function csrfToken($length = 48)
    {
        if (PHP_MAJOR_VERSION >= 7) {
            $bytes = random_bytes($length);
        } else {
            $bytes = openssl_random_pseudo_bytes($length);
        }

        return $this->base64urlEncode($bytes);
    }

    public function base64urlEncode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    /**
     * https://github.com/symfony/security-core/blob/master/Util/StringUtils.php
     *
     * Compares two strings.
     *
     * This method implements a constant-time algorithm to compare strings.
     *
     * @param string $knownString The string of known length to compare against
     * @param string $userInput   The string that the user can control
     *
     * @return bool    true if the two strings are the same, false otherwise
     */

    public function hashEquals($knownString, $userInput)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($knownString, $userInput);
        }

        // Prevent issues if string length is 0
        $knownString .= chr(0);
        $userInput .= chr(0);

        $knownLen = strlen($knownString);
        $userLen = strlen($userInput);

        // Set the result to the difference between the lengths
        $result = $knownLen - $userLen;

        // Note that we ALWAYS iterate over the user-supplied length
        // This is to prevent leaking length information
        for ($i = 0; $i < $userLen; $i++) {
            // Using % here is a trick to prevent notices
            // It's safe, since if the lengths are different
            // $result is already non-0
            $result |= (ord($knownString[$i % $knownLen]) ^ ord($userInput[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
    }
}

$app = new MyApplication();
$app->before(function (Request $request, Application $app) {

    if (!file_exists(__DIR__.'/config.php')) {
        throw new Exception('config.php を用意してください。');
    }

    require_once __DIR__.'/config.php';

    if (empty($app_config['public_key']) || empty($app_config['private_key'])) {
        throw new Exception('config.php に公開可能鍵と非公開鍵を記入してください。');
    }

    $dirs = explode('/', $request->getRequestUri());
    $count = count($dirs);
    if ($count === 2) {
        $app_config['base_uri'] = '';
    } else if ($count === 3) {
        $app_config['base_uri'] = '/'.$dirs[1];
    } else {
        throw new Exception('1階層よりも深いディレクトリはサポートされていません。');
    }

    $app['config'] = [
        'public_key' => $app_config['public_key'],
        'private_key' => $app_config['private_key'],
        'views' => $app_config['views'],
        'msg' => $app_config['msg'],
        'charge_uri' => $app_config['base_uri'].'/charges'
    ];

});

$app->register(new SessionServiceProvider(), [
    'session.storage.options' => [
       'name' => 'payment_app',
       'cookie_secure' => true,
       'cookie_httponly' => true
    ]
]);
$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views'
]);

if (!$app['session']->isStarted()) {
    $app['session']->start();
}

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    return $response;
});

$app->get('/', function (Request $request) use ($app) {

    if (!$app['session']->has('csrf-token')) {
        $app['session']->set('csrf-token', $app->csrfToken());
    }

    if (!$request->isSecure()) {
        return new Response("HTTPS でアクセスしてください。\n", 400);
    }

    foreach ($app['config']['views'] as $view) {

       $path = __DIR__.'/views/'.$view;
       $ext = pathinfo($view, PATHINFO_EXTENSION);

        if (file_exists($path)) {

            if ('twig' === $ext) {
                return $app->render($view, [
                    'csrf_token' => $app['session']->get('csrf-token'),
                    'public_key'=> $app['config']['public_key'],
                    'charge_uri' => $app['config']['charge_uri']
                ]);
            }

            if ('php' === $ext) {
                ob_start();
                include $path;
                $content = ob_get_contents();
                ob_end_clean();

                return new Response($content);
            }
        }
    }

    return new Response("ビューファイルが見つかりませんでした。\n", 404);
});

$app->post('/charges', function (Request $request) use ($app) {

    $msg = '';
    $valid = true;

    if (!$request->isSecure()) {
        $msg .= 'HTTPS でアクセスしてください。';
        $valid = false;
    }

    if (!$request->headers->has('x_csrf_token')) {
        $msg .= 'CSRF 対策のヘッダートークンが送信されていません。';
        $valid = false;
    }

    if (!$app['session']->has('csrf-token')) {
        $msg .= 'CSRF 対策のセッショントークンが送信されていません。';
        $valid = false;
    }
    
    if (!$app->hashEquals(
        $app['session']->get('csrf-token'),
        $request->headers->get('x_csrf_token'))
    ) {
        $msg .= 'CSRF 対策のトークンが一致しません。';
        $valid = false;
    }

    if (!$request->request->has('amount')) {
        $msg .= '金額が送信されていません。'.$request->request->get('amount');
        $valid = false;
    }

    if (!$request->request->has('webpay-token')) {
        $msg .= 'クレジットカードのトークンが送信されていません。';
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
        $msg = [
            'msg' => $app['config']['msg']['success']
        ];
        $app['session']->remove('csrf-token');
    } catch (\Exception $e) {
        $status = $e->getStatus();
        $msg = [
        'msg' => $app['config']['msg']['failure'].$e->getMessage()
        ];
    }

    return $app->json($msg, $status);
});

$app->match('/charges', function (Request $request) use($app) {
    return $app->json(['msg' => '許可されない HTTP メソッドです。'], 400);
});

$app->error(function (\Exception $e, $code) {

    if ($code == 404) {
        return new Response('ページが見つかりませんでした。', $code);
    }

    return $e->getMessage();
});

$app->run();
