<?php

declare(strict_types=1);

namespace Swow\Docker;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Dockerfile
{
    protected string $basePath;
    protected string $phpVersion;

    public function __construct(string $phpVersion)
    {
        $this->setBasePath(dirname(__DIR__))->setPhpVersion($phpVersion);
    }

    public function render(): void
    {
        foreach (['fpm', 'cli', 'alpine'] as $type) {
            $this->generateDockerFile($type, 'Dockerfile.twig', true);
        }
    }

    public function generateDockerFile(string $type, string $template, bool $save = false): string
    {
        $dockerFile = (new Environment(new FilesystemLoader($this->getBasePath())))
            ->load($template)
            ->render($this->getContext($type));

        if ($save) {
            $dockerFileDir = $this->getDockerFileDir($type);
            if (!file_exists($dockerFileDir)) {
                mkdir($dockerFileDir, 0777, true);
            }

            file_put_contents("{$dockerFileDir}/Dockerfile", $dockerFile);
        }

        return $dockerFile;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): self
    {
        if (!is_dir($basePath) || !is_readable($basePath)) {
            throw new Exception("base path '{$basePath}' does not point to a directory or not readable");
        }

        $this->basePath = $basePath;

        return $this;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function setPhpVersion(string $phpVersion): self
    {
        $this->phpVersion = $phpVersion;

        return $this;
    }

    protected function getPhpMajorVersion(): string
    {
        return preg_replace('/^(\d+\.\d+).*$/', '$1', $this->getPhpVersion());
    }

    protected function getDockerFileDir(string $type): string
    {
        return sprintf("%s/dockerfiles/%s/php%s", $this->getBasePath(), $type, $this->getPhpMajorVersion());
    }

    protected function getContext(string $type): array
    {
        return [
            'base_image' => "php:{$this->getPhpVersion()}-${type}",
        ];
    }
}
