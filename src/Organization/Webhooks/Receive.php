<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization\Webhooks;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

final class Receive
{
    /**
     * @param ServerRequestInterface $request
     * @param string                 $secret
     * @param callable|null          $callable
     *
     * @return Response
     */
    public function process(ServerRequestInterface $request, string $secret, callable $callable = null)
    {
        $digestable = $request->getBody()->getContents() . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        if ($request->getHeader('X-Hook-Signature') === $digest) {
            $data = json_decode($request->getBody()->getContents());
            $callable($data);
            return new Response(
                200,
                [
                    'X-Hook-Secret' => $secret,
                ],
                'received');
        }

        return new Response(
            400,
            [],
            'Invalid signature'
        );
    }
}
