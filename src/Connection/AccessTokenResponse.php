<?php
declare(strict_types = 1);

namespace Ufo\Client\Connection;

final class AccessTokenResponse
{
    /** @var string */
    private $accessToken;
    /** @var string */
    private $refreshToken;
    /** @var \DateTime */
    private $expiration;

    /**
     * AccessTokenResponse constructor.
     *
     * @param string    $accessToken
     * @param string    $refreshToken
     * @param \DateTime $expiration
     */
    public function __construct(
        string $accessToken,
        string $refreshToken,
        \DateTime $expiration
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiration = $expiration;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
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
     * @return \DateTime
     */
    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }


}
