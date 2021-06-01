<?php
declare(strict_types = 1);

namespace UwKluis\Client\Organization;

use DateTime;
use Lcobucci\JWT\Token;

/**
 * Class AccessTokenResponse
 */
final class AccessTokenResponse
{
    /** @var Token */
    private $accessToken;
    /** @var string|null */
    private $refreshToken;
    /** @var DateTime */
    private $expiration;

    /**
     * AccessTokenResponse constructor.
     *
     * @param Token    $accessToken
     * @param string|null   $refreshToken
     * @param DateTime $expiration
     */
    public function __construct(
        Token $accessToken,
        $refreshToken,
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
     * @return string|null
     */
    public function getRefreshToken()
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
