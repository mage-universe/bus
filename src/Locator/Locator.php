<?php declare(strict_types=1);

namespace Mage\Bus\Locator;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use SplFileInfo;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

abstract class Locator
{
    public function __construct(
        private readonly array $paths,
        private readonly string $commandInterface,
        private readonly string $commandHandlerInterface
    ) {}

    public function mappings(): array
    {
        /** @psalm-var array<int, SplFileInfo> $files */
        $files = reduce(
            /** @psalm-param array<string, string> $path */
            function (array $acc, array $path): array {
                return [...$acc, ...$this->searchFiles($path)];
            },
            $this->paths,
            []
        );

        /** @psalm-var array<string, string|array<string>> */
        return reduce(function (array $acc, string $class): array {
            if (class_exists($class) && $this->classImplements($class, $this->commandHandlerInterface)) {
                $class = new ReflectionClass($class);
                $acc = $this->setMapping($class, $acc);
            }
            return $acc;
        }, $this->filesToClasses($files), []);
    }

    /** @psalm-param array<string, string> $pathOptions */
    private function searchFiles(array $pathOptions): array
    {
        /** @psalm-var array<int, SplFileInfo> */
        return reduce(function (array $acc, SplFileInfo $file) use ($pathOptions): array {
            if ($file->isFile() && $this->matchFiles($pathOptions['pattern'], $file)) {
                $acc[] = $file;
            }
            return $acc;
        }, new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathOptions['path'])), []);
    }

    private function matchFiles(string $pattern, SplFileInfo $file): bool
    {
        /** @infection-ignore-all */
        if ($pattern === '') {
            return false;
        }
        /** @infection-ignore-all */
        return preg_match($pattern, $file->getPathname()) === 1;
    }

    private function filesToClasses(array $files): array
    {
        /** @psalm-var array<int, string> */
        return reduce(function (array $acc, SplFileInfo $file): array {
            $src = $file->openFile()->fread($file->getSize());
            if (preg_match('#(namespace)(\\s+)([A-Za-z0-9\\\\]+?)(\\s*);#', $src, $matches)) {
                $acc[] = $matches[3] . '\\' . $file->getBasename('.php');
            }
            return $acc;
        }, $files, []);
    }

    private function classImplements(string $class, string $implements): bool
    {
        return in_array($implements, class_implements($class));
    }

    protected function setMapping(ReflectionClass $class, array $acc): array
    {
        $acc[$this->commandType($class)->getName()] = $class->getName();
        return $acc;
    }

    protected function commandType(ReflectionClass $class): ReflectionNamedType
    {
        /** @psalm-var ReflectionParameter|null $command */
        $command = search(function (ReflectionParameter $parameter): bool {
            /** @psalm-var ReflectionNamedType|null $parameterType */
            $parameterType = $parameter->getType();
            return !is_null($parameterType) &&
                $this->classImplements($parameterType->getName(), $this->commandInterface);
        }, $class->getMethod('__invoke')->getParameters());

        if (!$command) {
            throw new ReflectionException(
                "Method argument in $class->name::__invoke() implementing $this->commandInterface not detected"
            );
        }

        /** @psalm-var ReflectionNamedType */
        return $command->getType();
    }
}
