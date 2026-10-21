<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Order;
use App\Entity\OrderItem;

class OrderItemRepository extends EntityRepository
{
	public function create(string $sku, float $price, int $qty): Order
	{
		$item = new OrderItem();
        $item->setSku($sku);
        $item->setPrice($price);
        $item->setQty($qty);

        return $item;
	}
}