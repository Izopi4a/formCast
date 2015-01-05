<?php

namespace Forms {

    class Layer {

        private $source = array();
        private $variableList = array();
        private $min = false;
        private $max = false;
        private $default = false;

        public function __construct(array $source , array $variableList) {
            $this->variableList = $variableList;
            $this->source = $source;
        }

        public function getData() {
            return $this->_getData(
                $this->variableList,
                $this->source
            );
        }

        private function _getData($variableList, $source) {

            $outputData = array();

            foreach ($variableList as $variable => $type) {

                $this->min = false;
                $this->max = false;
                $this->default = false;

                if (is_array($type)){

                    if (isset($type['name'])){

                        $source[$type['name']] = $source[$variable];
                        unset($source[$variable]);
                        $variable = $type['name'];
                    }

                    if (isset($type['limit'])){
                        $this->min = $type['limit'][0];
                        $this->max = $type['limit'][1];
                    }

                    if (array_key_exists('default', $type)){
                        $this->default = $type['default'];
                    }

                    $method = '_cast' . ucfirst($type['type']);

                    if (!isset($source[$variable])){
                        $outputData[$variable] = $this->getDefaultValue($method);
                        continue;
                    }
                } else {
                    $method = '_cast' . ucfirst($type);
                }

                //check if is deffined 1st
                if (isset($source[$variable]) === false){
                    $outputData[$variable] = $this->getDefaultValue($method);
                } else {

                    if (method_exists($this, $method)){
                        $outputData[$variable] = $this->$method($source[$variable]);
                    } else {
                        throw new \Exception('unsupported form cast :'. $method);
                    }
                }
            }

            return $outputData;
        }

        private function getDefaultValue($method){

            if ($this->default !== false){
                return $this->default;
            }

            if ($method === '_castArray'){
                return array();
            } else if ($method === '_castInt'){
                 return 0;
            } else if ($method === '_castString'){
                return '';
            } else if ($method === '_castDate'){
                return 0;
            } else if ($method === '_castYear'){
                return 0;
            } else if ($method === '_castBool'){
                return 'false';
            } else if ($method === '_castFloat'){
                return 0.00;
            } else if ($method === '_castDateToMysql'){
                return '';
            } else if ($method === '_castEmail'){
                return '';
            } else if ($method === '_castForeignInt'){
                return null;
            } else if ($method === '_castFormatedInt'){
                return 0;
            } else if ($method === '_castTimeAddFullRepair'){
                return NULL;
            }else if ($method === '_castArrayInt'){
                return array();
            }

            return '';
        }

        private function _castString($value) {

            if ($this->min !== false && $this->min > strlen($value)){
                return '';
            }

            if ($this->max !== false && $this->max < strlen($value)){
                return '';
            }

            $value = (string) $value;

            $replace = array(
                '"',
                "'",
                '\\'
            );

            $value = trim(stripcslashes(strip_tags(str_replace($replace,'',$value))));

            if ($this->default !== false && $value === ''){
                return $this->default;
            }

            return $value;
        }

        private function _castInt($value) {

            $value = intval($value);

            if ($this->min !== false && $this->min > $value){
                return ($this->default !== false ? $this->default : 0 );
            }
            if ($this->max !== false && $this->max < $value){
                return ($this->default !== false ? $this->default : 0 );
            }

            return $value;
        }

        private function _castFormatedInt($value) {

            $value = str_replace("' ", '', $value);

            return $this->_castInt($value);
        }

        private function _castFloat($value) {

            $value = floatval(str_replace(',','.', $value));

            if ($value === 0.00 && $this->default !== false){
                return $this->default;
            }

            if ($this->min !== false && $this->min > $value){
                return ($this->default !== false ? $this->default : 0 );
            }
            if ($this->max !== false && $this->max < $value){
                return ($this->default !== false ? $this->default : 0 );
            }

            return $value;
        }

        private function _castArrayInt($value) {

            if (is_array($value) && count($value) > 0){
                foreach ($value AS $k => $v){
                    $value[$k] = $this->_castInt($v);
                }
            }

            return $value;

        }

        private function _castArrayString($value) {

            if (is_array($value) && count($value) > 0){
                foreach ($value AS $k => $v){
                    $value[$k] = $this->_castString($v);
                }
            }

            return $value;

        }

        private function _castDate($value){

            $value = $this->_castString($value);

            $arr = explode('.', $value);
            if (count($arr) !== 3) { return 0; }

            $dt = new \DateTime($value);

            return $dt->getTimestamp();
        }

        private function _castDateToMysql($value){

            $value = $this->_castDate($this->_castString($value));

            if ($value == ''){ return '';}

            $dt = new \DateTime($value);

            return $dt->format('Y-m-d');
        }

        private function _castBool($value){

            return boolval($value);
        }

        private function _castYear($value){
            $value = $this->_castInt($value);

            if ($value == 0){ return 0; }

            if (strlen($value) != 4 ){
                return 0;
            }

            if ($value < 1950 || $value > (date('Y') + 10)){
                return 0;
            }

            return $value;

        }

        private function _castArrayDates($value){

            $dates = array();

            if (!is_array($value) || count($value) === 0){
                return $dates;
            }

            foreach ($value AS $k => $v){
                array_push($dates, $this->_castDate($v));
            }

            return $dates;

        }

        private function _castEmail($value){

            $value = $this->_castString($value);

            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                return '';
            } else {
                return $value;
            }
        }

        private function _castForeignInt($value){

            $value = $this->_castInt($value);

            if ($value === 0){
                return NULL;
            }
            return $value;
        }

        private function _castPassword($value){
            return $value;
        }

    }

}


?>