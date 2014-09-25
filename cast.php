<?php

namespace Forms {

    class Cast {

        private $source = array();
        private $variableList = array();

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

            foreach ($variableList as $variable=>$type) {
                if (is_array($type) && count($type) === 0) {
                    $type = 'array';
                } else if (is_array($type) && count($type) === 2){

                    if (!isset($source[$variable])){
                        $outputData[$type[0]] = $this->getDefaultValue('_cast' . ucfirst($type[1]));
                        continue;
                    } else {
                        $source[$type[0]] = $source[$variable];
                        unset($source[$variable]);

                        $variable = $type[0];
                        $type = $type[1];
                    }
                }

                $method = '_cast' . ucfirst($type);

                //check if is deffined 1st
                if (isset($source[$variable]) === false){
                    $outputData[$variable] = $this->getDefaultValue($method);
                } else {

                    if (method_exists($this, $method)){
                        $outputData[$variable] = $this->$method($source[$variable]);
                    }
                }
            }

            return $outputData;
        }

        private function getDefaultValue($method){

            if ($method === '_castArray'){
                return array();
            } else if ($method === '_castInt'){
                 return 0;
            } else if ($method === '_castString'){
                return '';
            } else if ($method === '_castDate'){
                return '';
            } else if ($method === '_castBool'){
                return 'false';
            } else if ($method === '_castFloat'){
                return 0.00;
            } else if ($method === '_castDateToMysql'){
                return '';
            } else if ($method === '_castForeignInt'){
                return null;
            } else if ($method === '_castDatePicker'){
                return null;
            } else if ($method === '_castText'){
                return '';
            }

            return '';
        }

        private function _castString($value) {
            $value = (string) $value;

            $replace = array(
                '"',
                "'",
                '\\'
            );

            return trim(stripcslashes(strip_tags(str_replace($replace,'',$value))));
        }

        private function _castInt($value) {
            return (int) $value;
        }

        private function _castFloat($value) {
            return (float) str_replace(',','.', $value);
        }

        private function _castArray($value) {

            if (is_array($value) && count($value) > 0){
                foreach ($value AS $k => $v){
                    $value[$k] = $this->_castInt($v);
                }
            }

            return $value;

        }

        private function _castForeignInt($value) {

            $value = $this->_castInt($value);

            if ($value == 0){
                $value = NULL;
            }
            return $value;

        }

        private function _castDate($value){

            $value = $this->_castString($value);

            $arr = explode('.', $value);

            if (count($arr) !== 3) { return ''; }

            $day = $arr[0];
            $month = $arr[1];
            $year = $arr[2];

            if ($day < 1 || $day > 31){ return ''; }
            if ($month < 1 || $month > 12){ return '';}

            return $value;
        }
        private function _castDateToMysql($value){

            $value = $this->_castDate($this->_castString($value));

            if ($value == ''){ return '';}

            $dt = new \DateTime($value);

            return $dt->format('Y-m-d');
        }

        private function _castBool($value){

            return (boolean) $value;
        }

        private function _castYear($value){
            $value = $this->_castInt($value);

            if ($value == 0){ return 0; }

            if (strlen($value) != 4 ){
                return 0;
            }

            if ($value < 1900){
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

        private function _castDatePicker($value){

            $dt = new \DateTime($value);

            return $dt->format('Y-m-d H:i:s');

        }

        private function _castText($value){

            return $value;

        }

    }

}


?>