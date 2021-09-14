<?php

    namespace DynamicalWeb\Abstracts;

    abstract class AbstractClient
    {
        /**
         * @return string
         */
        abstract public function toString(): string;

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->toString();
        }
    }
