<?php


namespace App\Repositories;


class PeopleRepository
{
    public static function getAutofillPeople($userRegisteredPeople, $deletedPeople, $eventRegisteredPeople)
    {
        $people = array();
        foreach ($userRegisteredPeople as $userRegisteredPerson)
        {
            foreach ($deletedPeople as $deletedPerson)
            {
                if ($deletedPerson->id === $userRegisteredPerson->id)
                {
                    continue 2;
                }
            }

            foreach ($eventRegisteredPeople as $eventRegisteredPerson)
            {
                $userRegisteredPerson->registered = false;
                if ($eventRegisteredPerson->id == $userRegisteredPerson->id)
                {
                    $userRegisteredPerson->registered = true;
                    break;
                }
            }

            $people[] = $userRegisteredPerson;
        }
        return $people;
    }
}
