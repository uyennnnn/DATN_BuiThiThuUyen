<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Option extends Model
{
    use HasFactory;

    protected $table = 'configs';

    protected $fillable = [
        'key',
        'value',
    ];

    public function initData($data)
    {
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $this->where('key', $key)->delete();

                continue;
            }

            $this->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public static function getConfig()
    {
        return Option::pluck('value', 'key');
    }

    public static function get($key, $defaultValue = null, $domain = null)
    {
        $keyCache = $domain ? $domain.'_'.$key : request()->getHost().'_'.$key;
        if (Cache::has($keyCache)) {
            return Cache::get($keyCache);
        }
        $config = Option::where('key', $key)->first();
        if (! $config) {
            return $defaultValue;
        }
        Cache::put($keyCache, $config->value);

        return $config->value;
    }
}
