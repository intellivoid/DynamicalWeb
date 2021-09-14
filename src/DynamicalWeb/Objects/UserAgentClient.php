<?php

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\AbstractClient;

    class UserAgentClient extends AbstractClient
    {
        /**
         * @var UserAgent
         */
        public $ua;

        /**
         * @var UserAgentOperatingSystem
         */
        public $os;

        /**
         * @var UserAgentDevice
         */
        public $device;

        /**
         * @var string
         */
        public $originalUserAgent;

        /**
         * @param string $originalUserAgent
         */
        public function __construct(string $originalUserAgent)
        {
            $this->originalUserAgent = $originalUserAgent;
        }

        /**
         * @return string
         */
        public function toString(): string
        {
            return $this->ua->toString().'/'.$this->os->toString();
        }
    }
