<?php
/**
 *
 * @package     Common
 *
 * @subpackage  Translator
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2024 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    common classes
 *
 * @since       2024-02-01
 *
 */

namespace overbyte\common;

/**
 * Class Translator
 *
 * The Translator class is responsible for translating strings based on the specified locale and fallback locale.
 */
class Translator
{
    /**
     * The locale used by the Translator.
     *
     * @var string
     */
    protected $locale;
    /**
     * The fallback value for the Translator.
     *
     * @var mixed
     */
    protected $fallback;
    /**
     * @var array $translations The array that holds the translations.
     */
    protected $translations = [];

    /**
     * Translator constructor.
     *
     * @param Fileloader $translations An associative array of translations.
     * @param string     $file         The file to load the translations from.
     * @param string     $locale       The locale to use for translation.
     * @param string     $fallback     The fallback locale to use if the translation for the specified locale is not available.
     *
     * @return void
     */
    public function __construct(Fileloader $translations, $file, $locale, $fallback = 'en')
    {
        $this->translations[$locale]   = $translations->load($locale, $file, 'language');
        $this->translations[$fallback] = $translations->load($fallback, $file, 'language');
        $this->locale                  = $locale;
        $this->fallback                = $fallback;
    }

    /**
     * Get the translated string for the specified key.
     *
     * @param string      $key     The key of the translation.
     * @param array       $replace An associative array of replacements to be made in the translated string.
     * @param string|null $locale  The locale to use for translation. If not specified, the default locale will be used.
     *
     * @return string The translated string.
     */
    public function get($key, array $replace = [], $locale = null): string
    {
        $locale = $locale ?: $this->locale;

        $line = $this->getLine($key, $locale);

        if ($line === null) {
            $line = $this->getLine($key, $this->fallback);
        }

        if ($line === null) {
            return $key;
        }

        return $this->makeReplacements($line, $replace);
    }

    /**
     * Get the translation line for the specified key and locale.
     *
     * @param string $key    The key of the translation.
     * @param string $locale The locale to use for translation.
     *
     * @return string|null The translation line if found, null otherwise.
     */
    protected function getLine($key, $locale): ?string
    {
        return $this->translations[$locale][$key] ?? null;
    }

    /**
     * Make replacements in the translated string.
     *
     * @param string $line    The translated string.
     * @param array  $replace An associative array of replacements to be made in the translated string.
     *
     * @return string The translated string with replacements made.
     */
    protected function makeReplacements($line, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $line = str_replace('{{' . $key . '}}', $value, $line ?? '');
        }

        return $line;
    }

    /**
     * Set the translations for the specified locale.
     *
     * @param string $locale       The locale for which translations are being set.
     * @param array  $translations An associative array of translations for the specified locale.
     *
     * @return void
     */
    public function setTranslations($locale, array $translations): void
    {
        $this->translations[$locale] = $translations;
    }

    /**
     * Get the locale used by the Translator.
     *
     * @return string The locale used by the Translator.
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Translates a given key into the specified locale.
     *
     * @param string      $key     The key to be translated.
     * @param array       $replace An array of values to replace placeholders in the translation.
     * @param string|null $locale  The locale to translate the key into. If null, the default locale will be used.
     *
     * @return string The translated string.
     */
    public function translate($key, array $replace = [], $locale = null): string
    {
        return $this->capitalize($this->get($key, $replace, $locale));
    }

    /**
     * Capitalizes the given text.
     *
     * @param string $text The text to be capitalized.
     *
     * @return string The capitalized text.
     */
    private function capitalize($text): string
    {
    // Capitalize the first letter of each sentence
        $text = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
            return strtoupper($matches[1] . ' ' . $matches[2]);
        }, ucfirst(strtolower($text)));

    // Capitalize proper nouns (assuming they are always followed by a space or end of line)
        $properNouns = [
        'com', 'net', 'org', 'gov', 'edu', 'io',
        'php', 'html', 'css', 'js', 'json', 'xml', 'yaml', 'yml', 'sql', 'php', 'java', 'c', 'cpp', 'cs', 'py', 'rb', 'go', 'kt', 'ts', 'sh', 'bash', 'bat', 'cmd',
        ];
        foreach ($properNouns as $noun) {
            $text = preg_replace_callback('/\b' . $noun . '\b/', function ($matches) {
                return ucfirst($matches[0]);
            }, $text);
        }

        return $text;
    }
}
