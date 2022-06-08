
var fCol='#000000'; //face/number colour.
var dCol='#cccccc'; //dot colour.
var hCol='#000000'; //hours colour.
var mCol='#000000'; //minutes colour.
var sCol='#ff0000'; //seconds colour.
var cCol='#000000'; //date colour.
var aCol='#999999'; //am-pm colour.
var bCol='#ffffff'; //select/form background colour.
var tCol='#000000'; //select/form text colour.

//Alter nothing below! Alignments will be lost!
var y=87;
var xpos=60;
var h=4;
var m=5;
var s=6;
var cf=new Array();
var cd=new Array();
var ch=new Array();
var cm=new Array();
var cs=new Array();
var face='3 4 5 6 7 8 9 10 11 12 1 2';
face=face.split(' ');
var n=face.length;
var e=360/n;
var hDims=7;
var zone=0;
var isItLocal=true;
var ampm='';
var daysInMonth=31;
var addHours;
var oddMinutes;
var getOddMinutes;
var addOddMinutes;
var mon=new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

function lcl(currIndex, localState) {
	zone=document.frmtimezone.clockcity.options[currIndex].value;
	isItLocal=localState;
	let plusMinus=(zone.charAt(0) == '-');
	oddMinutes=(zone.indexOf('.') != -1);
	if (oddMinutes) {
		getOddMinutes=zone.substring(zone.indexOf('.')+1, zone.length);
	}
	addHours=(oddMinutes) ? parseInt(zone.substring(0, zone.indexOf('.'))) : parseInt(zone);
	if (plusMinus) {
		addOddMinutes=(oddMinutes)?parseInt(-getOddMinutes):0;
	} else {
		addOddMinutes=(oddMinutes)?parseInt(getOddMinutes):0;
	}
	set_cookie('timezone', currIndex);
}

function ClockAndAssign() {
	let hourAdjust=0;
	let dayAdjust=0;
	let monthAdjust=0;
	let now=new Date();
	let secs=now.getSeconds();
	let sec=Math.PI*(secs-15)/30;
	let mins=(isItLocal)?now.getMinutes():now.getUTCMinutes();
	if (oddMinutes) {
		mins=mins+addOddMinutes;
	}
	let min=Math.PI*(mins-15)/30;
	if (mins<0) {
		mins+=60;
		hourAdjust=-1;
	}
	if (mins>59) {
		mins-=60;
		hourAdjust=1;
	}

	let hr=(isItLocal)?now.getHours()+hourAdjust:now.getUTCHours()+addHours+hourAdjust;
	let hrs=Math.PI*(hr-3)/6+Math.PI*parseInt(now.getMinutes())/360;

	if (!isItLocal) {
		if (addHours<0) {
			if (now.getUTCHours()+parseInt(addHours)<0) {
				dayAdjust-=1;
			}
		} else {
			if (now.getUTCHours()+parseInt(addHours)>23) {
				dayAdjust+=1;
			}
		}
	}

	let day=now.getDate()+dayAdjust;
	if (day<1) {
		day+=daysInMonth;
		monthAdjust=-1;
	}
	if (day>daysInMonth) {
		day-=daysInMonth;
		monthAdjust=1;
	}

	let month=parseInt(now.getMonth()+1+monthAdjust);

	if (month==2) {
		daysInMonth=28;
	}
	let year=now.getYear();
	if (year<2000) {
		year=year+1900;
	}
	let leap_year=(year%4==0)?true:false;
	if (leap_year&&month==2) {
		daysInMonth=29;
	}
	if (month<1) {
		month+=12;
		year--;
	}
	if (month>12) {
		month-=12;
		year++;
	}
	let todaysDate=mon[month-1]+' '+day+', '+year;

	if (hr<0) {
		hr+=24;
	}
	if (hr>23) {
		hr-=24;
	}

	ampm=(hr>11) ? 'PM' : 'AM';

	for (var i=0; i<s; i++) {
		cs[i].top=y+(i*hDims)*Math.sin(sec)+'px';
		cs[i].left=xpos+(i*hDims)*Math.cos(sec)+'px';
	}
	for (i=0; i<m; i++) {
		cm[i].top=y+(i*hDims)*Math.sin(min)+'px';
		cm[i].left=xpos+(i*hDims)*Math.cos(min)+'px';
	}
	for (i=0; i<h; i++) {
		ch[i].top=y+(i*hDims)*Math.sin(hrs)+'px';
		ch[i].left=xpos+(i*hDims)*Math.cos(hrs)+'px';
	}

	document.getElementById('amOrPm').firstChild.data=ampm;

	if (hr==0) {
		hr=12;
	} else if (hr>12) {
		hr-=12;
	}

	if (mins.toString().length==1) {
		mins='0'+mins;
	}

	document.getElementById('theDate').firstChild.data=todaysDate+' '+hr+':'+mins+' '+ampm;
	setTimeout('ClockAndAssign()', 100);
}
