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
         * Indicates if the buffer stream is currently streaming
         *
         * @var bool
         */
        private static $CurrentlyStreaming = false;

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
            if(self::$CurrentlyStreaming)
                self::endStream();

            ob_start();
            self::$CurrentlyStreaming = true;
            return True;
        }

        /**
         * Returns the stream buffer output
         *
         * @return string|null
         */
        public static function endStream(): ?string
        {
            if (ob_get_contents())
            {
                $results = ob_get_contents();
                ob_get_clean();

                if($results == false)
                {
                    throw new RuntimeException("There was an error while trying to obtain the buffer output.");
                }

                self::$CurrentlyStreaming = false;
                self::$Content = $results;
            }
            else
            {
                self::$CurrentlyStreaming = false;
                self::$Content = null;
                $results = null;
            }

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