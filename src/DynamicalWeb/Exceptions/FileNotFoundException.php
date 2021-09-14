<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace DynamicalWeb\Exceptions;


    use Exception;
    use Throwable;

    class FileNotFoundException extends Exception
    {
        /**
         * @var Throwable|null
         */
        private ?Throwable $previous;

        /**
         * @var string
         */
        private string $file_path;

        /**
         * @param string $message
         * @param string $file_path
         * @param int $code
         * @param Throwable|null $previous
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public function __construct($message = "", $file_path = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->message = $message;
            $this->code = $code;
            $this->previous = $previous;
            $this->file_path = $file_path;
        }
    }
