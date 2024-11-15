<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Contracts\Cover;

class Image extends File implements Cover
{
    use PresentsImages;

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = true;

    const ASPECT_AUTO = 'aspect-auto';

    const ASPECT_SQUARE = 'aspect-square';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $disk
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, object, string, string, ?string, ?string):(mixed))|null  $storageCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $disk = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->acceptedTypes('image/*');

        $this->thumbnail(function () {
            return !$this->value || Str::startsWith($this->value, 'http')
                ? $this->value
                : Storage::disk($this->getStorageDisk())->url($this->value);
        })->preview(function () {
            return !$this->value || Str::startsWith($this->value, 'http')
                ? $this->value
                : Storage::disk($this->getStorageDisk())->url($this->value);
        });
    }

    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), $this->imageAttributes());
    }
}
