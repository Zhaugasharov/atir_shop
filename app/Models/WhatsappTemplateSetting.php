<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplateSetting extends Model
{
    protected $table = 'whatsapp_template_settings';

    protected $fillable = ['key', 'value'];

    public const KEY_DEFAULT_TEMPLATE_NAME = 'default_template_name';
    public const KEY_DEFAULT_TEMPLATE_LANGUAGE = 'default_template_language';

    public static function get(string $key, ?string $default = null): ?string
    {
        $row = self::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getDefaultTemplate(): ?array
    {
        $name = self::get(self::KEY_DEFAULT_TEMPLATE_NAME);
        $language = self::get(self::KEY_DEFAULT_TEMPLATE_LANGUAGE, 'ru');

        if (empty($name)) {
            return null;
        }

        return [
            'name' => $name,
            'language' => $language,
        ];
    }

    public static function setDefaultTemplate(string $name, string $language): void
    {
        self::set(self::KEY_DEFAULT_TEMPLATE_NAME, $name);
        self::set(self::KEY_DEFAULT_TEMPLATE_LANGUAGE, $language);
    }
}
