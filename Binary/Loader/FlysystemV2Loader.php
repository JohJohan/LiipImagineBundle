<?php

/*
 * This file is part of the `liip/LiipImagineBundle` project.
 *
 * (c) https://github.com/liip/LiipImagineBundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Liip\ImagineBundle\Binary\Loader;

use League\Flysystem\FilesystemOperator;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\Mime\MimeTypesInterface;

class FlysystemV2Loader implements LoaderInterface
{
    /**
     * @var FilesystemOperator
     */
    protected $filesystem;

    /**
     * @var MimeTypesInterface
     */
    protected $extensionGuesser;

    public function __construct(
        MimeTypesInterface $extensionGuesser,
        FilesystemOperator $filesystem
    ) {
        $this->extensionGuesser = $extensionGuesser;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function find($path)
    {
        if (false === $this->filesystem->fileExists($path)) {
            throw new NotLoadableException(sprintf('Source image "%s" not found.', $path));
        }

        $mimeType = $this->filesystem->mimeType($path);

        $extension = $this->getExtension($mimeType);

        return new Binary(
            $this->filesystem->read($path),
            $mimeType,
            $extension
        );
    }

    private function getExtension(?string $mimeType): ?string
    {
        return $this->extensionGuesser->getExtensions($mimeType)[0] ?? null;
    }
}
