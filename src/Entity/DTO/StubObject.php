<?php

namespace App\Entity\DTO;


class StubObject
{
    public int $id;
    public string $name;
    public string $description;
    public ?string $excerpt = null;
    public string $background;
    public array $labels = [];
}