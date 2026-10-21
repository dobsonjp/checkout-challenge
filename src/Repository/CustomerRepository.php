<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Customer;
use App\Config\CustomerType;

class CustomerRepository extends EntityRepository
{
	public function findOrCreate(string $email, string $customerType): Customer
	{
		$customer = $this->findOneBy($email);

		if (is_null($customer)) {
			$customer = new Customer();
			$customer->setEmail($email);
			$customer->setType($customerType);
		}

		return $customer;
	}
}