<?php declare(strict_types=1);

namespace App\Tests\Functional\Checkout;


use App\Entity\Order;
use App\Enum\OrderState;
use App\Service\PriceService;
use App\Service\PurchaseService;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CheckoutPurchaseTest
 * @package App\Tests\Functional\Checkout
 */
class CheckoutPurchaseTest extends WebTestCase
{
    private KernelBrowser $client;
    private MockObject $priceServiceMock;
    private MockObject $purchaseServiceMock;
    private string $uri;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = self::getContainer();

        $this->priceServiceMock = $this->createMock(PriceService::class);
        $this->purchaseServiceMock = $this->createMock(PurchaseService::class);

        $this->uri = '/purchase';

        $container->set(PriceService::class, $this->priceServiceMock);
        $container->set(PurchaseService::class, $this->purchaseServiceMock);
    }

    public function testPurchaseSuccess(): void
    {
        $productId = Uuid::uuid4()->toString();
        $taxNumber = 'DE123456789';
        $paymentMethod = 'paypal';
        $calculatedPrice = 119.0;

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getState')->willReturn(OrderState::Open);

        $this->priceServiceMock->expects(self::once())->method('calculatePrice')
            ->with($productId, $taxNumber)
            ->willReturn($calculatedPrice);

        $this->purchaseServiceMock->expects(self::once())->method('makePurchase')
            ->with($productId, $paymentMethod, $calculatedPrice)
            ->willReturn($orderMock);

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
                'paymentMethod' => $paymentMethod,
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
    }

    public function testPurchaseNotValidId(): void
    {
        $productId = 'randomString';
        $taxNumber = 'DE123456789';
        $paymentMethod = 'paypal';

        $this->priceServiceMock->expects(self::never())->method('calculatePrice');
        $this->purchaseServiceMock->expects(self::never())->method('makePurchase');

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
                'paymentMethod' => $paymentMethod,
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testPurchaseNotValidTaxNumber(): void
    {
        $productId = Uuid::uuid4()->toString();
        $taxNumber = 'DE1234567891';
        $paymentMethod = 'paypal';

        $this->priceServiceMock->expects(self::never())->method('calculatePrice');
        $this->purchaseServiceMock->expects(self::never())->method('makePurchase');

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
                'paymentMethod' => $paymentMethod,
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testPurchaseNotValidPaymentMethod(): void
    {
        $productId = Uuid::uuid4()->toString();
        $taxNumber = 'DE123456789';
        $paymentMethod = 'unsupported_method';

        $this->priceServiceMock->expects(self::never())->method('calculatePrice');
        $this->purchaseServiceMock->expects(self::never())->method('makePurchase');

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
                'paymentMethod' => $paymentMethod,
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
