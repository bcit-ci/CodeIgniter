<?xml version="1.0" encoding="UTF-8"?>

<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false"
         syntaxCheck="false"
         bootstrap="./tests/Doctrine/Tests/TestInit.php"
>
    <testsuites>
        <testsuite name="Doctrine Inflector Test Suite">
            <directory>./tests/Doctrine/</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory>./lib/Doctrine/</directory>
        </whitelist>
    </filter>
    
    <groups>
        <exclude>
            <group>performance</group>
        </exclude>
    </groups>
</phpunit>
