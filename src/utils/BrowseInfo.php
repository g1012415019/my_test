<?php


namespace gongzhe\utils;

/**
 * 获取浏览信息
 * @author gongzhe
 * @createTime 2018-08-28 10:49:59
 * @qqNumber 1012415019
 * Class BrowseInfo
 * @package util
 */
class BrowseInfo
{

    /**
     * 得到来源
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     */
    public function getAgentType(){

        //获取软件类别
        $app_type = 'other';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'icroMessenger') !== false) {
            $app_type = 'wx';
        }

        //获取手机类别
        $device_type = $this->getDeviceType();

        //微信-苹果
        if ($app_type == 'wx' && $device_type == 'ios') {
            return 1;
        }
        //微信-安卓
        if ($app_type == 'wx' && $device_type == 'android') {
            return 2;
        }
        //微信-其他
        if ($app_type == 'wx' && $device_type == 'other') {
            return 3;
        }

        //手机网页-苹果
        if ($app_type == 'other' && $device_type == 'ios') {
            return 7;
        }
        //手机网页-安卓
        if ($app_type == 'other' && $device_type == 'android') {
            return 8;
        }
        //手机网页其他
        if ($app_type == 'other' && $device_type == 'other') {
            return 9;
        }

        //其他设备
        return 10;
    }

    /**
     * 得到当前浏览器
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     */
    public function getBrowser(){

        $agent=$_SERVER["HTTP_USER_AGENT"];
        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
            return "ie";
        else if(strpos($agent,'Firefox')!==false)
            return "firefox";
        else if(strpos($agent,'Chrome')!==false)
            return "chrome";
        else if(strpos($agent,'Opera')!==false)
            return 'opera';
        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
            return 'safari';
        else
            return 'unknown';
    }

    /**
     * 得到当前浏览器版本号
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     */
    public function getBrowserVer(){

        //当浏览器没有发送访问者的信息的时候
        if (empty($_SERVER['HTTP_USER_AGENT'])){
            return 'unknow';
        }
        $agent= $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
            return $regs[1];
        elseif ((strpos($agent,'Chrome')==false)&&preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
            return $regs[1];
        else
            return 'unknow';

    }

    /**
     * 获得用户的真实IP地址
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     * @return array|false|string
     */
    public function real_ip(){

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;

    }

    /**
     * 判断是否为手机
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     * @return bool
     */
    public function is_mobile(){

        $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

        $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

        $regex_match.=")/i";

        // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
        return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));

    }

    /**
     * 请输入描述和说明
     * @author gongzhe
     * @createTime 2018-08-28 10:50:40
     * @qqNumber 1012415019
     * @return string
     */
    public function getDeviceType()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type  = 'other';
        //分别进行判断
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 'ios';
        }

        if (strpos($agent, 'android')) {
            $type = 'android';
        }
        return $type;
    }
}
