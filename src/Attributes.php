<?php

namespace Laratrans;

use Illuminate\Support\Str;
use Laratrans\Exceptions\LocaleDoesNotExist;
use Illuminate\Support\Facades\Schema;

trait Attributes
{
    /**
     * @inheritDoc
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        $hiddenAttributes = $this->getHidden();

        if ($this->relationLoaded('translations'))
            $this->translations->makeHidden($hiddenAttributes);

        if ($this->relationLoaded('translations') || $this->relationLoaded('translation'))
            foreach ($this->getTranslatedAttributes() as $field) {
                if (in_array($field, $hiddenAttributes))
                    continue;

                $attributes[$field] = $this->getTranslatedAttribute($field);
            }

        $this->makeHidden('translation');

        return $attributes;
    }

    public function getTranslatedAttributes(): array
    {
        return $this->translatedAttributes;
    }

    /**
     * @inheritDoc
     * @throws LocaleDoesNotExist
     */
    public function setAttribute($key, $value)
    {
        [$attribute, $locale] = $this->resolveTranslatedAttribute($key);

        if ($this->isTranslatedAttribute($attribute)) {
            $this->getTranslationOrNew($locale)->$attribute = $value;

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @inheritDoc
     * @throws LocaleDoesNotExist
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $this->getTranslationOrNew($key)->fill($value);
                unset($attributes[$key]);
            } else {
                [$attribute, $locale] = $this->resolveTranslatedAttribute($key);

                if ($this->isTranslatedAttribute($attribute)) {
                    $this->getTranslationOrNew($locale)->fill([
                        $attribute => $value
                    ]);
                    unset($attributes[$key]);
                }
            }
        }

        return parent::fill($attributes);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key): mixed
    {
        [$attribute, $locale] = $this->resolveTranslatedAttribute($key);

        if ($this->isTranslatedAttribute($attribute)) {
            if ($this->getTranslation($locale) === null) {
                return $this->getAttributeValue($attribute);
            }

            if ($this->hasGetMutator($attribute)) {
                $this->attributes[$attribute] = $this->getTranslatedAttribute($attribute);

                return $this->getTranslatedAttribute($attribute);
            }

            return $this->getTranslatedAttribute($attribute);
        }

        return parent::getAttribute($key);
    }

    /**
     * Determine the attribute is translated.
     *
     * @param string $attribute
     * @return bool
     */
    public function isTranslatedAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->getTranslatedAttributes());
    }

    /**
     * Get translated attribute for given model.
     *
     * @param $attribute
     * @return mixed
     */
    protected function getTranslatedAttribute($attribute): mixed
    {
        return optional($this->getTranslation())->getAttribute($attribute);
    }

    /**
     * Resolve given translated attribute.
     *
     * @param string $key
     * @return array
     */
    protected function resolveTranslatedAttribute(string $key): array
    {
        if (Str::contains($key, ':'))
            return explode(':', $key);

        return [$key, $this->locale()];
    }
}
