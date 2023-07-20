<?php

namespace App\Form\Traits;

use App\Annotations\ListingAnnotation;
use App\Repository\ListingValueRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;

trait ListingValuesFormsTrait
{
    public function createListingValueBuilder(ListingValueRepository $rep, $uuidType)
    {

        return $rep->createQueryBuilder('lv')
                        ->leftJoin('lv.listingType', 'lt')
                        ->where('lt.uuid = \'' . $uuidType . '\'')
                        ->andWhere('lv.parent is null')
                        ->orderBy('lv.orderInList, lv.slug', 'ASC');
    }

    public function getUuidTypeListeFromAnnotation($className, $fieldName)
    {
        $className = 'App\\Entity\\' . $className;
        $obj = new $className();
        $reader = new AnnotationReader();
        $reflectionClass = new ReflectionClass($obj);
        $property = $reflectionClass->getProperty($fieldName);
        $myAnnotation = $reader->getPropertyAnnotation(
            $property,
            ListingAnnotation::class
        );

        return $myAnnotation->idListingUuid;
    }

}
