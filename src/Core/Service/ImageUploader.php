<?php

declare(strict_types=1);

namespace App\Core\Service;

use App\Core\Exception\ImageMaxDimensionsExceededException;
use App\Core\Exception\ImageMaxSizeExceededException;

class ImageUploader
{
    protected const MAX_MEMORY = 400;
    protected ?string $initialMemory = null;

    protected function validateImageSize($width, $height): void
    {
        $maxDimension = $this->calculateMaxDimensions();

        if ($width * $height > ($maxDimension ** 2)) {
            throw new ImageMaxDimensionsExceededException($maxDimension, $maxDimension);
        }
    }

    protected function calculateMaxDimensions(): int
    {
        $memoryUsed = max(round(memory_get_usage(true) / 1048576, 2) * 1.2, ((int) $this->initialMemory) * 0.75);

        $memoryAvailable = self::MAX_MEMORY - $memoryUsed;

        $maxTotalDimension = $memoryAvailable * 1048576 - 1048576;
        $maxTotalDimension /= 6;

        return floor(sqrt($maxTotalDimension));
    }

    protected function allocateMemory($width, $height): void
    {
        $this->initialMemory = ini_get('memory_limit');
        set_time_limit(50);

        $memoryUsed = max(round(memory_get_usage(true) / 1048576, 2) * 1.2, ((int) $this->initialMemory) * 0.75);

        $memorySize = $memoryUsed + floor(($width * $height * 4 * 1.5 + 1048576) / 1048576);
        $memorySize = max((int) $this->initialMemory, $memorySize);

        if ($memorySize > self::MAX_MEMORY) {
            throw new ImageMaxSizeExceededException();
        }

        ini_set('memory_limit', $memorySize.'M');
    }

    protected function resetMemoryAllocation(): void
    {
        if (null !== $this->initialMemory) {
            ini_set('memory_limit', $this->initialMemory);
        }
    }
}
