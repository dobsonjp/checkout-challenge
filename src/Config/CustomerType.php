<?php
namespace App\Config;

enum CustomerType: string
{
	case REGULAR = 'regular';
	case VIP = 'vip';
	case STAFF = 'staff';
	case TRADE = 'trade';
	case STUDENT = 'student'; // CHK-101 - add student customer type

	public function getDiscountPercentage(): int
	{
		return match($this) {
			self::STAFF => 50,
			self::VIP => 20,
			self::STUDENT => 15, // CHK-101 - add 15% discount for students
			self::TRADE => 10,
			self::REGULAR,
			default => 0,
		};
	}

	public function getDiscountAmount(): float
	{
		return match($this) {
			self::TRADE => 5.00,
			self::REGULAR,
			self::VIP,
			self::STAFF,
			self::STUDENT,
			default => 0,
		};
	}
}