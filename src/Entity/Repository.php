<?php

declare(strict_types=1);

namespace Quantum\Hub\Entity;

/**
 * @class Repository
 * @package Quantum\Hub\Entity
 */
class Repository extends BaseEntity
{
    /**
     * The name
     * @var string
     */
    protected string $name = '';

    /**
     * The full name (with format organization/repository)
     * @var string
     */
    protected string $fullName = '';

    /**
     * The repository description
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * Whether is private repository
     * @var bool
     */
    protected bool $private = false;

    /**
     * The HTML URL
     * @var string
     */
    protected string $htmlUrl = '';

    /**
     * The size of the repository in byte
     * @var int
     */
    protected int $size = 0;

    /**
     * The created at timestamp
     * @var int
     */
    protected int $createdAt = 0;

    /**
     * The updated time
     * @var string
     */
    protected string $updatedAt = '';

    /**
     * The visibility of this repository
     * @var string
     */
    protected string $visibility = '';

    /**
     * The repository organization name
     * @var string
     */
    protected string $organization = '';

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     *
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     *
     * @return string
     */
    public function getHtmlUrl(): string
    {
        return $this->htmlUrl;
    }

    /**
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     *
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     *
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }
}
