<?php

    namespace DynamicalWeb\Objects;

    use DynamicalWeb\Abstracts\AbstractSoftware;

    class UserAgentDevice extends AbstractSoftware
    {
        /**
         * @var string|null
         */
        public $brand;

        /**
         * @var string|null
         */
        public $model;
    }
