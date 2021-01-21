<?php


    namespace DynamicalWeb;


    use RuntimeException;

    /**
     * Class BufferStream
     * @package DynamicalWeb
     */
    class BufferStream
    {
        /**
         * The content of the current buffer stream
         *
         * @var string|null
         */
        private static $Content;

        /**
         * Determines if DynamicalWeb is configured to create a buffer stream for the output
         *
         * @return bool
         */
        public static function bufferOutputEnabled(): bool
        {
            $configuration = DynamicalWeb::getWebConfiguration();

            if(isset($configuration["configuration"]["buffer_output"]))
            {
                return (bool)$configuration["configuration"]["buffer_output"];
            }

            return true;
        }

        /**
         * Starts the buffer stream
         *
         * @return bool
         */
        public static function startStream(): bool
        {
            ob_start();
            return True;
        }

        /**
         * Returns the stream buffer output
         *
         * @return string
         */
        public static function endStream(): string
        {
            $results = ob_get_contents();
            ob_get_clean();
            ob_end_flush();

            if($results == false)
            {
                throw new RuntimeException("There was an error while trying to obtain the buffer output.");
            }

            self::$Content = $results;
            return $results;
        }

        /**
         * Returns the current content of the buffer stream.
         *
         * @return string|null
         */
        public static function getContent(): ?string
        {
            return self::$Content;
        }
    }