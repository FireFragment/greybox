<?php

namespace App\Objects;

use App\Person;
use Illuminate\Database\Eloquent\Builder;

class RegistrationGroup
{
    /* @var Builder */
    private $builder;

    /* @var \App\Registration[] */
    private $registrations;

    /* @var Person[] */
    private $people = array();

    /* @var int */
    private $groupSize;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->registrations = $this->builder->get();
        $this->setPeople();

        $this->groupSize = $this->registrations->count();

    }

    /**
     * Returns number of people in the group with given role
     * @param int $roleId
     * @return int
     */
    public function countPeopleByRole(int $roleId): int
    {
        return $this->registrations->where('role', $roleId)->count();
    }

    /**
     * @return Person[]
     */
    public function getPeople(): array
    {
        return $this->people;
    }

    public function isEmpty(): bool
    {
        return $this->groupSize === 0;
    }

    private function setPeople(): void
    {
        foreach ($this->registrations as $registration) {
            $this->people[] = $registration->person()->first();
        }
    }
}