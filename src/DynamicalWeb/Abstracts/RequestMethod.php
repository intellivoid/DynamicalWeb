<?php

    namespace DynamicalWeb\Abstracts;

    abstract class RequestMethod
    {
        /**
         * The OPTIONS method is used to describe the communication options for the target resource.
         */
        const OPTIONS = 'OPTIONS';

        /**
         * The GET method requests a representation of the specified resource.
         * Requests using GET should only retrieve data.
         */
        const GET = 'GET';

        /**
         * The HEAD method asks for a response identical to that of a GET request, but without the response body.
         */
        const HEAD = 'HEAD';

        /**
         * The PUT method replaces all current representations of the target resource with the request payload.
         */
        const PUT = 'PUT';

        /**
         * The POST method is used to submit an entity to the specified resource, often
         * causing a change in state or side effects on the server.
         */
        const POST = 'POST';

        /**
         * The DELETE method deletes the specified resource.
         */
        const DELETE = 'DELETE';

        /**
         * The PATCH method is used to apply partial modifications to a resource.
         */
        const PATCH = 'PATCH';
    }