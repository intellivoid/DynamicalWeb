<?php

    namespace DynamicalWeb\Objects;


    use DynamicalWeb\Objects\WebApplication\Route;

    class PathIndex
    {
        /**
         * The route of the page
         *
         * @var Route
         */
        public $Route;

        /**
         * The directory path of the page source
         *
         * @var string
         */
        public $PagePath;

        /**
         * The path of the page's execution point
         *
         * @var string
         */
        public $PageExecutionPoint;

        /**
         * Returns an Array Representation of the object
         *
         * @return array
         * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public function toArray(): array
        {
            return [
                'route' => $this->Route->toArray(),
                'page_path' => $this->PagePath,
                'page_execution_point' => $this->PageExecutionPoint
            ];
        }

        /**
         * Constructs an object from an array representation
         *
         * @param array $data
         * @return PathIndex
         * @noinspection PhpPureAttributeCanBeAddedInspection
         */
        public static function fromArray(array $data): PathIndex
        {
            $PathIndexObject = new PathIndex();

            if(isset($data['route']))
                $PathIndexObject->Route = Route::fromArray($data['route']);

            if(isset($data['page_path']))
                $PathIndexObject->PagePath = $data['page_path'];

            if(isset($data['page_execution_point']))
                $PathIndexObject->PageExecutionPoint = $data['page_execution_point'];

            return $PathIndexObject;
        }
    }