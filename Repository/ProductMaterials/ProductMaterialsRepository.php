<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Product\Repository\ProductMaterials;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Products\Product\Entity\Event\ProductEvent;
use BaksDev\Products\Product\Entity\Material\ProductMaterial;
use BaksDev\Products\Product\Type\Event\ProductEventUid;
use BaksDev\Products\Product\Type\Material\MaterialUid;
use Generator;
use InvalidArgumentException;

final class ProductMaterialsRepository implements ProductMaterialsInterface
{
    private ProductEventUid|false $event = false;

    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    public function forEvent(ProductEvent|ProductEventUid|string $event): self
    {
        if(is_string($event))
        {
            $event = new ProductEventUid($event);
        }

        if($event instanceof ProductEvent)
        {
            $event = $event->getId();
        }

        $this->event = $event;

        return $this;
    }

    /**
     * Метод возвращает список идентификаторов материалов, из которых изготавливается продукция с коэффициентом расхода
     */
    public function findAll(): Generator|false
    {
        if(false === $this->event)
        {
            throw new InvalidArgumentException('Invalid Argument Event');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(ProductMaterial::class, 'material')
            ->where('material.event = :event')
            ->setParameter('event', $this->event, ProductEventUid::TYPE);

        $dbal
            ->select('material.material AS value')
            ->addSelect('material.ratio AS attr');

        return $dbal
            ->fetchAllHydrate(MaterialUid::class);
    }
}