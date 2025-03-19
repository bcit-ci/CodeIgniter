<?php

namespace Doctrine\ORM\Persisters;

use Doctrine\ORM\ORMException;

class PersisterException extends ORMException
{
    /**
     * @return PersisterException
     */
    static public function matchingAssocationFieldRequiresObject($class, $associationName)
    {
        return new self(sprintf(
            "Cannot match on %s::%s with a non-object value. Matching objects by id is " .
            "not compatible with matching on an in-memory collection, which compares objects by reference.",
            $class, $associationName
        ));
    }
}
