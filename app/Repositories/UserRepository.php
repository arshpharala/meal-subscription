<?php

namespace App\Repositories;

use App\Models\User;
use App\Services\StripeService;
use Prettus\Repository\Eloquent\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Create.
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        $object = parent::create($attributes);

        (new StripeService())->ensureStripeCustomer($object);

        $object->refresh();

        return $object;
    }

    /**
     * Update.
     *
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $object = parent::update($attributes, $id);

        return $object;
    }

    /**
     * Delete.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id)
    {
        if ($this->model->count() == 1) {
            return false;
        }

        if ($this->model->destroy($id)) {

            return true;
        }

        return false;
    }
}
