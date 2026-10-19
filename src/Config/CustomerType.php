<?php
namespace App\Config;

enum CustomerType: string
{
	case REGULAR = 'regular';
	case VIP = 'vip';
	case STAFF = 'staff';
	case TRADE = 'trade';
	case GIFT = 'gift';
	case STUDENT = 'student';

	public function getDiscountPercentage(): float
	{
		return match($this) {
			self::STUDENT => 15,
			self::REGULAR,
			self::VIP,
			self::STAFF,
			self::TRADE,
			self::GIFT => 0,
		};
	}
}