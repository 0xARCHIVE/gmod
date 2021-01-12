<?php
/**
 * Class SteamID
 * Simple PHP SteamID converter class that converts between SteamID's and Steam CommunityID's
 *
 * @author Callum Thomson <callum@callumthomson.co.uk>
 * @version 3.0.0
 */
class SteamIDConverter {
    /**
	 * Make a decision based on the input which conversion function should be run and then call it with the data.
	 *
     * @param $id SteamID or CommunityID
	 * @return string Converted ID
     */
    public static function convert($id)
	{
		if(strpos($id, 'STEAM')===false)
		{ // It's a CommunityID
			return self::getIDFromCommunity($id);
		}
		else
		{ // It's a SteamID
			return self::getCommunityFromID($id);
		}
	}

    /**
	 * Convert SteamID into a CommunityID
	 *
     * @param $id SteamID
     * @return string CommunityID
     */
    private  static function getCommunityFromID($id)
	{
		$accountarray	=	explode(":", $id);
		$idnum			=	$accountarray[1];
		$accountnum		=	$accountarray[2];
		$constant		=	'76561197960265728';
		$number			=	bcadd(bcmul($accountnum, 2), bcadd($idnum, $constant)); // ($accountnum *2) + ($idnum + $constant)
		return $number;
	}

    /**
	 * Convert CommunityID to SteamID
	 *
     * @param $id CommunityID
     * @return string SteamID
     */
    private static function getIDFromCommunity($id)
	{
		$idnum		=	'0';
		$accnum		=	'0';
		$constant	=	'76561197960265728';
		if(bcmod($id, '2')==0)
		{
			$idnum	=	'0';
			$temp	=	bcsub($id, $constant);
		}
		else
		{
			$idnum	=	'1';
			$temp	=	bcsub($id,bcadd($constant, '1'));
		}
		$accnum	=	bcdiv($temp, '2');
		return 		"STEAM_0:".$idnum.":".number_format($accnum, 0, '', '');
	}
}
