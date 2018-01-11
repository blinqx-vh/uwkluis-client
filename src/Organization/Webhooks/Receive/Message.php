<?php
declare(strict_types = 1);

namespace Ufo\Client\Organization\Webhooks\Receive;

use DateTime;

final class Message
{
    /** @var array */
    private $data;
    /** @var int */
    private $webhookId;
    /** @var string */
    private $identifier;
    /** @var int */
    private $tries;
    /** @var string */
    private $event;
    /** @var int */
    private $sequence;
    /** @var DateTime */
    private $timestamp;

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @return int
     */
    public function getWebhookId(): int
    {
        return $this->webhookId;
    }

    /**
     * @param int $webhookId
     *
     * @return Message
     */
    public function setWebhookId($webhookId)
    {
        $this->webhookId = $webhookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return Message
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }


    /**
     * @return int
     */
    public function getTries(): int
    {
        return $this->tries;
    }

    /**
     * @param int $tries
     *
     * @return Message
     */
    public function setTries($tries)
    {
        $this->tries = $tries;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     *
     * @return Message
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return int
     */
    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @param int $sequence
     *
     * @return Message
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     *
     * @return Message
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }


}
