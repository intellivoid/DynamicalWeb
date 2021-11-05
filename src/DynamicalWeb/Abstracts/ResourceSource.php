<?php

    namespace DynamicalWeb\Abstracts;

    abstract class ResourceSource
    {
        /**
         * Indicates that the client is requesting for the page contents
         */
        const Page = 'PAGE';

        /**
         * Indicates that the client is requesting for a web asset from a pre-compiled binary
         */
        const WebAsset = 'WEB_ASSET';

        /**
         * Indicates that the client is requesting for a web asset that's compiled
         */
        const CompiledWebAsset = 'COMPILED_WEB_ASSET';

        /**
         * Indicates that the server is responding with the content it has in memory
         */
        const Memory = 'MEMORY';
    }