function CustomGetTileUrl(point, zoom) {
	var adr = "http://takethegrt.ca/map/tiles/";
	var hasmap = false;

	switch (zoom)
	{
		case 12:
			if (point.x >= 1130 && point.x <= 1135 && point.y >= 1496 && point.y <= 1500)
			{
				hasmap = true;
			}
			break;
		case 13:
			if (point.x >= 2260 && point.x <= 2270 && point.y >= 2992 && point.y <= 2999)
			{
				hasmap = true;
			}
			break;
		case 14:
			if (point.x >= 4521 && point.x <= 4541 && point.y >= 5985 && point.y <= 6000)
			{
				hasmap = true;
			}
			break;
		case 15:
			if (point.x >= 9043 && point.x <= 9079 && point.y >= 11971 && point.y <= 11999)
			{
				hasmap = true;
			}
			break;
		case 16:
			if (point.x >= 18087 && point.x <= 18158 && point.y >= 23943 && point.y <= 23999)
			{
				hasmap = true;
			}
			break;
		default:
			break;
	}

	if (hasmap)
	{
		return adr + "Z" + zoom + "/" + point.y + "_" + point.x + ".png";
	}
	else
	{
		return;
	}
}
