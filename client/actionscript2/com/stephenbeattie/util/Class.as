
// I'd love to remember where I got this code from.  Somewhere on flashcoders no doubt.  
// I've googled extensively so if anyone can lay claim to it I'll be glad to add a credit here - SB

class com.stephenbeattie.util.Class
{
	public static function getClass ( obj, includePackage_bool:Boolean ): String
	{
		//the difference between function(){ }; and MovieClip
		if ( typeof obj == "function" )
		{
			var result_str:String = getClassName ( obj, null, includePackage_bool );
			if (result_str != null)
			{
				return result_str;
			}
		}
		if (obj.__constructor__)
		{
			return getClassName ( obj.__constructor__, null, includePackage_bool );
		}
		else if ( obj.constructor )
		{	
			return;
			getClassName ( Function (obj.constructor), null, includePackage_bool );
		}
		else
		{	return null;
		}
	}
	
	private static function protect ( package_obj:Object, unprotected_array:Array ): Void
	{
		_global.ASSetPropFlags (package_obj, null, 7, true);
		_global.ASSetPropFlags (package_obj, unprotected_array, 6, true);
	}
	
	private static function getClassName ( constructor:Function, package_obj:Object, includePackage_bool:Boolean ): String
	{
		package_obj = package_obj ? package_obj : _global;
		
		var unprotected_array:Array = new Array ();
		for ( var i in package_obj )
		{	unprotected_array.push (i);
		}
		
		_global.ASSetPropFlags (package_obj, null, 6, true);
		//dont go recursive if its not nescessary
		for (var i in package_obj)
		{
			if (package_obj[i] == constructor)
			{	protect (package_obj, unprotected_array);
				return i;
			}
		}
		for (var i in package_obj)
		{
			if (typeof package_obj[i] == "object")
			{
				var result_str:String = getClassName (constructor, package_obj[i], includePackage_bool);
				if (result_str != null)
				{
					if (includePackage_bool)
					{	result_str = i+"."+result_str;
					}
					protect (package_obj, unprotected_array);
					return result_str;
				}
			}
		}
		protect (package_obj, unprotected_array);
		return null;
	}
}
