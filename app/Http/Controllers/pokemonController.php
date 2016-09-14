<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require __DIR__ . '../../../../vendor/autoload.php';

use App\Http\Requests;
use Auth;

use NicklasW\PkmGoApi\Authentication\Config\Config;
use NicklasW\PkmGoApi\Authentication\Factory\Factory;
use NicklasW\PkmGoApi\Kernels\ApplicationKernel;

class pokemonController extends Controller
{
    
    public function run(Request $request)
    {
        // EXAMPLE Authentication via Google user credentials
        $config = new Config();
        $config->setProvider(Factory::PROVIDER_GOOGLE);
        $config->setUser(Auth::User()->pokego_username);
        $config->setPassword(Auth::User()->pokego_password);
        // Create the authentication manager
        $manager = Factory::create($config);
        // Add a event listener,
        $manager->addListener(function ($event, $value) {
            if ($event === Manager::EVENT_ACCESS_TOKEN) {
                /** @var AccessToken $accessToken */
                $accessToken = $value;
                // Persist the access token in session storage, cache or whatever.
                // The persisted access token should be passed to the Authentication factory for authentication
            }
        });
        // Initialize the pokemon go application
        $application = new ApplicationKernel($manager);


        // Retrieve the pokemon go api instance
        $pokemonGoApi = $application->getPokemonGoApi();
        // Retrieve the profile
        $profile = $pokemonGoApi->getProfile();
        // Retrieve the profile data
        $profileData = $profile->getData();
        echo sprintf('The profile data: %s', print_r($profileData, true));
    }

}
