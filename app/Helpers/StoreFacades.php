<?php
    namespace App\Helpers;
    use Illuminate\Support\Facades\Facade;
    class StoreFacades extends Facade{
        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor(){
            
            return new StoreHelper();
        }
    }
?>