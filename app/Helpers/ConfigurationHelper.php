<?php
namespace App\Helpers\ConfigurationHelper;

use Illuminate\Support\Facades\DB;
  
class Configuration {
    /**
     * @param int $configuration_id Configuration-id
     * 
     * @return string
     */
    public static function get_configuration_infos($configuration_id) {
        $configuration = DB::table('configurations')->where('id', 1)->first();
         
        return (isset($configuration->id) ? $configuration : '');
    }
}

