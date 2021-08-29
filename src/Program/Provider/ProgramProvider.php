<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Program\Exception\ProgramNotFoundException;
use App\Program\Repository\ProgramRepository;

class ProgramProvider extends AbstractProvider
{
    public function __construct(ProgramRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function throwNotFoundException()
    {
        throw new ProgramNotFoundException();
    }

    protected function getSearchableFields(): array
    {
        return [
            'name' => 'text',
            'level' => 'text',
        ];
    }
}
