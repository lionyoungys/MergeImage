<?php
/**
 * Created by PhpStorm.
 * User: 帅
 * Date: 2017/2/24
 * Time: 11:49
 */
header('content-type:text/html;charset=utf-8');
$baseImage = './images/baseImage.png';    //背景图片
$avatarImage = './images/avatar.jpg';    //用户头像图片

/**
 * 生成图片数据函数
 * @param String $fileName 图片文件url
 * @return array array[0] = 图片资源, array[1] = 图片mime类型, array[2] = 图片宽度, array[3] = 图片高度
 */
function getCreateImageInfo($fileName) {
    list($width, $height, $imageTypeInt) = getimagesize($fileName);    //获取原图片的宽，高，类型
    $imageTypeArray = array(1=>'gif', 2=>'jpeg', 3=>'png');    //根据返回常量值定义图像类型数组
    $imageFrom = 'imagecreatefrom'.$imageTypeArray[$imageTypeInt];    //拼装创建图像图像函数，根据类型不同创建不同函数
    $image = $imageFrom($fileName);    //创建图片
    if(!$image)
    {
        //生成错误图片信息
        $image  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($image, 255, 255, 255);
        $tc  = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, 150, 30, $bgc);
        imagestring($image, 1, 5, 5, 'Error loading ' . $fileName, $tc);
        $imageOut = 'image'.$imageTypeArray[$imageTypeInt];    //根据图片类型不同来拼接图片输出函数
        header('Content-Type: image/'.$imageTypeArray[$imageTypeInt]);    //根据图片类型不同来拼接头信息
        $imageOut($image);    //输出图片
        imagedestroy($image);    //销毁资源
        return false;
    }
    return array($image, $imageTypeArray[$imageTypeInt], $width, $height);
}
/**
 * 生成正方形小图片函数
 * @param String $fileName 图片文件url
 * @param Int $imageSize 文件大小
 * @return array array[0] = 图片资源, array[1] = 图片大小
 */
function createSmallImage($fileName, $imageSize = 136) {
    list($image, , $width ,$height) = getCreateImageInfo($fileName);
    if ($width != $imageSize && $height != $imageSize) {    //判断图片是否为指定大小的正方形图片
        //重新设定图片大小
        $tmpImage = imagecreatetruecolor(136, 136);
        imagecopyresized($tmpImage, $image, 0, 0, 0, 0, $imageSize, $imageSize, $width, $height);
        return array($tmpImage, $imageSize);
    } else {
        return array($image, $imageSize);
    }
}
/**
 * 生成原型小图片函数
 * @param String $fileName 图片文件url
 * @param Array $rgb 基础图片定位周围的rgb色值
 * @return array array[0] = 图片资源, array[1] = 图片大小
 */
function createCircleImage($fileName, $rgb = array(246, 247, 229)) {
    list($image,$radius) = createSmallImage($fileName);

    $circleImage = imagecreatetruecolor($radius, $radius);

    imagesavealpha($circleImage, true);    //保存透明图像通道

    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $backgroundColor = imagecolorallocatealpha($circleImage, $rgb[0], $rgb[1], $rgb[2], 127);

    imagefill($circleImage, 0, 0, $backgroundColor);

    $r   = $radius / 2; //圆半径
    //重新拾取圆形区域像素并绘制图片
    for ($x = 0; $x < $radius; $x++) {
        for ($y = 0; $y < $radius; $y++) {
            $rgbColor = imagecolorat($image, $x, $y);
            if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                imagesetpixel($circleImage, $x, $y, $rgbColor);
            }
        }
    }

    return array($circleImage,$radius);
}

/**
 * 生成合成后的图片函数
 * @param String $baseImage 基础背景图片文件url
 * @param String $avatarImage 头像图片文件url
 * @return void 输出图片资源
 */
function createMergeImage($baseImage,$avatarImage) {
    ob_clean();    //清除缓冲区内容，防止无法输出图像
    list($avatar,$radius) = createCircleImage($avatarImage);
    list($image,$imageType) = getCreateImageInfo($baseImage);
    imagecopymerge($image,$avatar,165,65,0,0,$radius,$radius,100);
    $imageOut = 'image'.$imageType;    //根据图片类型不同来拼接图片输出函数
    header('Content-Type: image/'.$imageType);    //根据图片类型不同来拼接头信息
    $imageOut($image);    //输出图片
    imagedestroy($image);    //销毁资源
}

//调用合成函数
createMergeImage($baseImage,$avatarImage);











