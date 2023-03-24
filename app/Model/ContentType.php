<?php

namespace App\Model;

class ContentType
{
    const ID_ALBUM = 1;
    const ID_PLAYLIST = 2;
    const ID_PODCAST = 3;
    const ID_TRACK = 4;
    
    public int $id;
    public string $name;
}