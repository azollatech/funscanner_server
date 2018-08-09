<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    public function dateOfBirthToZodiac($birthdate, $lang = 'en') {
        $zodiac = '';
        list ($year, $month, $day) = explode ('-', $birthdate); 

        if ($lang == 'zh-hk') {
         
            if ( ( $month == 3 && $day > 20 ) || ( $month == 4 && $day < 20 ) ) { $zodiac = "白羊座"; } 
            
            elseif ( ( $month == 4 && $day > 19 ) || ( $month == 5 && $day < 21 ) ) { $zodiac = "金牛座"; } 
            
            elseif ( ( $month == 5 && $day > 20 ) || ( $month == 6 && $day < 21 ) ) { $zodiac = "雙子座"; } 
            
            elseif ( ( $month == 6 && $day > 20 ) || ( $month == 7 && $day < 23 ) ) { $zodiac = "巨蟹座"; } 
            
            elseif ( ( $month == 7 && $day > 22 ) || ( $month == 8 && $day < 23 ) ) { $zodiac = "獅子座"; } 
            
            elseif ( ( $month == 8 && $day > 22 ) || ( $month == 9 && $day < 23 ) ) { $zodiac = "處女座"; } 
            
            elseif ( ( $month == 9 && $day > 22 ) || ( $month == 10 && $day < 23 ) ) { $zodiac = "天秤座"; } 
            
            elseif ( ( $month == 10 && $day > 22 ) || ( $month == 11 && $day < 22 ) ) { $zodiac = "天蠍座"; } 
            
            elseif ( ( $month == 11 && $day > 21 ) || ( $month == 12 && $day < 22 ) ) { $zodiac = "射手座"; } 
            
            elseif ( ( $month == 12 && $day > 21 ) || ( $month == 1 && $day < 20 ) ) { $zodiac = "摩羯座"; } 
            
            elseif ( ( $month == 1 && $day > 19 ) || ( $month == 2 && $day < 19 ) ) { $zodiac = "水瓶座"; } 
            
            elseif ( ( $month == 2 && $day > 18 ) || ( $month == 3 && $day < 21 ) ) { $zodiac = "雙魚座"; } 

        } else {
        
            if ( ( $month == 3 && $day > 20 ) || ( $month == 4 && $day < 20 ) ) { $zodiac = "Aries"; } 

            elseif ( ( $month == 4 && $day > 19 ) || ( $month == 5 && $day < 21 ) ) { $zodiac = "Taurus"; } 

            elseif ( ( $month == 5 && $day > 20 ) || ( $month == 6 && $day < 21 ) ) { $zodiac = "Gemini"; } 

            elseif ( ( $month == 6 && $day > 20 ) || ( $month == 7 && $day < 23 ) ) { $zodiac = "Cancer"; } 

            elseif ( ( $month == 7 && $day > 22 ) || ( $month == 8 && $day < 23 ) ) { $zodiac = "Leo"; } 

            elseif ( ( $month == 8 && $day > 22 ) || ( $month == 9 && $day < 23 ) ) { $zodiac = "Virgo"; } 

            elseif ( ( $month == 9 && $day > 22 ) || ( $month == 10 && $day < 23 ) ) { $zodiac = "Libra"; } 

            elseif ( ( $month == 10 && $day > 22 ) || ( $month == 11 && $day < 22 ) ) { $zodiac = "Scorpio"; } 

            elseif ( ( $month == 11 && $day > 21 ) || ( $month == 12 && $day < 22 ) ) { $zodiac = "Sagittarius"; } 

            elseif ( ( $month == 12 && $day > 21 ) || ( $month == 1 && $day < 20 ) ) { $zodiac = "Capricorn"; } 

            elseif ( ( $month == 1 && $day > 19 ) || ( $month == 2 && $day < 19 ) ) { $zodiac = "Aquarius"; } 

            elseif ( ( $month == 2 && $day > 18 ) || ( $month == 3 && $day < 21 ) ) { $zodiac = "Pisces"; } 

        }

        return $zodiac;
    }
    public function dateOfBirthToAge($birthdate) {
        //date in mm/dd/yyyy format; or it can be in other formats as well
        // $birthdate = "2016-08-01";
        //explode the date to get month, day and year
        $birthdate = explode("-", $birthdate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthdate[1], $birthdate[2], $birthdate[0]))) > date("md")
        ? ((date("Y") - $birthdate[0]) - 1)
        : (date("Y") - $birthdate[0]));
        return $age;
    }
    public function time_elapsed_string($datetime, $full = false, $lang = 'en') {
        date_default_timezone_set('Asia/Hong_Kong');
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        if ($lang == 'zh-hk') {
            $string = array(
                'y' => '年',
                'm' => '個月',
                'w' => '星期',
                'd' => '日',
                'h' => '小時',
                'i' => '分鐘',
                's' => '秒',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v;
                } else {
                    unset($string[$k]);
                }
            }
            if (!$full) $string = array_slice($string, 0, 1);
            if (!$diff->invert) {
                // return $string ? implode(', ', $string) . ' ago' : 'just now';
                return $string ? implode(', ', $string) . '後': '現在';
            } else {
                // return $string ? implode(', ', $string) . ' ago' : 'just now';
                return $string ? implode(', ', $string) . '前' : '剛剛';
            }
        } else {
            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }
            if (!$full) $string = array_slice($string, 0, 1);
            if (!$diff->invert) {
                // return $string ? implode(', ', $string) . ' ago' : 'just now';
                return $string ? 'In ' . implode(', ', $string): 'now';
            } else {
                // return $string ? implode(', ', $string) . ' ago' : 'just now';
                return $string ? implode(', ', $string) . ' ago' : 'just now';
            }
        }
    }
    public function time_elapsed_string_long($datetime, $lang = 'en') {
        date_default_timezone_set('Asia/Hong_Kong');
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        if ($lang == 'zh-hk') {
            $string = array(
                'y' => '年',
                'm' => '個月',
                'w' => '星期',
                'd' => '日',
                'h' => '小時',
                'i' => '分鐘',
                's' => '秒',
            );
            if ($diff->y > 0 || $diff->m > 0 || $diff->w > 0 || $diff->d > 1) {
                return $ago->format('n\月j\日 g:i A');;
            }
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v;
                } else {
                    unset($string[$k]);
                }
            }
            if (!$diff->invert) {
                if ($diff->d > 0) {
                    return "明天 " . $ago->format('g:i A');
                }
                $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . '後': '現在';
            } else {
                if ($diff->d > 0) {
                    return "昨天 " . $ago->format('g:i A');
                }
                $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . '前' : '剛剛';
            }
        } else {
            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            if ($diff->y > 0 || $diff->m > 0 || $diff->w > 0 || $diff->d > 1) {
                return $ago->format('F j \a\t g:i A');;
            }
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }
            if (!$diff->invert) {
                if ($diff->d > 0) {
                    return "Tomorrow at " . $ago->format('g:i A');
                }
                $string = array_slice($string, 0, 1);
                return $string ? 'In ' . implode(', ', $string): 'now';
            } else {
                if ($diff->d > 0) {
                    return "Yesterday at " . $ago->format('g:i A');
                }
                $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . ' ago' : 'just now';
            }
        }
    }
}
