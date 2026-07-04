<?php
namespace App;

Class Helper{

    public static function geolocation($ip)
    {
        $session = sha1('_ipapi_' . $ip);
        if (isset($_SESSION[$session])) {

            return $_SESSION[$session];
        } else {
            $api_key = 'LlYVGewz67LJuV8';
            $url = 'https://pro.ip-api.com/json/' . $ip . '?fields=21229567&key=' . $api_key;

            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36");
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                curl_close($ch);
                return false;
            }
            curl_close($ch);
            $data = json_decode($response, true);
            $_SESSION[$session] = $data;
            return $data;
        }

    }

    public static function realIP()
    {
        // Initialize ip to null
        $ip = null;
        
        // Check various headers in the following order of precedence
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // HTTP_X_FORWARDED_FOR can be a comma-separated list of IPs
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // If ip is null after all checks, default to 'UNKNOWN'
        if ($ip === null) {
            $ip = 'UNKNOWN';
        }
        if($ip == '::1' || $ip == '127.0.0.1') return '8.8.8.8';

        return $ip;
    }

    public static function comma2array(string|null $str)
    {
        if($str == null) return '';
        if(strpos($str, ',') === false) return $str;
        $explode = explode(',', $str);
        return array_map(fn($v) => trim($v), $explode);
    }

    public static function array2comma(array $arr)
    {
        return implode(',', $arr);
    }
    public static function buildTargetUrl(string $urls)
    {
        if(!preg_match("/\n/", $urls)) return $urls;
        $explode = explode("\n", $urls);
        $arr= array_map(fn($v) => trim($v), $explode);
        shuffle($arr);
        return $arr[rand(0, count($arr)-1)];
    }
    

}