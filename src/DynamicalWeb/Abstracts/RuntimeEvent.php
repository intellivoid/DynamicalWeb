<?php

    namespace DynamicalWeb\Abstracts;

    abstract class RuntimeEvent
    {
        const PreRequest = 'pre_request';

        const PostRequest = 'post_Request';

        const PrePageLoad = 'pre_page_load';

        const PostPageLoad = 'post_page_load';
    }