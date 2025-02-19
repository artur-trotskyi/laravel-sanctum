<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
    public function all(): Collection;

    public function create(array $data): Model;

    public function update(string $id, array $data): bool;

    public function destroy(string $id): bool;

    public function getById(string $id): ?Model;
}
