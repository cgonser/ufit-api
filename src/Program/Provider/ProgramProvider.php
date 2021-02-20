<?php

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Program\Entity\Program;
use App\Program\Exception\ProgramNotFoundException;
use App\Program\Repository\ProgramRepository;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

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
