<?php


class Time {

	/**
	 * 将秒数转成%s天%时%分%秒
	 * @param int $second
	 * @return string
	 */
	public static function parseSecond($second) {
		$day = floor($second/86400);
		$second = $second%86400;
		
		$result = $day ? "{$day}天" : NULL;
		$result .= (int)gmdate('H') ? sprintf("%s时", (int)gmdate('H')) : NULL;
		$result .= (int)gmdate('i') ? sprintf("%s分", (int)gmdate('i')) : NULL;
		$result .= (int)gmdate('s') ? sprintf("%s秒", (int)gmdate('s')) : NULL;
		
		return $result;
	}
}