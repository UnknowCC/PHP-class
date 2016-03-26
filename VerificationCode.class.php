<?php

/**
* @example
* 1、echo new VerificationCode();
* 2、echo new VerificationCode(80,30,5);
*/
class VerificationCode
{
    private $width;
    private $height;
    private $vCodeNum;
    private $disturbColorNumber;
    private $checkCode;
    private $image;

    public function __construct($width = 80, $height = 20, $vCodeNum = 4)
    {
        $this->width = $width;
        $this->height = $height;
        $this->vCodeNum = $vCodeNum;
        $number = floor($height * $width / 15);

        $this->disturbColorNumber = $this->setDisturbNum();
        $this->checkCode = $this->setCheckCode();
    }

    private function setDisturbNum()
    {
        $number = floor($this->height * $this->width / 15);
        if ($number > 240 - $this->vCodeNum) {
            $disturbNum = 240 - $this->vCodeNum;
        } else {
            $disturbNum = $number;
        }

        return $disturbNum;
    }

    private function setCheckCode()
    {
        $codeStr = '';
        $code = '3456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXY';
        for ($i=0; $i < $this->vCodeNum; $i++) {
            $codeStr .= $code[rand(0, strlen($code) - 1)];
        }
        return $codeStr;
    }

    public function __toString()
    {
        $_SESSION['verificationCode'] = strtoupper($this->checkCode);
        $this->outImg();
        return ;
    }

    private function outImg()
    {
        $this->setImage();
        $this->setDisturb();
        $this->setCode();
        $this->outputImage();
    }

    private function setImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $backColor = imagecolorallocate($this->image, rand(250,255), rand(250,255), rand(250,255));
        imagefill($this->image, 0, 0, $backColor);
    }

    private function setDisturb()
    {
        for ($i=0; $i < $this->disturbColorNumber; $i++) {
            $color = imagecolorallocate($this->image, rand(50,120), rand(50,120), rand(50,120));
            imagesetpixel($this->image, rand(1,$this->width-2), rand(1,$this->height - 2), $color);
        }
        for ($i=0; $i < 10; $i++) {
            $color = imagecolorallocate($this->image, rand(120,220), rand(120,220), rand(120,220));
            imagearc($this->image, rand(-10,$this->width), rand(-10,$this->width), rand(30,300), rand(20,200), 55, 44, $color);
        }
    }

    private function setCode()
    {
        for ($i=0; $i < $this->vCodeNum; $i++) {
            $fontcolor = imagecolorallocate($this->image, rand(0,120), rand(0,120), rand(0,120));
            $fontSize = rand(7,10);
            $x = floor($this->width / $this->vCodeNum) * $i +3;
            $y = rand(0, $this->height - imagefontheight($fontSize));
            imagechar($this->image, $fontSize, $x, $y, $this->checkCode[$i], $fontcolor);
        }
    }

    private function outputImage()
    {
        ob_clean();
        header('content-type:image/png');
        imagepng($this->image);
    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }
}
