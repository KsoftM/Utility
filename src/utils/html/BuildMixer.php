<?php

namespace ksoftm\utils\html;

use ksoftm\utils\io\FileManager;

class BuildMixer extends Mixer
{
    /** @var string EXTENSION BLADE MIXER EXTENSION. */
    protected const EXTENSION = '.mix.php';

    public static function build(
        string $resPath,
        string $path,
        array $compactData = [],
        array $languageData = []
    ): string|false {
        $childFile = new FileManager($path);

        if (!$childFile->isExist()) {
            return false;
        }

        // read all data using the [path] 
        $childFile = $childFile->requireOnce();

        // remove comments in the html documents
        $childFile = parent::commentTag($childFile);

        // check the extend tag, if the document haven't extend tag then remove the yields
        /** @var MixResult */
        $childExtend = parent::extend($childFile);

        if ($childExtend instanceof MixResult) {

            $parentFile = new FileManager(
                $resPath . DIRECTORY_SEPARATOR . str_replace(
                    '.',
                    DIRECTORY_SEPARATOR,
                    $childExtend->getName()
                ) . BuildMixer::EXTENSION
            );

            if ($parentFile->isExist()) {
                $parentFile = $parentFile->requireOnce();

                $parentFile = self::renderYield(
                    parent::yield($parentFile),
                    parent::section($childExtend->getData()),
                    $parentFile,
                    true
                );

                if (!empty($compactData)) {
                    $parentFile = self::renderVar(
                        parent::var($parentFile),
                        $parentFile,
                        $compactData
                    );
                }

                if (!empty($languageData)) {
                    $lang = parent::lang($parentFile);

                    if (is_array($lang)) {
                        $parentFile = self::renderLang(
                            $lang[0],
                            $lang[1],
                            $parentFile,
                            $languageData
                        );
                    }
                }
            }
            $childExtend = $parentFile;
        }

        return $childExtend;
    }

    public static function renderYield(array $parentYield, array $childSection, string $parentFile, bool $removeUnused = false): string
    {
        if (!empty($parentYield) && !empty($childSection)) {
            $yieldedData = [];

            // get yield content data
            foreach ($parentYield as $pValue) {
                foreach ($childSection as $cValue) {
                    if ($pValue instanceof MixResult && $cValue instanceof MixResult) {
                        if ($pValue->getName() === $cValue->getName()) {
                            $yieldedData[] = [$pValue->getTemplate(), $cValue->getData()];
                        }
                    }
                }
            }

            // replace yield content
            foreach ($yieldedData as $yield) {
                $parentFile = str_replace($yield[0], $yield[1], $parentFile);
            }

            if ($removeUnused) {
                // empty the unused yield content
                foreach ($parentYield as $pYield) {
                    if ($pYield instanceof MixResult) {

                        foreach ($yieldedData as $cYield) {
                            if ($pYield->getTemplate() != $cYield[0]) {
                                $parentFile = str_replace(
                                    $pYield->getTemplate(),
                                    '<!-- not available -->',
                                    $parentFile
                                );
                            }
                        }
                    }
                }
            }
        }
        return $parentFile;
    }

    public static function renderVar(array $parentVars, string $parentFile, array $compactData): string
    {
        foreach ($parentVars as $variables) {
            if ($variables instanceof MixResult) {
                if (
                    is_string($variables->getName()) && strpos($variables->getName(), '.')
                ) {

                    $var = explode('.', $variables->getName());

                    $v = $compactData;
                    foreach ($var as $value) {
                        if (array_key_exists($value, !is_array($v) ? [] : $v)) {
                            $v = $v[$value];
                        } else {
                            $v = '<!-- not available -->';
                        }
                    }
                    $parentFile = str_replace(
                        $variables->getTemplate(),
                        $v,
                        $parentFile
                    );
                }
                if (
                    array_key_exists($variables->getName(), $compactData) &&
                    filter_var($compactData[$variables->getName()], FILTER_SANITIZE_STRING)
                ) {
                    $parentFile = str_replace(
                        $variables->getTemplate(),
                        $compactData[$variables->getName()],
                        $parentFile
                    );
                } else {
                    $parentFile = str_replace(
                        $variables->getTemplate(),
                        '<!-- not available -->',
                        $parentFile
                    );
                }
            }
        }

        return $parentFile;
    }

    public static function renderLang(string $lang, array $parentLang, string $parentFile, array $languageData): string
    {
        foreach ($parentLang as $variables) {
            if ($variables instanceof MixResult) {
                if (
                    is_string($variables->getSrc()) && strpos($variables->getSrc(), '.')
                ) {
                    $var = explode('.', $variables->getSrc());

                    $v = $languageData;
                    foreach ($var as $value) {
                        if (array_key_exists($value, $v)) {
                            $v = $v[$value];
                        }
                    }

                    $parentFile = str_replace(
                        $variables->getTemplate(),
                        $v,
                        $parentFile
                    );
                } else {
                    if (array_key_exists($variables->getSrc(), $languageData[$lang])) {
                        $parentFile = str_replace(
                            $variables->getTemplate(),
                            $languageData[$lang][$variables->getSrc()],
                            $parentFile
                        );
                    } else {
                        $parentFile = str_replace(
                            $variables->getTemplate(),
                            '<!-- not available -->',
                            $parentFile
                        );
                    }
                }
            }
        }

        return $parentFile;
    }
}
