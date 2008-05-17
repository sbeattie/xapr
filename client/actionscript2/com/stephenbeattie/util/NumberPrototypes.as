
class com.stephenbeattie.util.NumberPrototypes
{
	public static function force2Digits( num:Number ):String
	{
		if ( num < 10 ) return "0"+num.toString();
		return num.toString();
	}

	public static function force2DecimalPlaces( num:Number ):String
	{
		var numStr = num.toString()
		if ( numStr.indexOf(".") != -1 )
		{
			var numParts = numStr.split(".")
			if ( numParts[1].length > 2 )
			{
				numParts[1] = numParts[1].substr(0,2);
			}
			else if ( numParts[1].length < 2 )
			{
				numParts[1] += "0";
			}
			return numParts.join(".");
				
		}
		else
		{
			return num.toString() + ".00";
		}
	}
}