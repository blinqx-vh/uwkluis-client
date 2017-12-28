<?php
declare(strict_types = 1);

namespace Ufo\Client\Connection;

use Lcobucci\JWT\Token;

final class AccessTokenResponse
{
    /** @var Token */
    private $accessToken;
    /** @var Token */
    private $refreshToken;
    /** @var \DateTime */
    private $expiration;

    /**
     * AccessTokenResponse constructor.
     *
     * @param Token     $accessToken
     * @param Token     $refreshToken
     * @param \DateTime $expiration
     */
    public function __construct(
        Token $accessToken,
        Token $refreshToken,
        \DateTime $expiration
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiration = $expiration;
    }

    /**
     * @return Token
     */
    public function getAccessToken(): Token
    {
        return $this->accessToken;
    }

    /**
     * @return Token
     */
    public function getRefreshToken(): Token
    {
        return $this->refreshToken;
    }

    /**
     * @return \DateTime
     */
    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }


}
