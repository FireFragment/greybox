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

            foreach ($eventRegisteredEntities as $eventRegisteredEntity)
            {
                $userRegisteredEntity->registered = false;
                if ($eventRegisteredEntity->id == $userRegisteredEntity->id)
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
