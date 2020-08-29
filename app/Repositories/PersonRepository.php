<?php


namespace App\Repositories;


class PersonRepository extends AutofillRepository
{
    public static function getAutofillPeople($userRegisteredPeople, $deletedPeople, $eventRegisteredPeople)
    {
        return parent::getAutofill($userRegisteredPeople, $deletedPeople, $eventRegisteredPeople);
    }

    public static function getPeopleFromRegistrations($registrations)
    {
        $people = array();
        foreach ($registrations as $registration)
        {
            $person = $registration->person()->first();
            if (!empty($person))
            {
                $people[] = $person;
            }
        }
        return $people;
    }
}
