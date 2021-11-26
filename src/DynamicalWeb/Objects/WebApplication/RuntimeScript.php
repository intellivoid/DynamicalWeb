<?php

    /** @noinspection PhpMissingFieldTypeInspection */

    namespace DynamicalWeb\Objects\WebApplication;

    use DynamicalWeb\Abstracts\RuntimeEvent;

    class RuntimeScript
    {
        /**
         * Indicates when the script is to be executed
         *
         * @var string|RuntimeEvent
         */
        public $Event;

        /**
         * The script that is to be executed
         *
         * @var string
         */
        public $Script;

        /**
         * The resolved execution point of the script
         *
         * @var string
         */
        public $ExecutionPoint;

        /**
         * Executes the runtime script
         */
        public function execute()
        {
            include($this->ExecutionPoint);
        }

        /**
         * Returns an array representation of the object
         *
         * @return array
         */
        public function toArray(): array
        {
            return [
                'event' => $this->Event,
                'script' => $this->Script,
                'execution_point' => $this->ExecutionPoint
            ];
        }

        /**
         * Constructs object from an array representation
         *
         * @param array $data
         * @return RuntimeScript
         */
        public static function fromArray(array $data): RuntimeScript
        {
            $RuntimeScriptObject = new RuntimeScript();

            if(isset($data['event']))
                $RuntimeScriptObject->Event = $data['event'];

            if(isset($data['script']))
                $RuntimeScriptObject->Script = $data['script'];

            if(isset($data['execution_point']))
                $RuntimeScriptObject->ExecutionPoint = $data['execution_point'];

            return $RuntimeScriptObject;
        }
    }