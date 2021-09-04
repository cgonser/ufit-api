<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Program\Exception\ProgramNotFoundException;
use App\Program\Repository\ProgramRepository;

class ProgramProvider extends AbstractProvider
{
    public function __construct(ProgramRepository $programRepository)
    {
        $this->repository = $programRepository;
    }

    protected function throwNotFoundException()
    {
        throw new ProgramNotFoundException();
    }

    /**
     * @return array<string, string>
     */
    protected function getSearchableFields(): array
    {
        return [
            'name' => 'text',
            'level' => 'text',
        ];
    }
}
