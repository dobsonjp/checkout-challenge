<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Customer;
use App\Config\CustomerType;

class CustomerRepository extends EntityRepository
{
	public function findOrCreate(array $params): Customer
	{
		$customer = $this->findOneBy($params);

		if (is_null($customer)) {
			$customer = new Customer();
			$customer->setEmail($params['email']);
			$customer->setType($params['customerType'] ?? CustomerType::REGULAR);

			$customer->persist();
			$customer->flush();
		}

		return $customer;
	}
}