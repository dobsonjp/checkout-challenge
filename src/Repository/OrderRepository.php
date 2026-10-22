<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Customer;
use App\Config\CustomerType;

class OrderRepository extends EntityRepository
{
	public function create(Customer $customer, bool $isGift, bool $isSample): Order
	{
		$order = new Order();
        $order->setCustomer($customer);
        $order->setCreatedAt(new \DateTimeImmutable());
        // CHK-102 - ensure gifts flagged as such
    	$order->setGift($isGift);
    	$order->setSample($isSample);

        return $order;
	}
}