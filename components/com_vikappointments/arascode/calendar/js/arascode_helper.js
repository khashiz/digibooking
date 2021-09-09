/*!
 * ArasJoomla Helper 
 *
 * Copyright 2014-2017, ArasJoomla
 * http://arasjoomla.ir
 * arasjoomla@gmail.com
 */
 jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)){
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

function arascode_jalali_to_gregorian(jy,jm,jd,arascode='/'){
	gy=(jy<=979)?621:1600;
	jy-=(jy<=979)?0:979;
	days=(365*jy) +((parseInt(jy/33))*8) +(parseInt(((jy%33)+3)/4)) +78 +jd +((jm<7)?(jm-1)*31:((jm-7)*30)+186);
	gy+=400*(parseInt(days/146097));
	days%=146097;
	if(days > 36524){
	gy+=100*(parseInt(--days/36524));
	days%=36524;
	if(days >= 365)days++;
	}
	gy+=4*(parseInt((days)/1461));
	days%=1461;
	gy+=parseInt((days-1)/365);
	if(days > 365)days=(days-1)%365;
	gd=days+1;
	sal_a=[0,31,((gy%4==0 && gy%100!=0) || (gy%400==0))?29:28,31,30,31,30,31,31,30,31,30,31];
	for(gm=0;gm<13;gm++){
	v=sal_a[gm];
	if(gd <= v)break;
	gd-=v;
	}
	//return gy+arascode+gm+arascode+gd; 
    if(gd<10) gd = '0'+gd;		
    if(gm<10) gm = '0'+gm;		
	return gd+arascode+gm+arascode+gy;  
}

function setValueAfterConvertJalali(date){
	
	        var date_split = date.split("/");
            var convert_date = arascode_jalali_to_gregorian(parseInt(date_split[2]),parseInt(date_split[1]),parseInt(date_split[0]));
			return convert_date;
}