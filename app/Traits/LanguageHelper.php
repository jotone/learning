<?php

namespace App\Traits;

use App\Classes\FileHelper;

trait LanguageHelper
{
    /**
     * Translation file row
     *
     * @param array $locale_data
     * @param string $data
     * @param string $row
     * @param string $key
     * @return string
     */
    protected function translationRow(array $locale_data, string $data, string $row, string $key = ''): string
    {
        $val = isset($locale_data[$row]) ? $this->lowercaseAttr($locale_data[$row]) : $data;

        return sprintf("    '%s' => '%s',\n",
            empty($key) ? $row : $key,
            preg_replace('/\'/', '&apos;', $val)
        );
    }

    /**
     * Lowercase :attribute entry
     * @param string $str
     * @return string
     */
    protected function lowercaseAttr(string $str): string
    {
        return preg_replace_callback('/:[a-zA-Z]+/', fn($matches) => strtolower($matches[0]), $str);
    }

    /**
     * Write translations into the folder
     *
     * @param array|null $source
     * @param array $locale
     * @param string $lang
     * @param bool $ignore_key
     * @return void
     */
    protected function writeTranslationsToFiles(?array $source, array $locale, string $lang, bool $ignore_key = false): void
    {
        foreach ($source as $file_name => $content) {
            $result = '';
            foreach ($content as $row => $data) {
                if (is_array($data)) {
                    // Nested translations array
                    $result .= "    '$row' => [\n"; // Open array brackets
                    // Insert nested key => value pairs
                    foreach ($data as $key => $value) {
                        // Inner array translation value
                        $result .= '    ' . (
                            $ignore_key
                                ? $this->translationRow($locale, $value, $key)
                                : $this->translationRow($locale, $value, "$row.$key", $key)
                            );
                    }
                    $result .= "    ],\n"; // Close array brackets
                } else {
                    // Translation key => value pair
                    $result .= $this->translationRow($locale, $data, $row);
                }
            }
            $folder = FileHelper::createFolder(lang_path($lang));

            file_put_contents($folder . '/' . $file_name . '.php', "<?php\n\nreturn [\n$result];");
        }
    }
}