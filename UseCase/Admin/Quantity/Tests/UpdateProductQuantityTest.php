<?php
/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Products\Product\UseCase\Admin\Quantity\Tests;

use BaksDev\Products\Product\Repository\ProductDetail\ProductDetailByInvariableInterface;
use BaksDev\Products\Product\Repository\ProductDetail\ProductDetailByInvariableResult;
use BaksDev\Products\Product\Type\Invariable\ProductInvariableUid;
use BaksDev\Products\Product\UseCase\Admin\Quantity\UpdateProductQuantityDTO;
use BaksDev\Products\Product\UseCase\Admin\Quantity\UpdateProductQuantityHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use BaksDev\Products\Product\Entity\Price\ProductPrice;

/**
 * @group products-product
 * @group products-product-usecase
 *
 * @depends BaksDev\Products\Product\UseCase\Admin\NewEdit\Tests\ProductsProductNewAdminUseCaseTest::class
 * @depends BaksDev\Products\Product\UseCase\Admin\Invariable\Tests\ProductInvariableAdminUseCaseTest::class
 */
#[When(env: 'test')]
final class UpdateProductQuantityTest extends KernelTestCase
{
    public function testUseCase()
    {
        /** Получение данных о тестируемом товаре */
        $quantity = rand(0, 10);
        $reserve = rand(0, 10);

        /** @var ProductDetailByInvariableInterface $productDetailByInvariable */
        $productDetailByInvariable = self::getContainer()->get(ProductDetailByInvariableInterface::class);
        /** @var ProductDetailByInvariableResult $result */
        $result = $productDetailByInvariable->invariable(ProductInvariableUid::TEST)->find();

        $event = $result->getProductEvent();

        /** @var UpdateProductQuantityHandler$handler */
        $handler = self::getContainer()->get(UpdateProductQuantityHandler::class);
        $dto = new UpdateProductQuantityDTO(
            $event,
            $quantity,
            $reserve,
        );

        $result = $handler->handle($dto);

        self::assertTrue($result instanceof ProductPrice);

        self::assertTrue($result->getQuantity() === $quantity);
        self::assertTrue($result->getReserve() === $reserve);
    }
}