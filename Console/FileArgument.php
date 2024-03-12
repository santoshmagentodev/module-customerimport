<?php
declare(strict_types=1);

namespace Pramod\CustomerImport\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * File type Options helper
 *
 * This class contains the list options and their related constants,
 * which can be used for customer import CLI command
 */
class FileArgument
{
    /**
     * Key for file type
     */
    const FILE_TYPE = 'file-type';

    /**
     * Key for file name
     */
    const FILE_NAME = 'file-name';

    /**
     * Argument list
     *
     * @return array
     */
    public function getArgumentList()
    {
        return [
            new InputArgument(
                self::FILE_TYPE,
                InputArgument::REQUIRED,
                "File type"
            ),
            new InputArgument(
                self::FILE_NAME,
                InputArgument::REQUIRED,
                "File name"
            )
        ];
    }    
}
