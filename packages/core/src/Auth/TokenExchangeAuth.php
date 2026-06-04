<?php

namespace Sanvex\Core\Auth;

enum TokenExchangeAuth: string
{
    /** Client id/secret sent as HTTP Basic (e.g. Notion). */
    case Basic = 'basic';

    /** Client id/secret sent in the form body (e.g. Google). */
    case RequestBody = 'body';
}
