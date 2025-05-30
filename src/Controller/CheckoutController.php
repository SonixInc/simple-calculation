<?php declare(strict_types=1);

namespace App\Controller;


use App\Service\PriceService;
use App\Service\PurchaseService;
use App\Struct\CalcPriceStruct;
use App\Struct\PurchaseStruct;
use App\Util\ValidationListParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CheckoutController
 * @package App\Controller
 */
class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly PriceService $priceService,
        private readonly PurchaseService $purchaseService
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/calculate-price', name: 'checkout.calc.price', methods: ['POST'])]
    public function calcPrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $struct = new CalcPriceStruct();
        $struct->productId = $data['productId'] ?? '';
        $struct->taxNumber = $data['taxNumber'] ?? '';
        $struct->couponCode = $data['couponCode'] ?? '';

        $errors = $this->validator->validate($struct);
        if (count($errors) > 0) {
            return $this->json(['errors' => ValidationListParser::toArray($errors)], 400);
        }

        try {
            $price = $this->priceService->calculatePrice($struct->productId, $struct->taxNumber, $struct->couponCode);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        return $this->json([
            'data' => [
                'price' => $price
            ]
        ]);
    }

    #[Route('/purchase', name: 'checkout.purchase', methods: ['POST'])]
    public function purchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $struct = new PurchaseStruct();
        $struct->productId = $data['productId'] ?? '';
        $struct->taxNumber = $data['taxNumber'] ?? '';
        $struct->couponCode = $data['couponCode'] ?? '';
        $struct->paymentMethod = $data['paymentMethod'] ?? '';

        $errors = $this->validator->validate($struct);
        if (count($errors) > 0) {
            return $this->json(['errors' => ValidationListParser::toArray($errors)], 400);
        }

        try {
            $price = $this->priceService->calculatePrice($struct->productId, $struct->taxNumber, $struct->couponCode);
            $order = $this->purchaseService->makePurchase($struct->productId, $struct->paymentMethod, $price);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        return $this->json([
            'data' => [
                'order' => [
                    'id' => $order->getId(),
                    'state' => $order->getState(),
                    'price' => $order->getPrice(),
                    'createdAt' => $order->getCreatedAt(),
                ]
            ]
        ]);
    }
}
