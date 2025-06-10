<?php declare(strict_types=1);

namespace App\Controller;


use App\Service\PriceService;
use App\Service\PurchaseService;
use App\Struct\CalcPriceStruct;
use App\Struct\OrderResponseStruct;
use App\Struct\PurchaseStruct;
use App\Util\ValidationListParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CheckoutController
 * @package App\Controller
 */
class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly PriceService $priceService,
        private readonly PurchaseService $purchaseService
    )
    {
    }

    #[Route('/calculate-price', name: 'checkout.calc.price', methods: ['POST'])]
    public function calcPrice(#[MapRequestPayload] CalcPriceStruct $data): JsonResponse
    {
        try {
            $price = $this->priceService->calculatePrice($data->productId, $data->taxNumber, $data->couponCode);
        } catch (\DomainException $e) {
            return $this->json(['errors' =>[$e->getMessage()]], 400);
        }

        return $this->json(['data' => ['price' => $price]]);
    }

    #[Route('/purchase', name: 'checkout.purchase', methods: ['POST'])]
    public function purchase(#[MapRequestPayload] PurchaseStruct $data): JsonResponse
    {
        try {
            $price = $this->priceService->calculatePrice($data->productId, $data->taxNumber, $data->couponCode);
            $order = $this->purchaseService->makePurchase($data->productId, $data->paymentMethod, $price);
        } catch (\DomainException $e) {
            return $this->json(['errors' => [$e->getMessage()]], 400);
        }

        return $this->json(['data' => new OrderResponseStruct($order)]);
    }
}
