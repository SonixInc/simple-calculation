<?php declare(strict_types=1);

namespace App\Tests\Functional\Checkout;


use App\Service\PriceService;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CheckoutCalcPriceTest
 * @package App\Tests\Functional\Checkout
 */
class CheckoutCalcPriceTest extends WebTestCase
{
    private KernelBrowser $client;
    private MockObject $priceServiceMock;
    private string $uri;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = self::getContainer();

        $this->priceServiceMock = $this->createMock(PriceService::class);

        $this->uri = '/calculate-price';

        $container->set(PriceService::class, $this->priceServiceMock);
    }

    public function testCalcPriceSuccess(): void
    {
        $productId = Uuid::uuid4()->toString();
        $taxNumber = 'DE123456789';
        $calculatedPrice = 119.0;

        $this->priceServiceMock->expects(self::once())->method('calculatePrice')
            ->with($productId, $taxNumber)
            ->willReturn($calculatedPrice);

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
            ])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('price', $data['data']);
        $this->assertEquals($calculatedPrice, $data['data']['price']);
    }

    public function testCalcPriceNotValidId(): void
    {
        $productId = 'randomString';
        $taxNumber = 'DE123456789';

        $this->priceServiceMock->expects(self::never())->method('calculatePrice')
            ->with($productId, $taxNumber);

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCalcPriceNotValidTaxNumber(): void
    {
        $productId = Uuid::uuid4()->toString();
        $taxNumber = 'DE12345678911';

        $this->priceServiceMock->expects(self::never())->method('calculatePrice')
            ->with($productId, $taxNumber);

        $this->client->request('POST', $this->uri, [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'productId' => $productId,
                'taxNumber' => $taxNumber,
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
