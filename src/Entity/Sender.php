<?php

declare(strict_types=1);

namespace Quantum\Hub\Entity;

/**
 * @class Sender
 * @package Quantum\Hub\Entity
 */
class Sender extends BaseEntity
{
    /**
     * The name of the user who do the push
     * @var string
     */
    protected string $login = '';

    /**
     * The HTML URL of the user profile
     * @var string
     */
    protected string $htmlUrl = '';

    /**
     *
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     *
     * @return string
     */
    public function getHtmlUrl(): string
    {
        return $this->htmlUrl;
    }
}
