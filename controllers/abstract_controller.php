<?php

abstract class AbstractController {
    
    public function __construct(){

    }
    
    protected function loadModel($model) {
        $model_path= ROOT_DIR . "/models/{$model}.php";
        if(file_exists($model_path)) {
            require($model_path);
            
            $modelName= str_replace("_", "", $model)."Model";
            return new $modelName(App::db());
        } else {
            raise_error(404, "Requested process[{$model}] not found", 404);
        }
    }
}