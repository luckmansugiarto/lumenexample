<?php
namespace App\Services\Repositories;

use App\Contracts\Repositories\EloquentRepositoryInterface;
use App\Models\BaseModel as EloquentModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

abstract class EloquentRepository implements EloquentRepositoryInterface
{
    protected $attributesMapping = [];
    protected $model;

    public function __construct(EloquentModel $model)
    {
        $this->model = $model;
    }

    public function applyFilterById(int $id)
    {
        $this->applyFilters(['id' => $id]);

        return $this;
    }

    public function applyPagination($max = -1, $page = -1)
    {
        if ($max > 0)
        {
            $hasFilter = true;

            if ($page > 0)
            {
                $this->model = $this->model->skip(($page - 1) * $max);
            }

            $this->model = $this->model->take($max);
        }

        return $this;
    }

    public function applySorting($sortBy = [])
    {
        if (count($sortBy) > 0)
        {
            $hasFilter = true;

            foreach ($sortBy as $attribute => $direction)
            {
                $this->model = $this->model->orderBy($attribute, $direction);
            }
        }

        return $this;
    }

    public function delete(): bool
    {
        return $this->model->delete();
    }

    public function getRecords(array $attributes = []): Collection
    {
        return $this->model->get(count($attributes) > 0 ? $attributes : ['*']);
    }

    public function reset(): void
    {
        $this->model = App::make(get_class($this->model));
    }

    public function save(array $attributes)
    {
        try
        {
            return $this->model->create($attributes);
        }
        catch (\Exception $ex) {}

        return null;
    }

    public function update(array $attributes): bool
    {
        $updateData = [];
        $totalSuccessfulUpdates = 0;

        foreach ($attributes as $attribute)
       {
            if (isset($attribute['id']))
            {
                $model = $this->findById($attribute['id']);

                if (!is_null($model))
                {
                    if ($model->update(Arr::except($attribute, ['id'])))
                    {
                        $totalSuccessfulUpdates++;
                    }
                }
            }
        }

        return count($attributes) === $totalSuccessfulUpdates;
    }

    protected function applyFilters(array $attributes)
    {
        if (count($attributes) > 0)
        {
            $this->model = $this->model->where($attributes);
        }

        return $this;
    }
}