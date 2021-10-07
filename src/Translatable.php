<?php

namespace Laratrans;

use Illuminate\Database\Eloquent\Model;
use Laratrans\Exceptions\LocaleDoesNotExist;

trait Translatable
{
    use Relationships;
    use Attributes;

    /**
     * Get the translation for given model.
     *
     * @param string|null $locale
     * @return Model|null
     */
    public function translate(?string $locale = null): ?Model
    {
        return $this->getTranslation($locale);
    }

    /**
     * Get the translation for given model.
     *
     * @param string|null $locale
     * @return Model|null
     * @throws LocaleDoesNotExist
     */
    public function translateOrNew(?string $locale = null): ?Model
    {
        return $this->getTranslationOrNew($locale);
    }

    /**
     * Get the translation or default for given model.
     *
     * @param string|null $locale
     * @return Model|null
     */
    public function translateOrDefault(?string $locale = null): ?Model
    {
        return $this->getTranslation($locale, true);
    }

    /**
     * Get the locale key for given model.
     *
     * @return string
     */
    public function getLocaleKey(): string
    {
        return $this->localeKey ?? app('locale')->localeKey();
    }

    /**
     * Determine the translation exists.
     *
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(?string $locale = null): bool
    {
        return (bool) $this->getTranslation($locale);
    }

    /**
     * Delete the translations.
     *
     * @param mixed|null $locales
     * @return void
     */
    public function deleteTranslations(mixed $locales = null): void
    {
        $locales = is_array($locales) ? $locales : func_get_args();
        $translations = $this->translations();

        if (! is_null($locales))
            $translations->whereIn($this->getLocaleKey(), $locales);

        $translations->delete();
        $this->load('translations');
    }

    /**
     * @inheritDoc
     */
    protected static function booted()
    {
        static::saved(function(Model $model) {
            return $model->saveTranslations();
        });
    }

    /**
     * @return false|void
     */
    protected function saveTranslations()
    {
        if (! $this->relationLoaded('translations')) return false;

        foreach ($this->translations as $translation) {
            if ($translation->isDirty()) {
                if (! empty($connectionName = $this->getConnectionName())) {
                    $translation->setConnection($connectionName);
                }

                $translation->setAttribute($this->getTranslationForeignKey(), $this->getKey());
                $translation->save();
            }
        }
    }

    /**
     * Resolve the translation.
     *
     * @param $translation
     * @return Model
     */
    protected function resolveTranslation($translation): Model
    {
        $translation->fillable($this->getFillable());
        $translation->primaryKey = $this->getLocaleKey();
        $translation->incrementing = false;

        return $translation;
    }

    /**
     * Get the translation for given model.
     *
     * @param string|null $locale
     * @param bool $withFallback
     * @return Model|null
     */
    protected function getTranslation(?string $locale = null, bool $withFallback = false): ?Model
    {
        $locale = $locale ?: $this->locale();
        $fallbackLocale = config('app.fallback_locale');

        if ($translation = $this->getTranslationByLocaleKey($locale))
            return $this->resolveTranslation($translation);

        if ($withFallback && $fallbackLocale) {
            return $this->resolveTranslation($this->getTranslationByLocaleKey($fallbackLocale));
        }

        return null;
    }

    /**
     * Get the translation by locale key for given model.
     *
     * @param string $key
     * @return Model|null
     */
    protected function getTranslationByLocaleKey(string $key): ?Model
    {
        return $this->relationLoaded('translations')
            ? $this->translations->firstWhere($this->getLocaleKey(), $key)
            : $this->translation;
    }

    /**
     * Get a new translation for given model.
     *
     * @param string $locale
     * @return Model
     * @throws LocaleDoesNotExist
     */
    protected function getNewTranslation(string $locale): Model
    {
        $locale = $locale ?? $this->locale();

        if (! $this->isLocaleExists($locale))
            throw new LocaleDoesNotExist('The locale not support');

        $translation = new ($this->getTranslationModelName());
        $translation->setAttribute(config('translatable.locale_key'), $locale);
        $this->resolveTranslation($translation);

        $this->translations->add($translation);

        return $translation;
    }

    /**
     * Determine the local exist.
     *
     * @param string $locale
     * @return bool
     */
    protected function isLocaleExists(string $locale): bool
    {
        return app('locale')->has($locale);
    }

    /**
     * Get the translation or create a new if not exists for given model.
     *
     * @param string|null $locale
     * @return Model
     * @throws LocaleDoesNotExist
     */
    protected function getTranslationOrNew(?string $locale = null): Model
    {
        return $this->getTranslation($locale) ?? $this->getNewTranslation($locale);
    }

    /**
     * Get current locale.
     *
     * @return string
     */
    protected function locale(): string
    {
        return app('locale')->current();
    }
}
