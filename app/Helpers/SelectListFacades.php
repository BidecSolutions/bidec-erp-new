<?php
    namespace App\Helpers;
    use Illuminate\Support\Facades\Facade;
    class SelectListFacades extends Facade{
        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor(){
            
            return new SelectListHelper();
        }
    }
?>