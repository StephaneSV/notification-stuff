<?php

namespace app\Model;

class Notification
{
    public int $id;
    public int $id_notification_type;
    public int $id_user;
    public ?int $id_content_type;
    public ?int $id_content;
    public ?string $expires;
    public ?string $description;
    public int $new;
    public string $date_creation;
}