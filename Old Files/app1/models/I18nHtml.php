<?php

class I18nHtml extends BaseModel
{
    public static function nextKey()
    {
        $first = self::orderBy('key', 'desc')->first();
        return $first ? intval($first->key) + 1 : 1;
    }
}