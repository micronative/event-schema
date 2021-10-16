<?php

namespace Samples\UserService\Entities;

class User
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['name' => $this->name, 'email' => $this->email];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Samples\UserService\Entities\User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return \Samples\UserService\Entities\User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }
}
