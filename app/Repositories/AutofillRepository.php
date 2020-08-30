<?php


namespace App\Repositories;


abstract class AutofillRepository
{
    protected static function getAutofill($userRegisteredEntities, $deletedEntities, $eventRegisteredEntities)
    {
        $autofill = array();
        foreach ($userRegisteredEntities as $userRegisteredEntity)
        {
            foreach ($deletedEntities as $deletedEntity)
            {
                if ($deletedEntity->id === $userRegisteredEntity->id)
                {
                    continue 2;
                }
            }

            $userRegisteredEntity->registered = false;
            foreach ($eventRegisteredEntities as $eventRegisteredEntity)
            {
                if ($eventRegisteredEntity->id === $userRegisteredEntity->id)
                {
                    $userRegisteredEntity->registered = true;
                    break;
                }
            }

            $autofill[] = $userRegisteredEntity;
        }
        return $autofill;
    }
}
