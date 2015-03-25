<?php namespace webcitron\Subframe;

use webcitron\Subframe\Db;

class Model {
    
    
    public function isEmpty() {
        return empty($this->id);
    }
    
    public static function get ($mulKey, $strValue = '', $arrFields = array()) {
        $objDb = Db::getInstance();
        $arrCallerClassTokens = explode('\\', get_called_class());
        $strCallerClass = array_pop($arrCallerClassTokens);
        $strQ = sprintf("SELECT %s FROM %s WHERE %s = :search_value", join(', ', $arrFields), strtolower($strCallerClass), $mulKey);
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':search_value' => $strValue
        ));
        $arrResult = $objSth->fetch();
        return $arrResult;
//        $objResult = new $strCallerClass();
//        if ($arrResult === false) {
//            return $objResult;
//        }
//        $objResult->fill($arrResult);
    }
    
//    public function () {
//        
//    }
    
//    public function fill($arrArray) {
////        get_called_class()
//        echo '<pre>';
//        print_r($arrArray);
//        exit();
//    }
    
//    public function modelsCollection($arrArray) {
//        
//        $arrResult = array();
//        foreach ($arrArray as $arrRow) {
//            $objRow = new $this->strChildClassName();
//            foreach ($arrRow as $strKey => $strValue) {
//                $objRow->{$strKey} = $strValue;
//            }
//            $arrResult[] = $objRow;
//        }
//        return $arrResult;
//    }
    
}