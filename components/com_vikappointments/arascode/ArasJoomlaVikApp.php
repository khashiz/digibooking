<?php

defined('_JEXEC') or die;

class ArasJoomlaVikApp{
    
     /* In The Name Of Allah */

    /** Software Hijri_Shamsi , Solar(Jalali) Date and Time
    Copyright(C)2011, Reza Gholampanahi , http://jdf.scr.ir
    version 2.55 :: 1391/08/24 = 1433/12/18 = 2012/11/15 */

    /*    F    */
    const COMPONENT_NAME = 'vikappointments';
    public static function jdate($format,$timestamp='',$none='',$time_zone='Asia/Tehran',$tr_num='en'){

      $T_sec=0;/* <= رفع خطاي زمان سرور ، با اعداد '+' و '-' بر حسب ثانيه */

      if($time_zone!='local')date_default_timezone_set(($time_zone=='')?'Asia/Tehran':$time_zone);
      $ts=$T_sec+(($timestamp=='' or $timestamp=='now')?time():ArasJoomlaVikApp::tr_num($timestamp));
      $date=explode('_',date('H_i_j_n_O_P_s_w_Y',$ts));
      list($j_y,$j_m,$j_d)= ArasJoomlaVikApp::gregorian_to_jalali($date[8],$date[3],$date[2]);
      $doy=($j_m<7)?(($j_m-1)*31)+$j_d-1:(($j_m-7)*30)+$j_d+185;
      $kab=($j_y%33%4-1==(int)($j_y%33*.05))?1:0;
      $sl=strlen($format);
      $out='';
      for($i=0; $i<$sl; $i++){
      $sub=substr($format,$i,1);
      if($sub=='\\'){
        $out.=substr($format,++$i,1);
        continue;
      }
      switch($sub){

        case'E':case'R':case'x':case'X':
        $out.='http://jdf.scr.ir';
        break;

        case'B':case'e':case'g':
        case'G':case'h':case'I':
        case'T':case'u':case'Z':
        $out.=date($sub,$ts);
        break;

        case'a':
        $out.=($date[0]<12)?'ق.ظ':'ب.ظ';
        break;

        case'A':
        //$out.=($date[0]<12)?'صبح':'عصر';
        if($date[0]<12){
            $out .= 'صبح';
        }elseif($date[0]>=12 AND $date[0]<=13){
             $out .= 'ظهر';
        }elseif($date[0]>13 AND $date[0]<20){
            $out .= 'بعد از ظهر';
        }else{
           $out .= 'شب'; 
        }
        
        break;

        case'b':
        $out.=(int)($j_m/3.1)+1;
        break;

        case'c':
        $out.=$j_y.'/'.$j_m.'/'.$j_d.' ،'.$date[0].':'.$date[1].':'.$date[6].' '.$date[5];
        break;

        case'C':
        $out.=(int)(($j_y+99)/100);
        break;

        case'd':
        $out.=($j_d<10)?'0'.$j_d:$j_d;
        break;

        case'D':
        $out.=ArasJoomlaVikApp::jdate_words(array('kh'=>$date[7]),' ');
        break;

        case'f':
        $out.=ArasJoomlaVikApp::jdate_words(array('ff'=>$j_m),' ');
        break;

        case'F':
        $out.=ArasJoomlaVikApp::jdate_words(array('mm'=>$j_m),' ');
        break;

        case'H':
        $out.=$date[0];
        break;

        case'i':
        $out.=$date[1];
        break;

        case'j':
        $out.=$j_d;
        break;

        case'J':
        $out.=ArasJoomlaVikApp::jdate_words(array('rr'=>$j_d),' ');
        break;

        case'k';
        $out.=ArasJoomlaVikApp::tr_num(100-(int)($doy/($kab+365)*1000)/10,$tr_num);
        break;

        case'K':
        $out.=ArasJoomlaVikApp::tr_num((int)($doy/($kab+365)*1000)/10,$tr_num);
        break;

        case'l':
        $out.=ArasJoomlaVikApp::jdate_words(array('rh'=>$date[7]),' ');
        break;

        case'L':
        $out.=$kab;
        break;

        case'm':
        $out.=($j_m>9)?$j_m:'0'.$j_m;
        break;

        case'M':
        $out.=ArasJoomlaVikApp::jdate_words(array('km'=>$j_m),' ');
        break;

        case'n':
        $out.=$j_m;
        break;

        case'N':
        $out.=$date[7]+1;
        break;

        case'o':
        $jdw=($date[7]==6)?0:$date[7]+1;
        $dny=364+$kab-$doy;
        $out.=($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y);
        break;

        case'O':
        $out.=$date[4];
        break;

        case'p':
        $out.=ArasJoomlaVikApp::jdate_words(array('mb'=>$j_m),' ');
        break;

        case'P':
        $out.=$date[5];
        break;

        case'q':
        $out.=ArasJoomlaVikApp::jdate_words(array('sh'=>$j_y),' ');
        break;

        case'Q':
        $out.=$kab+364-$doy;
        break;

        case'r':
        $key=ArasJoomlaVikApp::jdate_words(array('rh'=>$date[7],'mm'=>$j_m));
        $out.=$date[0].':'.$date[1].':'.$date[6].' '.$date[4]
        .' '.$key['rh'].'، '.$j_d.' '.$key['mm'].' '.$j_y;
        break;

        case's':
        $out.=$date[6];
        break;

        case'S':
        $out.='ام';
        break;

        case't':
        $out.=($j_m!=12)?(31-(int)($j_m/6.5)):($kab+29);
        break;

        case'U':
        $out.=$ts;
        break;

        case'v':
         $out.=ArasJoomlaVikApp::jdate_words(array('ss'=>substr($j_y,2,2)),' ');
        break;

        case'V':
        $out.=ArasJoomlaVikApp::jdate_words(array('ss'=>$j_y),' ');
        break;

        case'w':
        $out.=($date[7]==6)?0:$date[7]+1;
        break;

        case'W':
        $avs=(($date[7]==6)?0:$date[7]+1)-($doy%7);
        if($avs<0)$avs+=7;
        $num=(int)(($doy+$avs)/7);
        if($avs<4){
         $num++;
        }elseif($num<1){
         $num=($avs==4 or $avs==(($j_y%33%4-2==(int)($j_y%33*.05))?5:4))?53:52;
        }
        $aks=$avs+$kab;
        if($aks==7)$aks=0;
        $out.=(($kab+363-$doy)<$aks and $aks<3)?'01':(($num<10)?'0'.$num:$num);
        break;

        case'y':
        $out.=substr($j_y,2,2);
        break;

        case'Y':
        $out.=$j_y;
        break;

        case'z':
        $out.=$doy;
        break;

        default:$out.=$sub;
      }
     }
     return($tr_num!='en')?ArasJoomlaVikApp::tr_num($out,'fa','.'):$out;
    }

    /*    F    */
    public static function jstrftime($format,$timestamp='',$none='',$time_zone='Asia/Tehran',$tr_num='fa'){

     $T_sec=0;/* <= رفع خطاي زمان سرور ، با اعداد '+' و '-' بر حسب ثانيه */

     if($time_zone!='local')date_default_timezone_set(($time_zone=='')?'Asia/Tehran':$time_zone);
     $ts=$T_sec+(($timestamp=='' or $timestamp=='now')?time():ArasJoomlaVikApp::tr_num($timestamp));
     $date=explode('_',date('h_H_i_j_n_s_w_Y',$ts));
     list($j_y,$j_m,$j_d)=ArasJoomlaVikApp::gregorian_to_jalali($date[7],$date[4],$date[3]);
     $doy=($j_m<7)?(($j_m-1)*31)+$j_d-1:(($j_m-7)*30)+$j_d+185;
     $kab=($j_y%33%4-1==(int)($j_y%33*.05))?1:0;
     $sl=strlen($format);
     $out='';
     for($i=0; $i<$sl; $i++){
      $sub=substr($format,$i,1);
      if($sub=='%'){
        $sub=substr($format,++$i,1);
      }else{
        $out.=$sub;
        continue;
      }
      switch($sub){

        /* Day */
        case'a':
        $out.=ArasJoomlaVikApp::jdate_words(array('kh'=>$date[6]),' ');
        break;

        case'A':
        $out.=ArasJoomlaVikApp::jdate_words(array('rh'=>$date[6]),' ');
        break;

        case'd':
        $out.=($j_d<10)?'0'.$j_d:$j_d;
        break;

        case'e':
        $out.=($j_d<10)?' '.$j_d:$j_d;
        break;

        case'j':
        $out.=str_pad($doy+1,3,0,STR_PAD_LEFT);
        break;

        case'u':
        $out.=$date[6]+1;
        break;

        case'w':
        $out.=($date[6]==6)?0:$date[6]+1;
        break;

        /* Week */
        case'U':
        $avs=(($date[6]<5)?$date[6]+2:$date[6]-5)-($doy%7);
        if($avs<0)$avs+=7;
        $num=(int)(($doy+$avs)/7)+1;
        if($avs>3 or $avs==1)$num--;
        $out.=($num<10)?'0'.$num:$num;
        break;

        case'V':
        $avs=(($date[6]==6)?0:$date[6]+1)-($doy%7);
        if($avs<0)$avs+=7;
        $num=(int)(($doy+$avs)/7);
        if($avs<4){
         $num++;
        }elseif($num<1){
         $num=($avs==4 or $avs==(($j_y%33%4-2==(int)($j_y%33*.05))?5:4))?53:52;
        }
        $aks=$avs+$kab;
        if($aks==7)$aks=0;
        $out.=(($kab+363-$doy)<$aks and $aks<3)?'01':(($num<10)?'0'.$num:$num);
        break;

        case'W':
        $avs=(($date[6]==6)?0:$date[6]+1)-($doy%7);
        if($avs<0)$avs+=7;
        $num=(int)(($doy+$avs)/7)+1;
        if($avs>3)$num--;
        $out.=($num<10)?'0'.$num:$num;
        break;

        /* Month */
        case'b':
        case'h':
        $out.=ArasJoomlaVikApp::jdate_words(array('km'=>$j_m),' ');
        break;

        case'B':
        $out.=ArasJoomlaVikApp::jdate_words(array('mm'=>$j_m),' ');
        break;

        case'm':
        $out.=($j_m>9)?$j_m:'0'.$j_m;
        break;

        /* Year */
        case'C':
        $out.=substr($j_y,0,2);
        break;

        case'g':
        $jdw=($date[6]==6)?0:$date[6]+1;
        $dny=364+$kab-$doy;
        $out.=substr(($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y),2,2);
        break;    

        case'G':
        $jdw=($date[6]==6)?0:$date[6]+1;
        $dny=364+$kab-$doy;
        $out.=($jdw>($doy+3) and $doy<3)?$j_y-1:(((3-$dny)>$jdw and $dny<3)?$j_y+1:$j_y);
        break;

        case'y':
        $out.=substr($j_y,2,2);
        break;

        case'Y':
        $out.=$j_y;
        break;

        /* Time */
        case'H':
        $out.=$date[1];
        break;

        case'I':
        $out.=$date[0];
        break;

        case'l':
        $out.=($date[0]>9)?$date[0]:' '.(int)$date[0];
        break;

        case'M':
        $out.=$date[2];
        break;

        case'p':
        $out.=($date[1]<12)?'قبل از ظهر':'بعد از ظهر';
        break;

        case'P':
        $out.=($date[1]<12)?'ق.ظ':'ب.ظ';
        break;

        case'r':
        $out.=$date[0].':'.$date[2].':'.$date[5].' '.(($date[1]<12)?'قبل از ظهر':'بعد از ظهر');
        break;

        case'R':
        $out.=$date[1].':'.$date[2];
        break;

        case'S':
        $out.=$date[5];
        break;

        case'T':
        $out.=$date[1].':'.$date[2].':'.$date[5];
        break;

        case'X':
        $out.=$date[0].':'.$date[2].':'.$date[5];
        break;

        case'z':
        $out.=date('O',$ts);
        break;

        case'Z':
        $out.=date('T',$ts);
        break;

        /* Time and Date Stamps */
        case'c':
        $key=ArasJoomlaVikApp::jdate_words(array('rh'=>$date[6],'mm'=>$j_m));
        $out.=$date[1].':'.$date[2].':'.$date[5].' '.date('P',$ts)
        .' '.$key['rh'].'، '.$j_d.' '.$key['mm'].' '.$j_y;
        break;

        case'D':
        $out.=substr($j_y,2,2).'/'.(($j_m>9)?$j_m:'0'.$j_m).'/'.(($j_d<10)?'0'.$j_d:$j_d);
        break;

        case'F':
        $out.=$j_y.'-'.(($j_m>9)?$j_m:'0'.$j_m).'-'.(($j_d<10)?'0'.$j_d:$j_d);
        break;

        case's':
        $out.=$ts;
        break;

        case'x':
        $out.=substr($j_y,2,2).'/'.(($j_m>9)?$j_m:'0'.$j_m).'/'.(($j_d<10)?'0'.$j_d:$j_d);
        break;

        /* Miscellaneous */
        case'n':
        $out.="\n";
        break;

        case't':
        $out.="\t";
        break;

        case'%':
        $out.='%';
        break;

        default:$out.=$sub;
      }
     }
     return($tr_num!='en')?ArasJoomlaVikApp::tr_num($out,'fa','.'):$out;
    }

     /*    F    */
   /* public static function jmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$is_dst=-1){
     $h=ArasJoomlaVikApp::tr_num($h); $m=ArasJoomlaVikApp::tr_num($m); $s=ArasJoomlaVikApp::tr_num($s); $jm=ArasJoomlaVikApp::tr_num($jm); $jd=ArasJoomlaVikApp::tr_num($jd); $jy=ArasJoomlaVikApp::tr_num($jy);
     if($h=='' and $m=='' and $s=='' and $jm=='' and $jd=='' and $jy==''){
        return mktime();
     }else{
        list($year,$month,$day)=ArasJoomlaVikApp::jalali_to_gregorian($jy,$jm,$jd);
        return mktime($h,$m,$s,$month,$day,$year,$is_dst);
     }
    }*/

   public static function jmktime($h='',$m='',$s='',$jm='',$jd='',$jy='',$none='',$timezone='Asia/Tehran'){
         
          if($timezone!='local')date_default_timezone_set($timezone);
          if($h===''){
          return time();
          }else{
            list($h,$m,$s,$jm,$jd,$jy)=explode('_',ArasJoomlaVikApp::tr_num($h.'_'.$m.'_'.$s.'_'.$jm.'_'.$jd.'_'.$jy));
          if($m===''){
           return mktime($h);
          }else{
           if($s===''){
            return mktime($h,$m);
           }else{
            if($jm===''){
             return mktime($h,$m,$s);
            }else{
             $jdate=explode('_',ArasJoomlaVikApp::jdate('Y_j','','',$timezone,'en'));
             if($jd===''){
              list($gy,$gm,$gd)=ArasJoomlaVikApp::jalali_to_gregorian($jdate[0],$jm,$jdate[1]);
              return mktime($h,$m,$s,$gm);
             }else{
              if($jy===''){
               list($gy,$gm,$gd)=ArasJoomlaVikApp::jalali_to_gregorian($jdate[0],$jm,$jd);
               return mktime($h,$m,$s,$gm,$gd);
              }else{
               list($gy,$gm,$gd)=ArasJoomlaVikApp::jalali_to_gregorian($jy,$jm,$jd);
               return mktime($h,$m,$s,$gm,$gd,$gy);
              }
             }
            }
           }
          }
         }
    }
    
    /*    F    */
   public static function jgetdate($timestamp='',$none='',$tz='Asia/Tehran',$tn='en'){
     $ts=($timestamp=='')?time():ArasJoomlaVikApp::tr_num($timestamp);
     $jdate=explode('_',ArasJoomlaVikApp::jdate('F_G_i_j_l_n_s_w_Y_z',$ts,'',$tz,$tn));
     return array(
        'seconds'=>ArasJoomlaVikApp::tr_num((int)ArasJoomlaVikApp::tr_num($jdate[6]),$tn),
        'minutes'=>ArasJoomlaVikApp::tr_num((int)ArasJoomlaVikApp::tr_num($jdate[2]),$tn),
        'hours'=>$jdate[1],
        'mday'=>$jdate[3],
        'wday'=>$jdate[7],
        'mon'=>$jdate[5],
        'year'=>$jdate[8],
        'yday'=>$jdate[9],
        'weekday'=>$jdate[4],
        'month'=>$jdate[0],
        0=>ArasJoomlaVikApp::tr_num($ts,$tn)
     );
    }

    /*    F    */
   public static function jcheckdate($jm,$jd,$jy){
     $jm=ArasJoomlaVikApp::tr_num($jm); $jd=ArasJoomlaVikApp::tr_num($jd); $jy=ArasJoomlaVikApp::tr_num($jy);
     $l_d=($jm==12)?(($jy%33%4-1==(int)($jy%33*.05))?30:29):31-(int)($jm/6.5);
     return($jm>0 and $jd>0 and $jy>0 and $jm<13 and $jd<=$l_d)?true:false;
    }

    /*    F    */
   public static function tr_num($str,$mod='en',$mf='٫'){
     $num_a=array('0','1','2','3','4','5','6','7','8','9','.');
     $key_a=array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹',$mf);
     return($mod=='fa')?str_replace($num_a,$key_a,$str):str_replace($key_a,$num_a,$str);
    }

    /*    F    */
   public static function jdate_words($array,$mod=''){
     foreach($array as $type=>$num){
      $num=(int)ArasJoomlaVikApp::tr_num($num);
      switch($type){

        case'ss':
        $sl=strlen($num);
        $xy3=substr($num,2-$sl,1);
        $h3=$h34=$h4='';
        if($xy3==1){
         $p34='';
         $k34=array('ده','یازده','دوازده','سیزده','چهارده','پانزده','شانزده','هفده','هجده','نوزده');
         $h34=$k34[substr($num,2-$sl,2)-10];
        }else{
         $xy4=substr($num,3-$sl,1);
         $p34=($xy3==0 or $xy4==0)?'':' و ';
         $k3=array('','','بیست','سی','چهل','پنجاه','شصت','هفتاد','هشتاد','نود');
         $h3=$k3[$xy3];
         $k4=array('','یک','دو','سه','چهار','پنج','شش','هفت','هشت','نه');
         $h4=$k4[$xy4];
        }
        $array[$type]=(($num>99)?str_ireplace(array('12','13','14','19','20')
        ,array('هزار و دویست','هزار و سیصد','هزار و چهارصد','هزار و نهصد','دوهزار')
        ,substr($num,0,2)).((substr($num,2,2)=='00')?'':' و '):'').$h3.$p34.$h34.$h4;
        break;

        case'mm':
        $key=array
        ('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
        $array[$type]=$key[$num-1];
        break;

        case'rr':
        $key=array('یک','دو','سه','چهار','پنج','شش','هفت','هشت','نه','ده','یازده','دوازده','سیزده',
        'چهارده','پانزده','شانزده','هفده','هجده','نوزده','بیست','بیست و یک','بیست و دو','بیست و سه',
        'بیست و چهار','بیست و پنج','بیست و شش','بیست و هفت','بیست و هشت','بیست و نه','سی','سی و یک');
        $array[$type]=$key[$num-1];
        break;

        case'rh':
        $key=array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه');
        $array[$type]=$key[$num];
        break;

        case'sh':
        $key=array('مار','اسب','گوسفند','میمون','مرغ','سگ','خوک','موش','گاو','پلنگ','خرگوش','نهنگ');
        $array[$type]=$key[$num%12];
        break;

        case'mb':
        $key=array('حمل','ثور','جوزا','سرطان','اسد','سنبله','میزان','عقرب','قوس','جدی','دلو','حوت');
        $array[$type]=$key[$num-1];
        break;

        case'ff':
        $key=array('بهار','تابستان','پاییز','زمستان');
        $array[$type]=$key[(int)($num/3.1)];
        break;

        case'km':
        $key=array('فر','ار','خر','تی‍','مر','شه‍','مه‍','آب‍','آذ','دی','به‍','اس‍');
        $array[$type]=$key[$num-1];
        break;

        case'kh':
        $key=array('ی','د','س','چ','پ','ج','ش');
        $array[$type]=$key[$num];
        break;

        default:$array[$type]=$num;
      }
     }
     return($mod=='')?$array:implode($mod,$array);
    }

    /** Convertor from and to Gregorian and Jalali (Hijri_Shamsi,Solar) public static functions
    Copyright(C)2011, Reza Gholampanahi [ http://jdf.scr.ir/jdf ] version 2.50 */

    /*    F    */
   public static function gregorian_to_jalali($g_y,$g_m,$g_d,$mod=''){
        $g_y=ArasJoomlaVikApp::tr_num($g_y); $g_m=ArasJoomlaVikApp::tr_num($g_m); $g_d=ArasJoomlaVikApp::tr_num($g_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
     $d_4=$g_y%4;
     $g_a=array(0,0,31,59,90,120,151,181,212,243,273,304,334);
     $doy_g=$g_a[(int)$g_m]+$g_d;
     if($d_4==0 and $g_m>2)$doy_g++;
     $d_33=(int)((($g_y-16)%132)*.0305);
     $a=($d_33==3 or $d_33<($d_4-1) or $d_4==0)?286:287;
     $b=(($d_33==1 or $d_33==2) and ($d_33==$d_4 or $d_4==1))?78:(($d_33==3 and $d_4==0)?80:79);
     if((int)(($g_y-10)/63)==30){$a--;$b++;}
     if($doy_g>$b){
      $jy=$g_y-621; $doy_j=$doy_g-$b;
     }else{
      $jy=$g_y-622; $doy_j=$doy_g+$a;
     }
     if($doy_j<187){
      $jm=(int)(($doy_j-1)/31); $jd=$doy_j-(31*$jm++);
     }else{
      $jm=(int)(($doy_j-187)/30); $jd=$doy_j-186-($jm*30); $jm+=7;
     }
     return($mod=='')?array($jy,$jm,$jd):$jy.$mod.$jm.$mod.$jd;
    }

    /*    F    */
   public static function jalali_to_gregorian($j_y,$j_m,$j_d,$mod=''){
        $j_y=ArasJoomlaVikApp::tr_num($j_y); $j_m=ArasJoomlaVikApp::tr_num($j_m); $j_d=ArasJoomlaVikApp::tr_num($j_d);/* <= :اين سطر ، جزء تابع اصلي نيست */
     $d_4=($j_y+1)%4;
     $doy_j=($j_m<7)?(($j_m-1)*31)+$j_d:(($j_m-7)*30)+$j_d+186;
     $d_33=(int)((($j_y-55)%132)*.0305);
     $a=($d_33!=3 and $d_4<=$d_33)?287:286;
     $b=(($d_33==1 or $d_33==2) and ($d_33==$d_4 or $d_4==1))?78:(($d_33==3 and $d_4==0)?80:79);
     if((int)(($j_y-19)/63)==20){$a--;$b++;}
     if($doy_j<=$a){
      $gy=$j_y+621; $gd=$doy_j+$b;
     }else{
      $gy=$j_y+622; $gd=$doy_j-$a;
     }
     foreach(array(0,31,($gy%4==0)?29:28,31,30,31,30,31,31,30,31,30,31) as $gm=>$v){
      if($gd<=$v)break;
      $gd-=$v;
     }
     return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;
    }

   public static function is_Kabise($year){
        $mod = ($year % 33);

        if(($mod == 1) or ($mod == 5) or ($mod == 9) or ($mod == 13) or ($mod == 17) or ($mod == 22) or ($mod == 26) or ($mod == 30))
        {
            return 1;
        }

        return 0;
    }
   public static function getMonthName($num){
    
      switch ($num)
        {

            case 1:
              $month = "فروردین";
             break;
            case 2:
              $month = "اردیبهشت";
             break;
            case 3:
              $month = "خرداد";
             break;
            case 4:
              $month = "تیر";
             break;
            case 5:
              $month = "مرداد";
             break;
            case 6:
              $month = "شهریور";
             break;
            case 7:
              $month = "مهر";
             break;
            case 8:
              $month = "آبان";
             break;
            case 9:
              $month = "آذر";
             break;
            case 10:
              $month = "دی";
             break;
            case 11:
              $month = "بهمن";
             break;
            case 12:
              $month = "اسفند";
             break;
        }
        
        return $month;
   }
   
   public static function getArasday($month,$year = 0){
        $day = 0;
        if ($month <= 6) {
            $day = 31;
        }elseif ($month <= 11) {
            $day = 30;
        }else {
            $day = 29;
        }
       if((($year + 1)%4) == 0 && $month == 12) {
          $day = 30;
       }
       return $day;
   }
   
   public static function datePicker($css=true,$helper=true,$time=false,$arascode=false){
      $doc = JFactory::getDocument();
      $doc->addScript(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/js/arascode_datepicker.min.js');
      if($helper) $doc->addScript(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/js/arascode_helper.js'); 
      if($css){ 
          $doc->addStyleSheet(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/css/jquery-bootstrap-datepicker.css');
          $doc->addStyleSheet(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/css/theme/melon.datepicker.css');
          $doc->addStyleSheet(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/css/jquery-ui.min.css');
      } 
      if($arascode) $doc->addStyleSheet(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/css/arascode.css');
      if($time){
           $doc->addScript(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/js/jquery-ui-time.js');
           $doc->addScript(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/js/jquery-ui-i18n.js');
           $doc->addScript(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/js/jquery-ui-slider.js');
           $doc->addStyleSheet(JUri::root().'components/com_'.self::COMPONENT_NAME.'/arascode/calendar/css/time.css');
      } 
   }
   
   public static function setDatePicker($id,$format,$value=null,$attributes=null){

       $def = '';
       if($value){
          //$def = "defaultDate: '$value',";
       }
      $js = '<script type="text/javascript">
            
           jQuery(function(){
                var today = new JalaliDate();
                jQuery("#'.$id.':input").datepicker({
                    showOn: "both",
                    dateFormat: "yy'.$format.'mm'.$format.'dd",
                    changeYear: true,
                    changeMonth: true,
                    isRTL: true,
                    buttonImageOnly:true,
                    buttonText:"",
                    showButtonPanel: true,
                    onSelect: function(dateText) {
                        '.$attributes.'
                    },
                    '.$def.'
                });
                
           });
        
        </script>';
        return $js;
   }
    
    
    
   public static function addCalendar($input,$area,$format=NULL){
      $for = $format ? $format:'%Y-%m-%d %H:%M:%S';
      $calendar = '
      <script type="text/javascript">
               Calendar.setup({
                           inputField     :    "'.$input.'",
                           displayArea    :    "'.$area.'",
                           ifFormat       :    "'.$for.'",
                           dateType       :    "jalali",
                           ifDateType     :    "gregorian",
                           weekNumbers    : false
                            });
         </script>                   
                            '; 
      return $calendar;                                          
   }
  
   public static function dateJoomlaMode($date,$num){
      
               switch ($num) {
            case 1:
                $format = JText::_('DATE_FORMAT_LC1');
                break;
            case 2:
                $format = JText::_('DATE_FORMAT_LC2');
                break;
            case 3:
                $format = JText::_('DATE_FORMAT_LC3');
                break;
            case 4:
                $format = JText::_('DATE_FORMAT_LC4');
                break;
            default:
                $format = JText::_('DATE_FORMAT_LC3');
        }
        
      $date = JHTML::_('date',$date,$format);
      return $date; 
   }
   public static function getCustomFormat($date,$format){
       return JHtml::_('date',$date,$format); 
   }
  
   public static function weekDay($i){
      $days = array(
             0=>'شنبه',
             1=>'یکشنبه',
             2=>'دوشنبه',
             3=>'سه شنبه',
             4=>'چهارشنبه',
             5=>'پنجشنبه',
             6=>'جمعه'
      );
      return $days[$i];
   }

   public static function weekDay2($i){
      $days = array(
             1=>'شنبه',
             2=>'یکشنبه',
             3=>'دوشنبه',
             4=>'سه شنبه',
             5=>'چهارشنبه',
             6=>'پنجشنبه',
             0=>'جمعه'
      );
      return $days[$i];
   }
  
   public static function generateArchive($ts){
      
      $y = self::jdate( 'Y' , $ts);
      $num_month = self::jdate( 'n' , $ts);
      $year = self::jdate( 'F Y' , $ts);
      $day = self::getArasday($num_month,$y);
      $is = self::is_Kabise($y);
      if($is AND $num_month==12){
          $day = 30;
      }
      $start = self::jalali_to_gregorian( $y , $num_month , '','-' );  
      $start  = "$start 00:00:00"; 
      $end = self::jalali_to_gregorian( $y , $num_month , $day,'-' );  
      $end  = "$end 23:59:59";
      return array('start'=>$start,'end'=>$end);
   }
   
   public static function weekDaysShort(){
       return [
            'ش',
            'ی',
            'د',
            'س',
            'چ',
            'پ',
            'ج'
       ];
   } 
   
   public static function weekDays(){
       return [
            'شنبه',
            'یکشنبه',
            'دوشنبه',
            'سه شنبه',
            'چهارشنبه',
            'پنجشنبه',
            'جمعه'
       ];
   }

   public static function jDateTimeToTimestamp($dateTime){
       $param = explode(' ',$dateTime);
       $date = explode('-',$param[0]);
       $time = explode(':',$param[1]);
       return self::jmktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
   }

   public static function jDateToTimestamp($date,$df_separator){
       $date = explode($df_separator,$date);
       return self::jmktime(0,0,0,$date[1],$date[2],$date[0]);
   }

   public static function fixDayIndex($i){
       return [
           0 => 6,
           1 => 0,
           2 => 1,
           3 => 2,
           4 => 3,
           5 => 4,
           6 => 5,
       ][$i];
   }



}
function dddd($i){echo '<pre style="direction:ltr">';var_dump($i);echo '</pre>';die();}
function addDay($timestamp,$count){
    return $timestamp + ((24*3600)*$count);
}
function include_jdate(){
    $files = [
        'Notowo.php',
        'JalaliValidator.php',
        'Verta.php',
    ];
    foreach($files as $file){
        require_once( JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikappointments'. DIRECTORY_SEPARATOR. 'arascode' . DIRECTORY_SEPARATOR .'JDate'.DIRECTORY_SEPARATOR.$file);
    }
    return;
}
?>