<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PandaScoreAPI\PandaScoreAPI;

/**
 * Class PandascoreApiProvider
 *
 * @package App\Providers
 */
class PandascoreApiProvider extends ServiceProvider
{
    /** @var bool */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PandaScoreAPI::class, function () {
            return new PandaScoreAPI([
                PandaScoreAPI::SET_TOKEN       => env('PANDASCORE_TOKEN'),
                PandaScoreAPI::SET_API_BASEURL => 'api.pandascore.co/csgo',
            ]);
        });
    }

    public function provides()
    {
        return [PandaScoreAPI::class];
    }
}
