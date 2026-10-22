<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Service\CheckoutService;

class CheckoutController extends AbstractController
{
    private CustomerRepository $customerRepo;
    private OrderRepository $orderRepo;

    public const GIFT_WRAP_PRICE = 3.50;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->customerRepo = $entityManager->getRepository(Customer::class);
        $this->orderRepo = $entityManager->getRepository(Order::class);
    }

    #[Route('/checkout', methods: ['POST'])]
    public function checkout(Request $request, CheckoutService $service, EntityManagerInterface $em, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['cart'])) {
            return new JsonResponse(['error' => 'Empty cart'], 500);
        }

        $order = $service
            ->findOrCreateCustomer()
            ->createOrder()
            ->fillOrder()
            ->setTotal()
            ->setRecipientEmail()
            ->getOrder();

        $em->persist($order);
        $em->flush();

        if ($order->isSample()) {
            $body = 'Your free sample of ' . $data['cart'][0]['sku'] . ' is on its way!';
        } else {
            $body = 'Thanks for your order! Total: £' . $order->getTotal();
        }
        $mailer->send(
            (new Email())
                ->from('shop@example.com')
                ->to($order->getRecipientEmail())
                ->subject('Your order')
                ->text($body)
        );

        $logDir = $this->getParameter('kernel.logs_dir');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logDir . '/orders.log', date('c') . ' order placed for ' . $data['email'] . "\n", FILE_APPEND);

        return new JsonResponse(['id' => $order->getId(), 'total' => $order->getTotal()]);
    }
}
