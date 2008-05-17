/**
* Original class - SDDump.as
 * author: shinder lin
 * email: shinder.lin@gmail.com
 * see: http://qops.blogspot.com/2007/06/sddump-dump-as2.html
*/
class com.stephenbeattie.util.ObjectDump 
{
	static var n:Number = 0;
	static var str:String = '';

	static function echo(a) 
	{
		trace(go(a));
	}
	
	static function go(a) 
	{
		str = '';
		dumpIt(a);
		str = str.slice(0, str.length-1);
		return str;
	}
	
	static function dumpIt(a) 
	{
		if (a instanceof Array) {
			dumpArray(a);
		} else if (a instanceof Object) {
			dumpObject(a);
		} else {
			appendStr(a);
		}
	}
	
	static function dumpArray(ar:Array):Void 
	{
		n++;
		for (var o in ar) {
			if (ar[o] instanceof Object) {
				appendStr("["+o+"]:");
				dumpIt(ar[o]);
			} else {
				appendStr("["+o+"]:"+ar[o]);
			}
		}
		n--;
	}
	
	static function dumpObject(obj:Object):Void 
	{
		n++;
		for (var o in obj) {
			if (obj[o] instanceof Object) {
				appendStr(o+":");
				dumpIt(obj[o]);
			} else {
				appendStr(o+":"+obj[o]);
			}
		}
		n--;
	}
	
	static function appendStr(s):Void 
	{
		str += getSpaces()+s+'\n';
	}
	
	static function getSpaces():String 
	{
		var s:String = '';
		for (var i = 1; i<n; i++) {
			s += "  ";
		}
		return s;
	}
}