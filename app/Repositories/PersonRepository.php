<?php


namespace App\Repositories;


use App\Person;

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

    public static function findDuplicate(\Illuminate\Http\Request $request)
    {
        if ($request->has('email')) $searchCriteria = $request->only(['name','surname','email']);
        if ($request->has(['birthdate','street','city','zip'])) $searchCriteria = $request->only(['name','surname','birthdate','street','city','zip']);
        if (!isset($searchCriteria)) return null;
        return Person::where($searchCriteria)->first();
    }
}
