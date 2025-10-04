<?php

declare(strict_types=1);

namespace Quantum\Hub\Entity;

/**
 * @class Commit
 * @package Quantum\Hub\Entity
 */
class Commit extends BaseEntity
{
    /**
     * The id of the commit
     * @var string
     */
    protected string $id = '';

    /**
     * The commit message
     * @var string
     */
    protected string $message = '';

    /**
     * The time of commit
     * @var string
     */
    protected string $timestamp = '';

    /**
     * The URL of the commit
     * @var string
     */
    protected string $url = '';

    /**
     * The files added
     * @var array<string>
     */
    protected array $added = [];

    /**
     * The files modified
     * @var array<string>
     */
    protected array $modified = [];

    /**
     * The files removed
     * @var array<string>
     */
    protected array $removed = [];

    /**
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     *
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     *
     * @return array<string>
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    /**
     *
     * @return array<string>
     */
    public function getModified(): array
    {
        return $this->modified;
    }

    /**
     *
     * @return array<string>
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }
}
