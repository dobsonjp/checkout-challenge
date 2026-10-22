<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;

class CheckoutService
{
	private Customer $customer;
	private Order $order;

    public const GIFT_WRAP_PRICE = 3.50;

	public function __construct(private array $orderData, private EntityManagerInterface $em)
	{
	}

	public function findOrCreateCustomer(): self
	{
		$customerRepo = $em->getRepository(Customer::class);
		$this->customer = $customerRepo->findOrCreate($this->orderData['email'], $this->orderData['customerType'] ?? 'regular');

		return $this;
	}

	public function createOrder(): self
	{
		$orderRepo = $em->getRepository(Order::class);
		$this->order = $orderRepo->create($this->customer, $this->orderData['gift'] ?? false, $this->orderData['sample'] ?? false);

		return $this;
	}

	public function fillOrder(): self
	{
		$orderItemRepo = $em->getRepository(OrderItem::class);

		foreach ($this->orderData['cart'] as $row) {
			$item = $orderItemRepo->create($row['sku'], (float) $row['price'], (int) $row['qty']);

			$this->order->addItem($item);
		}

		return $this;
	}

	public function setTotal(): self
	{
		// samples cost nowt
		if ($this->order->isSample()) {
			$order->setTotal(0);

			return $this;
		}

		$total = 0;
		$items = $this->order->getItems();
		$items->each(function (OrderItem $item) use ($total) {
			$total += ($item->getPrice() * $item->getQty());
		});

		// apply discount percentage
		$discountPercent = $this->customer->getType()->getDiscountPercent();
		$discount = ($discountPercent / 100) * $total;
		$total -= $discount;

		// apply any discount amounts
		$discountAmount = $this->customer->getType()->getDiscountAmount();
		$total -= $discountAmount;

		// if it's a gift, add that on
		if ($this->order->isGift()) {
			$total += self::GIFT_WRAP_PRICE;
		}

		$order->setTotal(round($total, 2));

		// CHK-104 - total can't be less than zero
		if ($total < 0) {
			$order->setTotal(0);
		}

		return $this;

	}

	public function setRecipientEmail(): self
	{
		$order->setReceipientEmail($this->orderData['email']);

		if ($this->order->isGift()) {
			$order->setRecipientEmail($this->orderData['recipientEmail']);
		}

		return $this;
	}

	public getCustomer(): Customer
	{
		return $this->customer;
	}

	public function getOrder(): Order
	{
		return $this->order;
	}
}