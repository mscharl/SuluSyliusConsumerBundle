<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\SyliusConsumerBundle\Repository\Product;

use Doctrine\ORM\EntityRepository;
use Sulu\Bundle\SyliusConsumerBundle\Model\Product\ProductInformationAttributeValueInterface;
use Sulu\Bundle\SyliusConsumerBundle\Model\Product\ProductInformationAttributeValueRepositoryInterface;
use Sulu\Bundle\SyliusConsumerBundle\Model\Product\ProductInformationInterface;

class ProductInformationAttributeValueRepository extends EntityRepository implements ProductInformationAttributeValueRepositoryInterface
{
    public function create(
        ProductInformationInterface $productInformation,
        string $code,
        string $type
    ): ProductInformationAttributeValueInterface {
        $className = $this->getClassName();
        $productInformationAttributeValue = new $className($productInformation, $code, $type);
        $this->getEntityManager()->persist($productInformationAttributeValue);
        $productInformation->addAttributeValue($productInformationAttributeValue);

        return $productInformationAttributeValue;
    }

    public function getTypeByCodes(array $codes): array
    {
        $result = $this->createQueryBuilder('attribute_value')
            ->select('attribute_value.type AS type, attribute_value.code AS code')
            ->where('attribute_value.code IN(:codes)')
            ->setParameter('codes', $codes)
            ->distinct()
            ->getQuery()
            ->getArrayResult();

        $types = [];
        foreach ($result as $item) {
            $types[$item['code']] = $item['type'];
        }

        return $types;
    }

    public function remove(ProductInformationAttributeValueInterface $productInformationAttributeValue): void
    {
        $this->getEntityManager()->remove($productInformationAttributeValue);
    }
}
