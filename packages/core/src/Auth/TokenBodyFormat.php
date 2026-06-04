<?php

namespace Sanvex\Core\Auth;

enum TokenBodyFormat: string
{
    /** application/x-www-form-urlencoded (Google and most OAuth2 providers). */
    case Form = 'form';

    /** application/json body (Notion token endpoint). */
    case Json = 'json';
}
