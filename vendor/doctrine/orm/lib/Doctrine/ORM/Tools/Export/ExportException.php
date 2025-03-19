<?php

namespace Doctrine\ORM\Tools\Export;

use Doctrine\ORM\ORMException;

class ExportException extends ORMException
{
    /**
     * @param string $type
     *
     * @return ExportException
     */
    public static function invalidExporterDriverType($type)
    {
        return new self("The specified export driver '$type' does not exist");
    }

    /**
     * @param string $type
     *
     * @return ExportException
     */
    public static function invalidMappingDriverType($type)
    {
        return new self("The mapping driver '$type' does not exist");
    }

    /**
     * @param string $file
     *
     * @return ExportException
     */
    public static function attemptOverwriteExistingFile($file)
    {
        return new self("Attempting to overwrite an existing file '".$file."'.");
    }
}