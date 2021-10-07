<?php

namespace Laratrans;

class Locale
{
    /**
     * Get all available locales.
     *
     * @return array
     */
    public function all(): array
    {
        return config('translatable.locales', []);
    }

    /**
     * Get current locale.
     *
     * @return string
     */
    public function current(): string
    {
        return app()->getLocale();
    }

    /**
     * Determine the locale exist.
     *
     * @param string $locale
     * @return bool
     */
    public function has(string $locale): bool
    {
        return in_array($locale, $this->all());
    }

    /**
     * Add a new locale.
     *
     * @param mixed $locales
     * @return void
     */
    public function add(mixed $locales): void
    {
        $locales = is_array($locales) ?: func_get_args();

        config(['translatable.locales' => array_unique(array_merge($this->all(), $locales))]);
    }

    /**
     * Forget the given locale.
     *
     * @param mixed $locales
     * @return void
     */
    public function forget(mixed $locales): void
    {
        $locales = is_array($locales) ?: func_get_args();

        config(['translatable.locales' => array_values(array_diff($this->all(), $locales))]);
    }

    /**
     * Get the locale key.
     *
     * @return string
     */
    public function localeKey(): string
    {
        return config('translatable.locale_key', 'locale');
    }

    /**
     * Determine autoload translations.
     *
     * @return bool
     */
    public function hasAutoloadTranslations(): bool
    {
        return config('translatable.autoload_translations', false);
    }
}
