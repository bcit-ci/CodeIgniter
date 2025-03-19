<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Persistence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * PersistentObject base class that implements getter/setter methods for all mapped fields and associations
 * by overriding __call.
 *
 * This class is a forward compatible implementation of the PersistentObject trait.
 *
 * Limitations:
 *
 * 1. All persistent objects have to be associated with a single ObjectManager, multiple
 *    ObjectManagers are not supported. You can set the ObjectManager with `PersistentObject#setObjectManager()`.
 * 2. Setters and getters only work if a ClassMetadata instance was injected into the PersistentObject.
 *    This is either done on `postLoad` of an object or by accessing the global object manager.
 * 3. There are no hooks for setters/getters. Just implement the method yourself instead of relying on __call().
 * 4. Slower than handcoded implementations: An average of 7 method calls per access to a field and 11 for an association.
 * 5. Only the inverse side associations get autoset on the owning side as well. Setting objects on the owning side
 *    will not set the inverse side associations.
 *
 * @example
 *
 *  PersistentObject::setObjectManager($em);
 *
 *  class Foo extends PersistentObject
 *  {
 *      private $id;
 *  }
 *
 *  $foo = new Foo();
 *  $foo->getId(); // method exists through __call
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
abstract class PersistentObject implements ObjectManagerAware
{
    /**
     * @var ObjectManager|null
     */
    private static $objectManager = null;

    /**
     * @var ClassMetadata|null
     */
    private $cm = null;

    /**
     * Sets the object manager responsible for all persistent object base classes.
     *
     * @param ObjectManager|null $objectManager
     *
     * @return void
     */
    static public function setObjectManager(ObjectManager $objectManager = null)
    {
        self::$objectManager = $objectManager;
    }

    /**
     * @return ObjectManager|null
     */
    static public function getObjectManager()
    {
        return self::$objectManager;
    }

    /**
     * Injects the Doctrine Object Manager.
     *
     * @param ObjectManager $objectManager
     * @param ClassMetadata $classMetadata
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function injectObjectManager(ObjectManager $objectManager, ClassMetadata $classMetadata)
    {
        if ($objectManager !== self::$objectManager) {
            throw new \RuntimeException("Trying to use PersistentObject with different ObjectManager instances. " .
                "Was PersistentObject::setObjectManager() called?");
        }

        $this->cm = $classMetadata;
    }

    /**
     * Sets a persistent fields value.
     *
     * @param string $field
     * @param array  $args
     *
     * @return void
     *
     * @throws \BadMethodCallException   When no persistent field exists by that name.
     * @throws \InvalidArgumentException When the wrong target object type is passed to an association.
     */
    private function set($field, $args)
    {
        $this->initializeDoctrine();

        if ($this->cm->hasField($field) && !$this->cm->isIdentifier($field)) {
            $this->$field = $args[0];
        } else if ($this->cm->hasAssociation($field) && $this->cm->isSingleValuedAssociation($field)) {
            $targetClass = $this->cm->getAssociationTargetClass($field);
            if (!($args[0] instanceof $targetClass) && $args[0] !== null) {
                throw new \InvalidArgumentException("Expected persistent object of type '".$targetClass."'");
            }
            $this->$field = $args[0];
            $this->completeOwningSide($field, $targetClass, $args[0]);
        } else {
            throw new \BadMethodCallException("no field with name '".$field."' exists on '".$this->cm->getName()."'");
        }
    }

    /**
     * Gets a persistent field value.
     *
     * @param string $field
     *
     * @return mixed
     *
     * @throws \BadMethodCallException When no persistent field exists by that name.
     */
    private function get($field)
    {
        $this->initializeDoctrine();

        if ( $this->cm->hasField($field) || $this->cm->hasAssociation($field) ) {
            return $this->$field;
        } else {
            throw new \BadMethodCallException("no field with name '".$field."' exists on '".$this->cm->getName()."'");
        }
    }

    /**
     * If this is an inverse side association, completes the owning side.
     *
     * @param string        $field
     * @param ClassMetadata $targetClass
     * @param object        $targetObject
     *
     * @return void
     */
    private function completeOwningSide($field, $targetClass, $targetObject)
    {
        // add this object on the owning side as well, for obvious infinite recursion
        // reasons this is only done when called on the inverse side.
        if ($this->cm->isAssociationInverseSide($field)) {
            $mappedByField = $this->cm->getAssociationMappedByTargetField($field);
            $targetMetadata = self::$objectManager->getClassMetadata($targetClass);

            $setter = ($targetMetadata->isCollectionValuedAssociation($mappedByField) ? "add" : "set").$mappedByField;
            $targetObject->$setter($this);
        }
    }

    /**
     * Adds an object to a collection.
     *
     * @param string $field
     * @param array  $args
     *
     * @return void
     *
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    private function add($field, $args)
    {
        $this->initializeDoctrine();

        if ($this->cm->hasAssociation($field) && $this->cm->isCollectionValuedAssociation($field)) {
            $targetClass = $this->cm->getAssociationTargetClass($field);
            if (!($args[0] instanceof $targetClass)) {
                throw new \InvalidArgumentException("Expected persistent object of type '".$targetClass."'");
            }
            if (!($this->$field instanceof Collection)) {
                $this->$field = new ArrayCollection($this->$field ?: []);
            }
            $this->$field->add($args[0]);
            $this->completeOwningSide($field, $targetClass, $args[0]);
        } else {
            throw new \BadMethodCallException("There is no method add".$field."() on ".$this->cm->getName());
        }
    }

    /**
     * Initializes Doctrine Metadata for this class.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    private function initializeDoctrine()
    {
        if ($this->cm !== null) {
            return;
        }

        if (!self::$objectManager) {
            throw new \RuntimeException("No runtime object manager set. Call PersistentObject#setObjectManager().");
        }

        $this->cm = self::$objectManager->getClassMetadata(get_class($this));
    }

    /**
     * Magic methods.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        $command = substr($method, 0, 3);
        $field = lcfirst(substr($method, 3));
        if ($command == "set") {
            $this->set($field, $args);
        } else if ($command == "get") {
            return $this->get($field);
        } else if ($command == "add") {
            $this->add($field, $args);
        } else {
            throw new \BadMethodCallException("There is no method ".$method." on ".$this->cm->getName());
        }
    }
}
