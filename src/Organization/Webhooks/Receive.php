<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization\Webhooks;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Receive
 */
final class Receive
{
    /**
     * @param ServerRequestInterface $request
     * @param string                 $secret
     * @param callable|null          $callable
     *
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $secret,
        callable $callable = null
    ) {
        $digestable = (string) $request->getBody() . $secret;
        $digest = hash_hmac('sha256', $digestable, $secret);
        if ($request->getHeader('X-Hook-Signature')
            && $request->getHeader('X-Hook-Signature')[0] === $digest) {
            $data = $request->getParsedBody();
            $callable($data);

            return $response
                ->withHeader('X-Hook-Secret', $secret)
                ->withStatus(200, 'received');
        }

        return $response
            ->withStatus(400, 'Invalid signature');
    }
}
