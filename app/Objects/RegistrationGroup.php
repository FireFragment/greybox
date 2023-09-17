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
     * Returns number of accommodated people in the group with given role
     * @param int $roleId
     * @return int
     */
    public function countAccommodatedPeopleByRole(int $roleId): int
    {
        return $this->registrations->where('accommodation', true)->where('role', $roleId)->count();
    }

    /**
     * Returns number of people who ordered meals in the group with given role
     * @param int $roleId
     * @return int
     */
    public function countEatingPeopleByRole(int $roleId): int
    {
        return $this->registrations->where('meals', true)->where('role', $roleId)->count();
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

    public function update(int $invoiceId = null): void
    {
        $this->builder->update([
            'confirmed' => true,
            'invoice' => $invoiceId
        ]);
    }

    /**
     * @return array
     */
    public function getPeopleForEmail($lang = 'cs'): array
    {
        $people = array();
        foreach ($this->registrations as $registration) {
            $person = $registration->person()->first();
            $role = $registration->role()->first()->translation()->first()->$lang;
            $team = $registration->team()->first()->name ?? 'emptyTeamName';
            $people[$role][$team][] = $person->name . ' ' . $person->surname;
        }
        return $people;
    }
}