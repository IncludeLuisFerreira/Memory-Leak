<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Room;

interface RoomRepositoryInterface
{
    public function findById(int $id): ?Room;
    public function findAvailable(): ?Room;
    public function save(Room $room): void;
    public function update(Room $room): void;
}
