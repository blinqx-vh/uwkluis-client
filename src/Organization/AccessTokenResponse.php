<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization;

use DateTime;
use Lcobucci\JWT\Token;

final class AccessTokenResponse
{
    /** @var Token */
    private $accessToken;
    /** @var string */
    private $refreshToken;
    /** @var DateTime */
    private $expiration;

    /**
     * AccessTokenResponse constructor.
     *
     * @param Token     $accessToken
     * @param string     $refreshToken
     * @param DateTime $expiration
     */
    public function __construct(
        Token $accessToken,
        string $refreshToken,
        DateTime $expiration
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
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return DateTime
     */
    public function getExpiration(): DateTime
    {
        return $this->expiration;
    }


}
