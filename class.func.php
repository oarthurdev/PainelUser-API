<?php
error_reporting(E_ALL);
class func
{
    public function strace($a)
    {
        $a = preg_replace("[àáâäã]","a",$a);
        $a = preg_replace("[èéêë]","e",$a);
        $a = preg_replace("[ìíîï]","i",$a);
        $a = preg_replace("[òóôöõ]","o",$a);
        $a = preg_replace("[ùúûü]","u",$a);
        $a = preg_replace("[ÀÁÂÄÃ]","A",$a);
        $a = preg_replace("[ÈÉÊË]","E",$a);
        $a = preg_replace("[ÌÍÎÏ]","I",$a);
        $a = preg_replace("[ÒÓÔÖÕ]","O",$a);
        $a = preg_replace("[ÙÚÛÜ]","U",$a);
        $a = preg_replace("/ç/","c",$a);
        $a = preg_replace("/Ç/","C",$a);
        $a = preg_replace("/ñ/","n",$a);
        $a = preg_replace("/Ñ/","N",$a);
        $a = str_replace(".","",$a);
        $a = str_replace("[","",$a);
        $a = str_replace("]","",$a);
        $a = str_replace("´","",$a);
        $a = str_replace("`","",$a);
        $a = str_replace("¨","",$a);
        $a = str_replace("^","",$a);
        $a = str_replace("~","",$a);
        $a = str_replace("'","",$a);
        $a = str_replace("-","",$a);
        $a = str_replace("/","",$a);
        $a = str_replace("|","",$a);
        $a = str_replace(" ","",$a);
        $a = str_replace("¹","",$a);
        $a = str_replace("²","",$a);
        $a = str_replace("³","",$a);
        $a = str_replace("£","",$a);
        $a = str_replace("¢","",$a);
        $a = str_replace("¬","",$a);
        $a = str_replace("°","",$a);
        $a = str_replace("ª","",$a);
        $a = str_replace(",","",$a);
        $a = str_replace(";","",$a);
        $a = str_replace(":","",$a);
        $a = str_replace("?","",$a);
        $a = str_replace("!","",$a);
        $a = str_replace("@","",$a);
        $a = str_replace("#","",$a);
        $a = str_replace("$","",$a);
        $a = str_replace("%","",$a);
        $a = str_replace("¨","",$a);
        $a = str_replace("&","",$a);
        $a = str_replace("*","",$a);
        $a = str_replace("(","",$a);
        $a = str_replace(")","",$a);
        $a = str_replace("_","",$a);
        $a = str_replace("+","",$a);
        $a = str_replace("§","",$a);
        $a = str_replace("=","",$a);
        $a = str_replace("^","",$a);
        $a = str_replace("~","",$a);
        $a = str_replace("´","",$a);
        $a = str_replace("'","",$a);
        $a = str_replace("`","",$a);
        $a = str_replace("<","",$a);
        $a = str_replace(">","",$a);
        $a = str_replace("|","",$a);
        return $a;
    }

    function verificaClan($chName)
    {
        $usuarioClan = "Personagem sem clan";
        require "include/configura.php";
        $connection = odbc_connect($connection_string,$user,$pass);
        $query = "SELECT * FROM [clandb].[dbo].[UL] WHERE [ChName]='$chName'";;
        $q = odbc_do($connection,$query);
        while(odbc_fetch_row($q)) {
            $usuarioClan = odbc_result($q,7);;
            break;
        }
        return $usuarioClan;    
    }

    function is_valid_string($string) {

        $cfgBadChars='`~!@#$%^&*()+-_=[]{};\'\\:"|,/<>? ';

        if (empty($string))
            return true;

        for ($i = 0; $i < strlen($cfgBadChars); $i++):
            if (strstr($string, $cfgBadChars[$i]))
            return true;
        endfor;
        
        return false;
    }

    # function filter co mot gia tri la $ojb cai ma ban se kiem tra
    function char_filter($ojb)
    {
        $badchars='Ý`~!@$%^()+-_=[]{}\'\\:"|,/<>? '; # Nhung ky tu khong cho phep duoc len danh sach tai day
        for ($i=0;$i<strlen($badchars);$i++){
            $ojb=str_replace($badchars[$i],"",$ojb);

        }

        return $ojb;
    }

    function getInt($o)
    {
        $err=false;

        for($i=0;$i<strlen($o);$i++)
            if(!eregi("^[0-9]",$o[$i])) $err=true;

        if($err)
        {
            ereg ('[^0-9]*([0-9]+)[^0-9]*', $o, $cutInt);
            $o=intval($cutInt[1]);
            if(!$o)
                return false;

            return $o;
        }

        return $o;

    }

    function checkSkillPoints($level,$kind)
    {
        $SP=intval(( ($level-10) /2))+1;

        if($level>=55) $SP=$SP+1;
        if($level>=70) $SP=$SP+1;
        if($level>=80) $SP=$SP+2;

        $EP=intval(( ($level-60) /2))+1;

        return ($kind=='SP')?$SP:$EP;
    }


    //dim pontosStatus : pontosStatus = 0
    //for l = 2 to level
    //    if (l <= 70) then
    //        pontosStatus = pontosStatus + 5
    //    elseif ((l>70) and (l<=90)) then
    //        pontosStatus = pontosStatus + 7
    //    else
    //        pontosStatus = pontosStatus + 10
    //    end if 
    //next


    function checkStatePoints($level)
    {
        $pontosStatus = 0;
        for($i=2;$i<=$level;$i++)
        {
            if($i <= 70) {
                $pontosStatus = $pontosStatus + 5;
            } else if(($i > 70) and ($i <= 90)) {
                $pontosStatus = $pontosStatus + 7;
            } else {
                $pontosStatus = $pontosStatus + 10;
            }

        }
        return $pontosStatus;
    }

    function char2Name($classNum)
    {
        switch ($classNum)
        {
            case "01":
                $charClassName="Fighter";
            break;

            case "02":
                $charClassName="Mechanician";
            break;

            case "03":
                $charClassName="Archer";
            break;

            case "04":
                $charClassName="Pikeman";
            break;

            case "05":
                $charClassName="Atalanta";
            break;

            case "06":
                $charClassName="Knight";
            break;

            case "07":
                $charClassName="Magician";
            break;

            case "08":
                $charClassName="Priestess";
            break;

            default:
                return false;

        }

        return $className;
    }


    function char2Num($className)
    {
        switch ($className)
        {
            case "Fighter":
                $classNum=1;
            break;

            case "Mechanician":
                $classNum=2;
            break;

            case "Archer":
                $classNum=3;
            break;

            case "Pikeman":
                $classNum=4;
            break;

            case "Atalanta":
                $classNum=5;
            break;

            case "Knight":
                $classNum=6;
            break;

            case "Magician":
                $classNum=7;
            break;

            case "Priestess":
                $classNum=8;
            break;

            default:
                return false;


        }

        return $classNum;
    }


    function charResetState($class)
    {
        $charStateStr = '';
        
        switch ($class)
        {
            case "Fighter":
                $charStr=pack('i', 28);
                $charSpi=pack('i', 6);
                $charTal=pack('i', 21);
                $charAgi=pack('i', 17);
                $charHea=pack('i', 27);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Mechanician":
                $charStr=pack('i', 24);
                $charSpi=pack('i', 8);
                $charTal=pack('i', 25);
                $charAgi=pack('i', 18);
                $charHea=pack('i', 24);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Archer":
                $charStr=pack('i', 17);
                $charSpi=pack('i', 11);
                $charTal=pack('i', 21);
                $charAgi=pack('i', 27);
                $charHea=pack('i', 23);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Pikeman":
                $charStr=pack('i', 26);
                $charSpi=pack('i', 9);
                $charTal=pack('i', 20);
                $charAgi=pack('i', 19);
                $charHea=pack('i', 25);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Atalanta":
                $charStr=pack('i', 23);
                $charSpi=pack('i', 15);
                $charTal=pack('i', 19);
                $charAgi=pack('i', 19);
                $charHea=pack('i', 23);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Knight":
                $charStr=pack('i', 26);
                $charSpi=pack('i', 13);
                $charTal=pack('i', 17);
                $charAgi=pack('i', 19);
                $charHea=pack('i', 24);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Magician":
                $charStr=pack('i', 16);
                $charSpi=pack('i', 29);
                $charTal=pack('i', 19);
                $charAgi=pack('i', 14);
                $charHea=pack('i', 21);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

            case "Priestess":
                $charStr=pack('i', 15);
                $charSpi=pack('i', 28);
                $charTal=pack('i', 21);
                $charAgi=pack('i', 15);
                $charHea=pack('i', 20);
                $charStateStr=$charStr . $charSpi . $charTal . $charAgi . $charHea;
            break;

        }

        return $charStateStr;


    }

    function charResetHair($class,$model)
    {
        $modelName1 = '';
        $modelName2 = '';
        switch ($class)
        {
            case "Fighter":
                $modelName1=pack("a*","char\\tmABCD\\b001.ini").pack("h*",00);
                $modelName2=pack("a*","char\\tmABCD\\tmh-b02.inf").pack("h*",00);
            break;

            case "Mechanician":
                $modelName1=pack("a*","char\\tmABCD\\a001.ini").pack("h*",00);
                $modelName2=pack("a*","char\\tmABCD\\tmh-a02.inf").pack("h*",00);
            break;

            case "Archer":
                $modelName1=pack("a*","char\\tmABCD\\d001.ini").pack("h*",00);
                $modelName2=pack("a*","char\\tmABCD\\tfh-D01.inf").pack("h*",00);
            break;

            case "Pikeman":
                $modelName1=pack("a*","char\\tmABCD\\c001.ini").pack("h*",00);
                $modelName2=pack("a*","char\\tmABCD\\tmh-c02.inf").pack("h*",00);
            break;

            case "Atalanta":
                $modelName1=pack("a*","char\\tmABCD\\mb001.ini");
                $modelName2=pack("a*","char\\tmABCD\\Mfh-B02.inf").pack("h*",00);
            break;

            case "Knight":
                $modelName1=pack("a*","char\\tmABCD\\ma001.ini");
                $modelName2=pack("a*","char\\tmABCD\\Mmh-A03.inf").pack("h*",00);
            break;

            case "Magician":
                $modelName1=pack("a*","char\\tmABCD\\md001.ini");
                $modelName2=pack("a*","char\\tmABCD\\Mmh-D01.inf").pack("h*",00);
            break;

            case "Priestess":
                $modelName1=pack("a*","char\\tmABCD\\mc001.ini");
                $modelName2=pack("a*","char\\tmABCD\\Mfh-C01.inf").pack("h*",00);
            break;
        }

        $model=($model==1)?$modelName1:$modelName2;

        return $model;
    }

    function charResetSkill()
    {
        $tier0_1=pack("h*",00);
        $tier0_2=pack("h*",00);
        $tier0_3=pack("h*",00);
        $tier0_4=pack("h*",00);

        $tier1_1=pack("h*",00);
        $tier1_2=pack("h*",00);
        $tier1_3=pack("h*",00);
        $tier1_4=pack("h*",00);

        $tier2_1=pack("h*",00);
        $tier2_2=pack("h*",00);
        $tier2_3=pack("h*",00);
        $tier2_4=pack("h*",00);

        $tier3_1=pack("h*",00);
        $tier3_2=pack("h*",00);
        $tier3_3=pack("h*",00);
        $tier3_4=pack("h*",00);

        $skillStr=$tier3_4.$tier0_1.$tier0_2.$tier0_3.$tier0_4.
            $tier1_1.$tier1_2.$tier1_3.$tier1_4.
            $tier2_1.$tier2_2.$tier2_3.$tier2_4.
            $tier3_1.$tier3_2.$tier3_3;

        return $skillStr;
    }

    function charResetMastery()
    {
        $tier0_1=trim(pack("i",5000));
        $tier0_2=trim(pack("i",5000));
        $tier0_3=trim(pack("i",5000));
        $tier0_4=trim(pack("i",5000));

        $tier1_1=trim(pack("i",5000));
        $tier1_2=trim(pack("i",5000));
        $tier1_3=trim(pack("i",5000));
        $tier1_4=trim(pack("i",5000));

        $tier2_1=trim(pack("i",5000));
        $tier2_2=trim(pack("i",5000));
        $tier2_3=trim(pack("i",5000));
        $tier2_4=trim(pack("i",5000));

        $tier3_1=trim(pack("i",5000));
        $tier3_2=trim(pack("i",5000));
        $tier3_3=trim(pack("i",5000));
        $tier3_4=trim(pack("i",5000));

        $masteryStr=$tier3_4.$tier0_1.$tier0_2.$tier0_3.$tier0_4.
            $tier1_1.$tier1_2.$tier1_3.$tier1_4.
            $tier2_1.$tier2_2.$tier2_3.$tier2_4.
            $tier3_1.$tier3_2.$tier3_3;

        return $masteryStr;
    }

    function charFullMastery()
    {
        $tier0_1=trim(pack("i",10000));
        $tier0_2=trim(pack("i",10000));
        $tier0_3=trim(pack("i",10000));
        $tier0_4=trim(pack("i",10000));

        $tier1_1=trim(pack("i",10000));
        $tier1_2=trim(pack("i",10000));
        $tier1_3=trim(pack("i",10000));
        $tier1_4=trim(pack("i",10000));

        $tier2_1=trim(pack("i",10000));
        $tier2_2=trim(pack("i",10000));
        $tier2_3=trim(pack("i",10000));
        $tier2_4=trim(pack("i",10000));

        $tier3_1=trim(pack("i",10000));
        $tier3_2=trim(pack("i",10000));
        $tier3_3=trim(pack("i",10000));
        $tier3_4=trim(pack("i",10000));

        $masteryStr=$tier3_4.$tier0_1.$tier0_2.$tier0_3.$tier0_4.
            $tier1_1.$tier1_2.$tier1_3.$tier1_4.
            $tier2_1.$tier2_2.$tier2_3.$tier2_4.
            $tier3_1.$tier3_2.$tier3_3;

        return $masteryStr;
    }

function numDir($name)
    {
        $total = 0;
        $character=array
        (
            "!"=>"33",
            "\""=>"34",
            "#"=>"35",
            "$"=>"36",
            "%"=>"37",
            "&"=>"38",
            "'"=>"39",
            "("=>"40",
            ")"=>"41",
            "*"=>"42",
            "+"=>"43",
            ","=>"44",
            "-"=>"45",
            "."=>"46",
            "/"=>"47",
            "0"=>"48",
            "1"=>"49",
            "2"=>"50",
            "3"=>"51",
            "4"=>"52",
            "5"=>"53",
            "6"=>"54",
            "7"=>"55",
            "8"=>"56",
            "9"=>"57",
            ":"=>"58",
            ";"=>"59",
            "<"=>"60",
            "="=>"61",
            ">"=>"62",
            "?"=>"63",
            "@"=>"64",
            "A"=>"65",
            "B"=>"66",
            "C"=>"67",
            "D"=>"68",
            "E"=>"69",
            "F"=>"70",
            "G"=>"71",
            "H"=>"72",
            "I"=>"73",
            "J"=>"74",
            "K"=>"75",
            "L"=>"76",
            "M"=>"77",
            "N"=>"78",
            "O"=>"79",
            "P"=>"80",
            "Q"=>"81",
            "R"=>"82",
            "S"=>"83",
            "T"=>"84",
            "U"=>"85",
            "V"=>"86",
            "W"=>"87",
            "X"=>"88",
            "Y"=>"89",
            "Z"=>"90",
            "["=>"91",
            "\\"=>"92",
            "]"=>"93",
            "^"=>"94",
            "_"=>"95",
            "`"=>"96",
            "a"=>"65",
            "b"=>"66",
            "c"=>"67",
            "d"=>"68",
            "e"=>"69",
            "f"=>"70",
            "g"=>"71",
            "h"=>"72",
            "i"=>"73",
            "j"=>"74",
            "k"=>"75",
            "l"=>"76",
            "m"=>"77",
            "n"=>"78",
            "o"=>"79",
            "p"=>"80",
            "q"=>"81",
            "r"=>"82",
            "s"=>"83",
            "t"=>"84",
            "u"=>"85",
            "v"=>"86",
            "w"=>"87",
            "x"=>"88",
            "y"=>"89",
            "z"=>"90",
            "{"=>"123",
            "|"=>"124",
            "}"=>"125",
            "~"=>"126",
            ""=>"127",
            "€"=>"128",
            ""=>"129",
            "‚"=>"130",
            "ƒ"=>"131",
            "„"=>"132",
            "…"=>"133",
            "†"=>"134",
            "‡"=>"135",
            "ˆ"=>"136",
            "‰"=>"137",
            "Š"=>"138",
            "‹"=>"139",
            "Œ"=>"140",
            ""=>"141",
            "Ž"=>"142",
            ""=>"143",
            ""=>"144",
            "‘"=>"145",
            "’"=>"146",
            "“"=>"147",
            "”"=>"148",
            "•"=>"149",
            "–"=>"150",
            "—"=>"151",
            "˜"=>"152",
            "™"=>"153",
            "š"=>"154",
            "›"=>"155",
            "œ"=>"156",
            ""=>"157",
            "ž"=>"158",
            "Ÿ"=>"159",
            "¡"=>"161",
            "¢"=>"162",
            "£"=>"163",
            "¤"=>"164",
            "¥"=>"165",
            "¦"=>"166",
            "§"=>"167",
            "¨"=>"168",
            "©"=>"169",
            "ª"=>"170",
            "«"=>"171",
            "¬"=>"172",
            "­"=>"173",
            "®"=>"174",
            "¯"=>"175",
            "°"=>"176",
            "±"=>"177",
            "²"=>"178",
            "³"=>"179",
            "´"=>"180",
            "µ"=>"181",
            "¶"=>"182",
            "·"=>"183",
            "¸"=>"184",
            "¹"=>"185",
            "º"=>"186",
            "»"=>"187",
            "¼"=>"188",
            "½"=>"189",
            "¾"=>"190",
            "¿"=>"191",
            "À"=>"192",
            "Á"=>"193",
            "Â"=>"194",
            "Ã"=>"195",
            "Ä"=>"196",
            "Å"=>"197",
            "Æ"=>"198",
            "Ç"=>"199",
            "È"=>"200",
            "É"=>"201",
            "Ê"=>"202",
            "Ë"=>"203",
            "Ì"=>"204",
            "Í"=>"205",
            "Î"=>"206",
            "Ï"=>"207",
            "Ð"=>"208",
            "Ñ"=>"209",
            "Ò"=>"210",
            "Ó"=>"211",
            "Ô"=>"212",
            "Õ"=>"213",
            "Ö"=>"214",
            "×"=>"215",
            "Ø"=>"216",
            "Ù"=>"217",
            "Ú"=>"218",
            "Û"=>"219",
            "Ü"=>"220",
            "Ý"=>"221",
            "Þ"=>"222",
            "ß"=>"223",
            "à"=>"224",
            "á"=>"225",
            "â"=>"226",
            "ã"=>"227",
            "ä"=>"228",
            "å"=>"229",
            "æ"=>"230",
            "ç"=>"231",
            "è"=>"232",
            "é"=>"233",
            "ê"=>"234",
            "ë"=>"235",
            "ì"=>"236",
            "í"=>"237",
            "î"=>"238",
            "ï"=>"239",
            "ð"=>"240",
            "ñ"=>"241",
            "ò"=>"242",
            "ó"=>"243",
            "ô"=>"244",
            "õ"=>"245",
            "ö"=>"246",
            "÷"=>"247",
            "ø"=>"248",
            "ù"=>"249",
            "ú"=>"250",
            "û"=>"251",
            "ü"=>"252",
            "ý"=>"253",
            "þ"=>"254",
            "ÿ"=>"255",
        );

        for($i=0;$i<strlen($name);$i++)
        {
            $total+=$character[$name[$i]];
            if($total>=256)
                $total=$total-256;
        }

        return $total;
    }
}
?>