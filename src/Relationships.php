<?php

namespace Laratrans;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait Relationships
{
    /**
     * Get the translation associated with given model.
     *
     * @return HasOne
     */
    public function translation(): HasOne
    {
        return $this->hasOne($this->getTranslationModelName())
            ->where($this->getLocaleKey(), $this->locale());
    }

    /**
     * Get the translations for given model.
     *
     * @return HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany($this->getTranslationModelName());
    }

    /**
     * Get name of translation model.
     *
     * @return string
     */
    public function getTranslationModelName(): string
    {
        return rtrim(app('locale')->translationNamespace(), '\\')
            . '\\' . class_basename(get_called_class()) . 'Translation';
    }

    /**
     * Get the translation foreign key for given model.
     *
     * @return string
     */
    public function getTranslationForeignKey(): string
    {
        return $this->translationForeignKey ?? $this->getForeignKey();
    }
}
