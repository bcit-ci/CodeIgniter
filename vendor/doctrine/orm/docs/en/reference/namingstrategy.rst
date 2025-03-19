Implementing a NamingStrategy
==============================

.. versionadded:: 2.3

Using a naming strategy you can provide rules for automatically generating
database identifiers, columns and tables names
when the table/column name is not given.
This feature helps reduce the verbosity of the mapping document,
eliminating repetitive noise (eg: ``TABLE_``).


Configuring a naming strategy
-----------------------------
The default strategy used by Doctrine is quite minimal.

By default the ``Doctrine\ORM\Mapping\DefaultNamingStrategy``
uses the simple class name and the attributes names to generate tables and columns

You can specify a different strategy by calling ``Doctrine\ORM\Configuration#setNamingStrategy()`` :

.. code-block:: php

    <?php
    $namingStrategy = new MyNamingStrategy();
    $configuration()->setNamingStrategy($namingStrategy);

Underscore naming strategy
---------------------------

``\Doctrine\ORM\Mapping\UnderscoreNamingStrategy`` is a built-in strategy
that might be a useful if you want to use a underlying convention.

.. code-block:: php

    <?php
    $namingStrategy = new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_UPPER);
    $configuration()->setNamingStrategy($namingStrategy);

Then SomeEntityName will generate the table SOME_ENTITY_NAME when CASE_UPPER
or some_entity_name using CASE_LOWER is given.


Naming strategy interface
-------------------------
The interface ``Doctrine\ORM\Mapping\NamingStrategy`` allows you to specify
a "naming standard" for database tables and columns.

.. code-block:: php

    <?php
    /**
     * Return a table name for an entity class
     *
     * @param string $className The fully-qualified class name
     * @return string A table name
     */
    function classToTableName($className);

    /**
     * Return a column name for a property
     *
     * @param string $propertyName A property
     * @return string A column name
     */
    function propertyToColumnName($propertyName);

    /**
     * Return the default reference column name
     *
     * @return string A column name
     */
    function referenceColumnName();

    /**
     * Return a join column name for a property
     *
     * @param string $propertyName A property
     * @return string A join column name
     */
    function joinColumnName($propertyName, $className = null);

    /**
     * Return a join table name
     *
     * @param string $sourceEntity The source entity
     * @param string $targetEntity The target entity
     * @param string $propertyName A property
     * @return string A join table name
     */
    function joinTableName($sourceEntity, $targetEntity, $propertyName = null);

    /**
     * Return the foreign key column name for the given parameters
     *
     * @param string $entityName A entity
     * @param string $referencedColumnName A property
     * @return string A join column name
     */
    function joinKeyColumnName($entityName, $referencedColumnName = null);

Implementing a naming strategy
-------------------------------
If you have database naming standards like all tables names should be prefixed
by the application prefix, all column names should be upper case,
you can easily achieve such standards by implementing a naming strategy.
You need to implements NamingStrategy first. Following is an example


.. code-block:: php

    <?php
    class MyAppNamingStrategy implements NamingStrategy
    {
        public function classToTableName($className)
        {
            return 'MyApp_' . substr($className, strrpos($className, '\\') + 1);
        }
        public function propertyToColumnName($propertyName)
        {
            return $propertyName;
        }
        public function referenceColumnName()
        {
            return 'id';
        }
        public function joinColumnName($propertyName, $className = null)
        {
            return $propertyName . '_' . $this->referenceColumnName();
        }
        public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
        {
            return strtolower($this->classToTableName($sourceEntity) . '_' .
                    $this->classToTableName($targetEntity));
        }
        public function joinKeyColumnName($entityName, $referencedColumnName = null)
        {
            return strtolower($this->classToTableName($entityName) . '_' .
                    ($referencedColumnName ?: $this->referenceColumnName()));
        }
    }

Configuring the namingstrategy is easy if.
Just set your naming strategy calling ``Doctrine\ORM\Configuration#setNamingStrategy()`` :.

.. code-block:: php

    <?php
    $namingStrategy = new MyAppNamingStrategy();
    $configuration()->setNamingStrategy($namingStrategy);
