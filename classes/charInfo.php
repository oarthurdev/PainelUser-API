<?php

class CharInfo {
    function __construct(){
        $this->rootDir = "C:/ServerPT/";
        $this->dirUserData = $this->rootDir."DataServer/userdata/";
        $this->dirUserInfo = $this->rootDir."DataServer/userinfo/";
        $this->dirUserDelete = $this->rootDir."DataServer/deleted/";
    }
    
    public function getCharInfo ($charNameArr, $UserId) {
        
        $func = new Func;

        $aReturn = [];
        
        $i = 0;
        if($charNameArr == null){
            return array(
                'semChar' => true
            );
        }
        foreach($charNameArr as $key=>$value)
        {
            $i += 1;
            $expValue=explode("\x00", $value);
            
            $charDat = $this->dirUserData . ( $func->numDir($expValue[0]) ) . "/" . $expValue[0] . ".dat";
            
            if(file_exists($charDat) && ( (filesize($charDat)==16384) || (filesize($charDat)==111376) || (filesize($charDat)==220976) ) )
            {
                if($expValue[0]!="")
                {
                    $fOpen = fopen($charDat, "r");
                    $fRead = fread($fOpen,filesize($charDat));
                    @fclose($fOpen);
                    $charClass = substr($fRead,0xc4,1);
                    $class = ord($charClass);
                }
            }
            
            if(file_exists($charDat) && ( (filesize($charDat)==16384) || (filesize($charDat)==111376) || (filesize($charDat)==220976) ) )
            {        
                
                $fOpen = fopen($charDat, "r");
                $fRead = fread($fOpen,filesize($charDat));
                @fclose($fOpen);
                
                // details
                $charLevel = substr($fRead,0xc8,1);
                $charClass = substr($fRead,0xc4,1);
                $charName = trim(substr($fRead,0x10,15));
                $charID = trim(substr($fRead,0x2c0,10));
                $numClass = ord($charClass);
                
                if((strtolower($charID)==strtolower($UserId)))
                {
                    if($expValue[0]==$charName)
                    {
                        switch (ord($charClass))
                        {
                            case 1: $class = 'Lutador'; break;
                            case 2: $class = 'Mecânico'; break;
                            case 3: $class = 'Arqueira'; break;
                            case 4: $class = 'Pikeman'; break;
                            case 5: $class = 'Atalanta'; break;
                            case 6: $class = 'Cavaleiro'; break;
                            case 7: $class = 'Mago'; break;
                            case 8: $class = 'Sacerdotiza'; break;
                        }
                        
                        $cDat = $charDat;
                        $cNum = $func->numDir($expValue[0]);
                        $cId = $charID;
                        $cName = $charName;
                        $cLevel = ord($charLevel);
                        $cClass = $class;
                        
                        $aReturn[] = array(
                            'cName' => $cName,
                            'cLevel' => $cLevel,
                            'cClass' => $cClass,
                            'numClass' => $numClass
                        );            
                    } else {
                        $expName=explode("\x00",$charName);
                        
                        $fRead=false;
                        $fOpen = fopen($charDat, "r");
                        while (!feof($fOpen)) {
                            @$fRead = "$fRead" . fread($fOpen, filesize($charDat) );
                        }
                        fclose($fOpen);
                        
                        // Fill in 00 to left character
                        $addOnLeft=false;
                        $leftLen=32-strlen($expName[0]);
                        for($i=0;$i<$leftLen;$i++)
                        {
                            $addOnLeft.=pack("h*",00);
                        }
                        $writeName=$expName[0].$addOnLeft;
                        
                        $sourceStr = substr($fRead, 0, 16) . $writeName . substr($fRead, 48);
                        $fOpen = fopen($charDat, "wb");
                        fwrite($fOpen, $sourceStr, strlen($sourceStr));
                        fclose($fOpen);
                        
                        echo "<div class='alert alert-warning alert-dismissable'>".$mess_limpar_arquivo."</div>";
                        return false;
                    }
                } else {
                    echo "<div class='alert alert-warning alert-dismissable'>Personagem não é da conta</div>";
                    return false;
                }
            }
            else {

            }      
        }

        return $aReturn;
    }
    
    public function getUserInfo ($UserId) {
        $charNameArr = null;
        
        $func = new Func;
        
        $charInfo= $this->dirUserInfo . ($func->numDir($UserId)) . "/" . $UserId . ".dat";
        
        if(file_exists($charInfo) && ( filesize($charInfo)==240) )
        {
            $fRead=false;
            $fOpen = fopen($charInfo, "r");
            $fRead =fread($fOpen,filesize($charInfo));
            @fclose($fOpen);
            
            $charNameArr=array(
                "48" => trim(substr($fRead,0x30,15),"\x00"),
                "80" => trim(substr($fRead,0x50,15),"\x00"),
                "112"=> trim(substr($fRead,0x70,15),"\x00"),
                "144"=> trim(substr($fRead,0x90,15),"\x00"),
                "176"=> trim(substr($fRead,0xb0,15),"\x00"),
            );
        }
        
        return array(
            "userInfo" => $charInfo,
            "charNames" => $charNameArr
        );
    }

    public function deleteChar ($charname, $qCharID) {
        $func = new Func;

        $charInfo= $this->dirUserInfo . ($func->numDir($qCharID)) . "/" . $qCharID . ".dat";

        $fRead=false;
        $fOpen = fopen($charInfo, "r");
        $fRead =fread($fOpen,filesize($charInfo));
        @fclose($fOpen);

        // list char information
        $charNameArr=array(
        "48" => trim(substr($fRead,0x30,15),"\x00"),
        "80" => trim(substr($fRead,0x50,15),"\x00"),
        "112"=> trim(substr($fRead,0x70,15),"\x00"),
        "144"=> trim(substr($fRead,0x90,15),"\x00"),
        "176"=> trim(substr($fRead,0xb0,15),"\x00"),
        );

        $chkCharLine=array();
        foreach($charNameArr as $key=>$value)
        {
        if($charname==$value) $chkCharLine[]=$key;
        }

        // Remove character from information file--------------------------------------

        // Fill in 00 to left character
        $addOnLeft=false;
        for($i=0;$i<15;$i++)
        {
        $addOnLeft.=pack("h*",00);
        }

        $startPoint=$chkCharLine[0];
        $endPoint=$startPoint+15;

        $fRead=false;
        $fOpen = fopen($charInfo, "r");
        while (!feof($fOpen)) {
        @$fRead = "$fRead" . fread($fOpen, filesize($charInfo) );
        }
        fclose($fOpen);

        $sourceStr = substr($fRead, 0, $startPoint) . $addOnLeft . substr($fRead, $endPoint);
        $fOpen = fopen($charInfo, "wb");
        fwrite($fOpen, $sourceStr, strlen($sourceStr));
        fclose($fOpen);

        copy($this->dirUserData . ($func->numDir($charname)) . "/" . $charname . ".dat" ,$this->dirUserDelete . "/" . $charname . ".dat");
        unlink($this->dirUserData . ($func->numDir($charname)) . "/" . $charname . ".dat");

        return array(
            "deletado" => true
        );
    }
    
    public function getCharData ($charInfo, $charNameArr) {
        if(file_exists($charInfo) && ( filesize($charInfo)==240) )
        {
            if(count($charNameArr)>0)
            {                               
                $i = 0;
                foreach($charNameArr as $key=>$value)
                {
                    $i += 1;
                    $expValue=explode("\x00",$value);
                    $charDat = $this->dirUserData . ( $func->numDir($expValue[0]) ) . "/" . $expValue[0] . ".dat";
                    if(file_exists($charDat) && ( (filesize($charDat)==16384) || (filesize($charDat)==111376) || (filesize($charDat)==220976) ) )
                    {
                        if($expValue[0]!="")
                        {
                            $fOpen = fopen($charDat, "r");
                            $fRead = fread($fOpen,filesize($charDat));
                            @fclose($fOpen);
                            $charClass = substr($fRead,0xc4,1);
                            $class = ord($charClass);
                        }
                    }
                }
            }
        }
    }
}

