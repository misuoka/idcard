# idcard 身份证校验及信息提取

安装方式
```
composer require misuoka\idcard
```

使用方式
```php
use misuoka\IDCard;

$idcard = new IDCard("身份证号码");

if($idcard->validate()) {
    $idcard->getBirthDate(); // 出生日期 DateTime 格式
    $idcard->getBirthDate()->format('Y-m-d'); // 出生日期字符串（xxxx-xx-xx）格式
    $idcard->getBirthYear(); // 出生年份
    $idcard->getBirthMonth(); // 出生月份
    $idcard->getBirthDay(); // 出生当日
    $idcard->getGender(); // 性别：男 | 女
    $idcard->getGenderCode(); // 性别：1 | 2
    $idcard->getAge(); // 年龄
    $idcard->getConstellation(); // 星座
    $idcard->getRegion(); // 出生地
    $idcard->format(); // 格式化输出：5226***********326
    $idcard->format('-', 6, 4); // 格式化输出：522632--------2326
} else {
    echo "验证不通过";
}
```

> 备注：目前仅支持中国
