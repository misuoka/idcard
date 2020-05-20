<?php
/*
 * @Description:
 * @Author: Misuoka
 * @Github: https://github.com/misuoka
 * @Licensed: MIT
 * @Version: 1.0.0
 * @Date: 2020-05-20 09:34:39
 * @LastEditTime: 2020-05-20 09:34:43
 */
declare (strict_types = 1);

namespace misuoka;

class IDCard
{
    // 性别常量
    const GENDER_MALE   = '男';
    const GENDER_FEMALE = '女';

    // 默认时间格式
    const FORMAT_YEAR_DEFAULT  = 'Y';
    const FORMAT_MONTH_DEFAULT = 'm';
    const FORMAT_DAY_DEFAULT   = 'd';

    /**
     * 身份证号码
     *
     * @var [type]
     */
    private $code = null;

    /**
     * 出生日期
     *
     * @var [type]
     */
    private $birthdate = null;

    /**
     * 星座字典
     *
     * @var array
     */
    private $constellation = [
        "白羊座" => ['3-21', '4-19'],
        "金牛座" => ['4-20', '5-20'],
        "双子座" => ['5-21', '6-21'],
        "巨蟹座" => ['6-22', '7-22'],
        "狮子座" => ['7-23', '8-22'],
        "处女座" => ['8-23', '9-22'],
        "天秤座" => ['9-23', '10-23'],
        "天蝎座" => ['10-24', '11-22'],
        "射手座" => ['11-23', '12-21'],
        "摩羯座" => ['12-22', '1-19'],
        "水瓶座" => ['1-20', '2-18'],
        "双鱼座" => ['2-19', '3-20'],
    ];

    public function __construct($code, $area = "zh")
    {
        $this->code = strtoupper(trim($code));
    }

    /**
     * 身份证校验
     *
     * @return boolean
     */
    public function validate(): bool
    {
        $len = strlen($this->code);

        switch ($len) {
            case 18:
                $ret = $this->check18Code($this->code);
                break;
            case 15:
                $ret = $this->check15Code($this->code);
                break;
            default:
                $ret = false;
        }

        return $ret;
    }

    /**
     * 获取生日
     *
     * @return DateTime
     */
    public function getBirthDate(): \DateTime
    {
        if (is_null($this->birthdate)) {
            if (!$this->validate()) {
                throw new \Exception('身份证号码错误，无法解析信息');
            } else {
                $code = $this->code;
                if (strlen($code) == 18) {
                    $array = [
                        substr($code, 6, 4),
                        substr($code, 10, 2),
                        substr($code, 12, 2),
                    ];
                } else {
                    $array = [
                        '19' . substr($code, 6, 2),
                        substr($code, 8, 2),
                        substr($code, 10, 2),
                    ];
                }
                $this->birthdate = new \DateTime(\implode('-', $array));
            }
        }

        return $this->birthdate;
    }

    /**
     * 获取年龄
     *
     * @param DateTime $date
     * @return integer
     */
    public function getAge(\DateTime $date = null): int
    {
        if (is_null($date)) {
            $date = new \DateTime();
        }
        $birthdate = $this->getBirthDate();
        if ($birthdate) {
            $age = $date->diff($birthdate)->y;

            $bm = $birthdate->format('n');
            $bd = $birthdate->format('j');
            $cm = date('n');
            $cd = date('j');

            if ($cm > $bm || $cm == $bm && $cd > $bd) {
                $age++;
            }
        } else {
            $age = 0;
        }
        return $age;
    }

    /**
     * 获取身份证中生日的某年
     * @return [int] 返回出生的某年
     */
    public function getBirthYear($format = self::FORMAT_YEAR_DEFAULT): ?string
    {
        $allowedFormats = ['L', 'o', 'Y', 'y'];
        if (!in_array($format, $allowedFormats)) {
            throw new InvalidArgumentException(
                '不允许的年份格式。允许的值为: ' . implode($allowedFormats)
            );
        }
        $birthdate = $this->getBirthDate();
        return $birthdate ? $birthdate->format($format) : null;
    }

    /**
     * 获取身份证中生日的某月
     * @return [int] 返回出生的某月
     */
    public function getBirthMonth($format = self::FORMAT_MONTH_DEFAULT): ?string
    {
        $allowedFormats = ['F', 'm', 'M', 'n', 't'];
        if (!in_array($format, $allowedFormats)) {
            throw new InvalidArgumentException(
                '不允许的月份格式。允许值为: ' . implode($allowedFormats)
            );
        }

        return $this->getBirthDate()->format($format);
    }

    /**
     * 获取身份证中生日的某天
     * @return [int] 返回出生的某天
     */
    public function getBirthDay($format = self::FORMAT_DAY_DEFAULT): ?string
    {
        $allowedFormats = ['d', 'D', 'j', 'l', 'N', 'S', 'w', 'z'];
        if (!in_array($format, $allowedFormats)) {
            throw new InvalidArgumentException(
                '不允许的日期格式。允许的值为: ' . implode($allowedFormats)
            );
        }

        return $this->getBirthDate()->format($format);
    }

    /**
     * 获取身份证中的性别
     * @return string Person gender
     */
    public function getGender(): string
    {
        if (!$this->validate()) {
            throw new \Exception('身份证号码错误，无法解析信息');
        }

        $code     = $this->code;
        $genderNo = strlen($code) == 18 ? substr($code, 16, 1) : substr($code, 14, 1);

        return $genderNo % 2 === 0 ? self::GENDER_FEMALE : self::GENDER_MALE;
    }

    /**
     * 获取身份证中的性别，1: 男，2: 女
     *
     * @return integer
     */
    public function getGenderCode(): int
    {
        if (!$this->validate()) {
            throw new \Exception('身份证号码错误，无法解析信息');
        }

        $code     = $this->code;
        $genderNo = strlen($code) == 18 ? substr($code, 16, 1) : substr($code, 14, 1);

        return $genderNo % 2 === 0 ? 2 : 1;
    }

    public function getConstellation(): string
    {
        if (!$this->validate()) {
            throw new \Exception('身份证号码错误，无法解析信息');
        }

        foreach ($this->constellation as $name => $v) {
            $date1     = new \DateTime($this->getBirthYear() . '-' . $v[0]);
            $date2     = new \DateTime($this->getBirthYear() . '-' . $v[1]);
            $timestamp = $this->birthdate->getTimestamp();
            if ($timestamp >= $date1->getTimestamp() && $timestamp <= $date2->getTimestamp()) {
                return $name;
            }
        }
    }

    /**
     * 获得出生地区
     *
     * @return void
     */
    public function getRegion($seperate = ' ')
    {
        if (!$this->validate()) {
            throw new \Exception('身份证号码错误，无法解析信息');
        }

        $data = require __DIR__ . '/data/region.php';

        $regionCode = \substr($this->code, 0, 6);

        $province = \substr_replace($regionCode, '0000', 2, 4);
        $district = \substr_replace($regionCode, '00', 4, 2);

        return \implode($seperate, [
            $data[$province],
            $data[$district],
            $data[$regionCode],
        ]);
    }

    /**
     * 获得格式化后的身份证号
     *
     * @param string $replace
     * @param integer $left
     * @param integer $right
     * @return void
     */
    public function format($replace = '*', $left = 4, $right = 3)
    {
        $length = strlen($this->code) - $left - $right;

        $tpl = '';
        for ($i = 0; $i < $length; $i++) {
            $tpl .= $replace;
        }

        return \substr_replace($this->code, $tpl, $left, $length);
    }

    private function check18Code($code = null)
    {
        $code = $code ? $code : $this->code;
        // 取出本体码
        $idcardBase = substr($code, 0, 17);
        // 取出校验码
        $verifyCode = substr($code, 17, 1);
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        // 校验码对应值
        $verifyCodeList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        // 根据前17位计算校验码
        $total = 0;
        for ($i = 0; $i < 17; $i++) {
            $total += substr($idcardBase, $i, 1) * $factor[$i];
        }
        // 取模
        $mod = $total % 11;
        // 比较校验码
        if ($verifyCode != $verifyCodeList[$mod]) {
            return false;
        }
        return true;
    }

    private function check15Code($code = null)
    {
        $code = $code ? $code : $this->code;
        // 正则校验
        // 校验省份
        // 校验出生地
        // 校验生日
        $gb2260 = new \GB2260\GB2260();
        if (!$gb2260->get(\substr($code, 0, 6))) {
            return false;
        }

        try {
            $array = [
                '19' . substr($code, 6, 2),
                substr($code, 8, 2),
                substr($code, 10, 2),
            ];
            $datetime = new \DateTime(\implode('-', $array));
        } catch (\Exception $e) {
            return false;
        }

        return true; // 函数暂时未定
    }

    /*private function _idCode15to18() {
$idCode = $this->_idCode;
if(strlen($idCode) != 15) return '';

if(array_search(substr($idCode, 12,3), array('996', '997', '998', '999')) !== false) {
// $idCode18 = substr($idCode, )
}
}*/
}
