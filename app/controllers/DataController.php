<?php
class DataController
{
    public static function getById($id, $array){
        $data = array_search($id, array_column($array, 'id'));
        return $array[$data];
    }
}
