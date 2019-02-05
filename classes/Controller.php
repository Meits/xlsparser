<?php

/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 05-Feb-19
 * Time: 13:06
 */
class Controller
{

    private function checkUniqueAlias($alias,$table)
    {

        $sql = "SELECT * FROM ". $table . " WHERE `url` = '".$alias."'";
        $stmt = $this->container['db']->query($sql);
        $result = $stmt->fetch();
        if($result) {
            return false;
        }
        return true;
    }

    protected  function transliterate($string) {
        $str = mb_strtolower($string, 'UTF-8');

        $leter_array = array(
            'a' => 'а',
            'b' => 'б',
            'v' => 'в',
            'g' => 'г,ґ',
            'd' => 'д',
            'e' => 'е,є,э',
            'jo' => 'ё',
            'zh' => 'ж',
            'z' => 'з',
            'i' => 'и,і',
            'ji' => 'ї',
            'j' => 'й',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'у',
            'f' => 'ф',
            'kh' => 'х',
            'ts' => 'ц',
            'ch' => 'ч',
            'sh' => 'ш',
            'shch' => 'щ',
            '' => 'ъ',
            'y' => 'ы',
            '' => 'ь',
            'yu' => 'ю',
            'ya' => 'я',
        );

        foreach($leter_array as $leter => $kyr) {
            $kyr = explode(',',$kyr);

            $str = str_replace($kyr,$leter, $str);

        }

        //  A-Za-z0-9-
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/','-',$str);
        $str = preg_replace( '/[^\p{L}\p{Nd}]+/u', '-', $str );
        $str = trim($str,'-');

        return $str;
    }

    public function getAlias($string, $table = 'content')
    {
        $alias = $this->transliterate($string);
        if($this->checkUniqueAlias($alias,$table)) {
            return $alias;
        }
        else {
            for ( $suffix = 2; !$this->checkUniqueAlias( $newAlias = $alias . '-' . $suffix, $table ); $suffix++ ) {}
            return $newAlias;
        }
    }

}