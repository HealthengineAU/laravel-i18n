<?php

namespace HealthEngine\I18n\Translator;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

final class LanguageLoader
{
    protected Filesystem $files;
    protected string $path;

    public function __construct(Filesystem $files, string $path)
    {
        $this->files = $files;
        $this->path = $path;
    }

    /**
     * @param string $lang
     * @param string[] $namespaces
     * @return array<string, string>
     */
    public function load(string $lang, array $namespaces): array
    {
        $data = [];

        foreach ($namespaces as $namespace) {
            $entries = $this->loadLanguageFile($lang, $namespace);

            foreach ($entries as $key => $value) {
                if (!isset($data[$key])) {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * @param string $lang
     * @param string $namespace
     * @return array<string, string>
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function loadLanguageFile(string $lang, string $namespace): array
    {
        $file = "{$this->path}/{$lang}/$namespace.json";

        if (!$this->files->exists($file)) {
            return [];
        }

        $decoded = json_decode($this->files->get($file), true);

        if (is_null($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Translation file [{$file}] contains an invalid JSON structure.");
        }

        return $decoded;
    }
}
