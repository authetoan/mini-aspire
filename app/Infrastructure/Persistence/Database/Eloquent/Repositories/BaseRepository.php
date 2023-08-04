<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Database\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOneBy(array $criteria): Model
    {
        return $this->model->where($criteria)->first();
    }

    public function all(): array
    {
        return $this->model->all()->toArray();
    }

    public function update(int $id, array $updateData): void
    {
        $this->model->where('id', $id)->update($updateData);
    }

    public function findManyBy(array $criteria): Collection
    {
        return $this->model->where($criteria)->get();
    }
}
